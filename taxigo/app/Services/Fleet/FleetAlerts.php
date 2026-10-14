<?php

namespace App\Services\Fleet;

use App\Enums\Type;
use App\Models\BookingDetail;
use App\Models\FleetDeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

/**
 * Push alerts to the Seema Cabs Fleet app: high priority, on the app's
 * "New bookings" channel (own loud sound). Only phones whose user can see
 * the booking get it; tokens Firebase reports as gone are removed.
 */
class FleetAlerts
{
    /** Android notification channel created by the app (fleet-app/www/fleet-bridge.js). */
    public const CHANNEL = 'new_bookings';
    public const SOUND = 'new_booking';

    public static function newBooking(BookingDetail $booking): int
    {
        $b = FleetBookings::present($booking->fresh());

        return self::send($booking, 'New booking ' . $b['reference'],
            "{$b['from']} → {$b['to']}\n{$b['pickup_date']}, {$b['pickup_time']} · {$b['cab']} · ₹" . number_format($b['total']), 'new_booking');
    }

    public static function cancelled(BookingDetail $booking): int
    {
        $b = FleetBookings::present($booking->fresh());

        return self::send($booking, 'Booking cancelled ' . $b['reference'],
            "{$b['from']} → {$b['to']}\n{$b['pickup_date']}, {$b['pickup_time']}", 'cancelled');
    }

    private static function send(BookingDetail $booking, string $title, string $body, string $kind): int
    {
        $tokens = self::tokensFor($booking);
        if (!$tokens) {
            return 0;
        }

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData([
                'type' => $kind,
                'booking_id' => (string) $booking->id,
                'reference' => (string) $booking->booking_id,
                'url' => '/fleet/bookings/' . $booking->id,
            ])
            ->withAndroidConfig(AndroidConfig::fromArray([
                'priority' => 'high',
                'ttl' => '86400s',
                'notification' => [
                    'channel_id' => self::CHANNEL,
                    'sound' => self::SOUND,
                    'notification_priority' => 'PRIORITY_MAX',
                    'visibility' => 'PUBLIC',
                    'default_vibrate_timings' => true,
                    'tag' => $kind . '-' . $booking->id,
                ],
            ]));

        try {
            $report = self::messaging()->sendMulticast($message, $tokens);
            $gone = array_merge($report->invalidTokens(), $report->unknownTokens());
            if ($gone) {
                FleetDeviceToken::whereIn('token', $gone)->delete();
            }

            return $report->successes()->count();
        } catch (\Throwable $e) {
            Log::warning("Fleet alert for {$booking->booking_id} failed: " . $e->getMessage());

            return 0;
        }
    }

    /** Tokens of admins and of fleet operators who can see this booking. */
    private static function tokensFor(BookingDetail $booking): array
    {
        $tokens = [];
        foreach (FleetDeviceToken::get()->groupBy('user_id') as $userId => $rows) {
            $user = User::find($userId);
            if (!$user || !in_array((int) $user->type, [Type::ADMIN, Type::FleetOperator], true)) {
                continue;
            }
            if ((int) $user->type === Type::ADMIN || FleetBookings::query($user)->whereKey($booking->id)->exists()) {
                $tokens = array_merge($tokens, $rows->pluck('token')->all());
            }
        }

        return array_values(array_unique($tokens));
    }

    private static function messaging()
    {
        return (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'))->createMessaging();
    }
}
