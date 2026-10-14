<?php

namespace App\Services\Fleet;

use App\Enums\BookingEnum;
use App\Enums\Type;
use App\Http\Controllers\Admin\RazorpayController;
use App\Http\Controllers\Admin\TripManagementController;
use App\Models\BookingDetail;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Trip management from the fleet app. Completing, cancelling, refunding and
 * no-shows go through the admin Trip Management code (same invoices,
 * notifications and refund rules); driver assignment is here so it can be
 * limited to the operator's own drivers.
 */
class FleetTrips
{
    /** Drivers this user may assign: the operator's cabs (admin: all), same cab type first. */
    public static function drivers(User $user, ?BookingDetail $booking = null): Collection
    {
        $query = DB::table('drivers as d')->join('cabs as c', 'c.id', '=', 'd.cab_id')
            ->whereNull('d.deleted_at')->whereNull('c.deleted_at')
            ->select('d.id', 'd.name', 'd.mobile', 'c.number as cab_number', 'c.type as cab_type');
        if ((int) $user->type !== Type::ADMIN) {
            $query->where('c.fleet_operator_id', $user->id);
        }
        $want = $booking ? (int) $booking->cab_type : 0;

        return $query->get()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name ?: 'Driver #' . $d->id,
            'phone' => $d->mobile,
            'cab_number' => $d->cab_number,
            'cab' => match ((int) $d->cab_type) {
                Type::CAB_SEDAN_ID => Type::CAB_SEDAN,
                Type::CAB_SUV_ID => Type::CAB_SUV,
                Type::CAB_HATCHBACK_ID => Type::CAB_HATCHBACK,
                default => 'Cab',
            },
            'matches' => $want && (int) $d->cab_type === $want,
        ])->sortBy([['matches', 'desc'], ['name', 'asc']])->values();
    }

    public static function assign(User $user, BookingDetail $booking, int $driverId): void
    {
        self::mustBeOpen($booking);
        if (!self::drivers($user)->contains('id', $driverId)) {
            throw new RuntimeException('That driver is not on your fleet.');
        }
        $changed = (int) $booking->assigned_driver_id !== $driverId;
        $booking->assigned_driver_id = $driverId;
        $booking->status = Type::PAID;
        $booking->save();

        if ($changed) {
            self::notifyCustomerAssigned($booking->fresh());
        }
    }

    public static function complete(BookingDetail $booking): string
    {
        self::mustBeOpen($booking);
        if (!$booking->assigned_driver_id) {
            throw new RuntimeException('Assign a driver before completing the trip.');
        }

        return self::admin(fn () => app(TripManagementController::class)->cancelBooking($booking->id, 'complete'));
    }

    public static function cancel(BookingDetail $booking, bool $refund): string
    {
        self::mustBeOpen($booking);
        $message = self::admin(fn () => app(TripManagementController::class)->cancelBooking($booking->id, 'cancel'));
        if ($refund) {
            $message .= ' ' . self::refund($booking->fresh());
        }

        return trim($message);
    }

    /** Refund the advance (admin rule: cancelled bookings within the refund window). */
    public static function refund(BookingDetail $booking): string
    {
        if ((int) $booking->status !== BookingEnum::CANCEL) {
            throw new RuntimeException('Only cancelled bookings can be refunded.');
        }
        if ((float) $booking->refund > 0) {
            throw new RuntimeException('This booking has already been refunded.');
        }

        return self::admin(fn () => app(RazorpayController::class)->refundAmount(new Request(['id' => $booking->id])), 'message');
    }

    public static function noShow(BookingDetail $booking, string $reason): string
    {
        return self::admin(fn () => app(TripManagementController::class)->markNoShow(new Request(['reason' => $reason]), $booking->id));
    }

    public static function undoNoShow(BookingDetail $booking): string
    {
        return self::admin(fn () => app(TripManagementController::class)->undoNoShow($booking->id));
    }

    /** Trip details for the driver (WhatsApp / copy). No start OTP: the customer gives it at pickup. */
    public static function driverMessage(array $b): string
    {
        $lines = [
            '*Seema Cabs trip: ' . $b['reference'] . '*',
            '',
            '*' . $b['trip'] . '*' . ($b['package'] ? ' · ' . $b['package'] : ''),
            '*Pick-up:* ' . $b['pickup_time'] . ', ' . $b['pickup_day'] . ' ' . $b['pickup_date'],
            '*From:* ' . $b['from'] . (self::extra($b['pickup_address'], $b['from'])),
            '*To:* ' . $b['to'] . (self::extra($b['drop_address'], $b['to'])),
            '',
            '*Customer:* ' . $b['customer'],
        ];
        if ($b['customer_phone']) {
            $lines[] = '*Phone:* ' . $b['customer_phone'];
        }
        $lines[] = '*Cab:* ' . $b['cab'] . ($b['cab_number'] ? ' · ' . $b['cab_number'] : '');
        $lines[] = '';
        $lines[] = '*Collect from customer:* ₹' . number_format($b['balance'], 2);
        $lines[] = 'Ask the customer for the start OTP at pick-up.';

        return implode("\n", $lines);
    }

    private static function extra(?string $address, string $place): string
    {
        return $address && $address !== $place ? ' (' . $address . ')' : '';
    }

    private static function mustBeOpen(BookingDetail $booking): void
    {
        if (in_array((int) $booking->status, [BookingEnum::COMPLETE, BookingEnum::CANCEL, BookingEnum::NO_SHOW], true)) {
            throw new RuntimeException('This booking is already ' . FleetBookings::STATUS_LABELS[FleetBookings::status($booking)] . '.');
        }
    }

    /** Run an admin Trip Management action and turn its JSON answer into a message or an error. */
    private static function admin(callable $action, string $key = 'Message'): string
    {
        $response = $action();
        $data = method_exists($response, 'getData') ? (array) $response->getData(true) : [];
        $message = (string) ($data[$key] ?? $data['message'] ?? $data['Message'] ?? 'Done.');
        if (array_key_exists('status', $data) && !$data['status']) {
            throw new RuntimeException($message);
        }

        return $message;
    }

    private static function notifyCustomerAssigned(BookingDetail $booking): void
    {
        try {
            $driver = DB::table('drivers')->where('id', $booking->assigned_driver_id)->first();
            $cab = $driver ? DB::table('cabs')->where('id', $driver->cab_id)->first() : null;
            $name = $driver?->name ?: 'your driver';
            $date = $booking->pickup_date ? Carbon::parse($booking->getRawOriginal('pickup_date'))->format('d M, Y') : '';
            $time = $booking->pickup_time ? Carbon::parse($booking->pickup_time)->format('g:i A') : '';
            $title = 'Your Ride is Confirmed!';
            $body = "Your ride {$booking->booking_id} on {$date} at {$time} is assigned to {$name}."
                . ($cab ? "\n\nCab: {$cab->number}." : '') . " OTP: {$booking->trip_otp} (share only with the driver at pick-up).";
            $data = ['booking_id' => $booking->booking_id];
            $notification = new NotificationService();
            $notification->storeUserNotification($booking->customer_id, $title, $body, $data, '');
            if ($token = UserFcmToken::where('user_id', $booking->customer_id)->value('token')) {
                $notification->sendUserNotification($token, $title, $body, $data, $booking->customer_id);
            }
        } catch (\Throwable $e) {
            Log::warning("Driver-assigned notification for {$booking->booking_id} failed: " . $e->getMessage());
        }
    }
}
