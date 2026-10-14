<?php

namespace App\Services\Fleet;

use App\Enums\BookingEnum;
use App\Enums\Type;
use App\Models\BookingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Paid bookings as the fleet operator app shows them. A fleet operator sees
 * rides whose assigned driver drives one of its cabs, plus rides with no
 * driver yet while it is the only fleet operator; the admin sees all.
 * (booking_details.cab_id is the cab *rate*, not a vehicle.)
 */
class FleetBookings
{
    public const TABS = [
        'upcoming' => 'Upcoming',
        'today' => 'Today',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'all' => 'All',
    ];

    public static function query(User $user): Builder
    {
        $query = BookingDetail::query()
            ->without(['getSosDetails', 'user', 'cabRate'])
            ->where('payment_status', Type::ACTIVE);

        if ((int) $user->type !== Type::ADMIN) {
            $cabIds = DB::table('cabs')->where('fleet_operator_id', $user->id)->whereNull('deleted_at')->pluck('id');
            $driverIds = DB::table('drivers')->whereIn('cab_id', $cabIds)->pluck('id');
            $sole = self::soleOperator($user->id);
            $query->where(function ($q) use ($driverIds, $sole) {
                $q->whereIn('assigned_driver_id', $driverIds);
                if ($sole) {
                    $q->orWhereNull('assigned_driver_id');
                }
            });
        }

        return $query;
    }

    /** Bookings for a tab, newest pickup first for history, soonest first for what's coming. */
    public static function forTab(User $user, string $tab, int $limit = 100)
    {
        $today = now('Asia/Kolkata')->toDateString();
        $query = self::query($user)->with(['assignDriver', 'getPickupFrom', 'getDropTo', 'user']);

        match ($tab) {
            'today' => $query->whereDate('pickup_date', $today)->orderBy('pickup_time'),
            'completed' => $query->where('status', BookingEnum::COMPLETE)->orderByDesc('pickup_date')->orderByDesc('pickup_time'),
            'cancelled' => $query->whereIn('status', [BookingEnum::CANCEL, BookingEnum::NO_SHOW])->orderByDesc('updated_at'),
            'all' => $query->orderByDesc('id'),
            default => $query->whereNotIn('status', [BookingEnum::CANCEL, BookingEnum::COMPLETE, BookingEnum::NO_SHOW])
                ->whereDate('pickup_date', '>=', $today)->orderBy('pickup_date')->orderBy('pickup_time'),
        };

        return $query->limit($limit)->get()->map(fn (BookingDetail $b) => self::present($b));
    }

    /** Counts for the tab badges. */
    public static function counts(User $user): array
    {
        $today = now('Asia/Kolkata')->toDateString();

        return [
            'upcoming' => self::query($user)->whereNotIn('status', [BookingEnum::CANCEL, BookingEnum::COMPLETE, BookingEnum::NO_SHOW])
                ->whereDate('pickup_date', '>=', $today)->count(),
            'today' => self::query($user)->whereDate('pickup_date', $today)->count(),
        ];
    }

    public static function find(User $user, string $booking): ?array
    {
        $b = self::query($user)->with(['assignDriver', 'getPickupFrom', 'getDropTo', 'user'])
            ->where(fn ($q) => $q->where('id', ctype_digit($booking) ? (int) $booking : 0)->orWhere('booking_id', $booking))
            ->first();

        return $b ? self::present($b, true) : null;
    }

    /** Everything the app shows about one booking. */
    public static function present(BookingDetail $b, bool $full = false): array
    {
        [$from, $to] = self::places($b);
        $status = self::status($b);
        $driver = $b->assignDriver;
        $cabNumber = $driver ? DB::table('cabs')->where('id', $driver->cab_id)->value('number') : null;
        $total = (float) $b->total_payment + ((int) $b->pricing_version < 2 && abs((float) $b->total_payment - (float) $b->base_fare) < 1 ? (float) $b->tax_amount : 0);
        $advance = (float) $b->part_payment;

        $row = [
            'id' => $b->id,
            'reference' => $b->booking_id,
            'status' => $status,
            'status_label' => self::STATUS_LABELS[$status],
            'trip' => self::tripLabel($b),
            'from' => $from,
            'to' => $to,
            'pickup_date' => $b->pickup_date ? Carbon::parse($b->pickup_date)->format('d M Y') : '',
            'pickup_day' => $b->pickup_date ? Carbon::parse($b->pickup_date)->format('D') : '',
            'pickup_time' => $b->pickup_time ? Carbon::parse($b->pickup_time)->format('g:i A') : '',
            'cab' => self::cabLabel($b),
            'customer' => $b->user?->name ?? 'Customer',
            'driver' => $driver?->name ?: $driver?->driver_name,
            'total' => $total,
            'advance' => $advance,
            'balance' => max(0, round($total - $advance, 2)),
        ];
        if ($full) {
            $row += [
                'customer_phone' => $b->user?->phone_number,
                'pickup_address' => $b->pickup_address,
                'drop_address' => $b->drop_of_address,
                'driver_phone' => $driver?->mobile ?: $driver?->driver_mobile,
                'cab_number' => $cabNumber,
                'refund' => (float) $b->refund,
                'trip_otp' => $b->trip_otp,
                'booked_at' => $b->created_at ? Carbon::parse($b->created_at)->timezone('Asia/Kolkata')->format('d M Y, g:i A') : '',
                'package' => $b->sight_seeing_package_id ? optional($b->sightSeeingPackage)->title : null,
            ];
        }

        return $row;
    }

    public const STATUS_LABELS = [
        'confirmed' => 'Confirmed',
        'assigned' => 'Driver assigned',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No-show',
    ];

    public static function status(BookingDetail $b): string
    {
        return match ((int) $b->status) {
            BookingEnum::COMPLETE => 'completed',
            BookingEnum::CANCEL => 'cancelled',
            BookingEnum::NO_SHOW => 'no_show',
            default => $b->assigned_driver_id ? 'assigned' : 'confirmed',
        };
    }

    /** [pick-up, drop-off] as people know them (airport names, city, address). */
    public static function places(BookingDetail $b): array
    {
        $airport = fn ($id) => match ((int) $id) {
            Type::AIRPORT_ONE_ID => Type::AIRPORT_ONE,
            Type::AIRPORT_TWO_ID => Type::AIRPORT_TWO,
            default => null,
        };
        $isPickup = (int) $b->air_port_drop === Type::AIRPORT_PICKUP;
        $isDrop = (int) $b->air_port_drop === Type::AIRPORT_DROP;
        $from = ($isPickup ? $airport($b->pickup_from) : null) ?? ($b->getPickupFrom?->name ?: $b->pickup_address ?: '—');
        $to = ($isDrop ? $airport($b->drop_to) : null) ?? ($b->getDropTo?->name ?: $b->drop_of_address ?: '—');
        if ($b->sight_seeing_package_id && $to === '—') {
            $to = optional($b->sightSeeingPackage)->title ?? 'Sightseeing';
        }

        return [$from, $to];
    }

    public static function tripLabel(BookingDetail $b): string
    {
        if ($b->sight_seeing_package_id) {
            return 'Sightseeing';
        }

        return match (true) {
            $b->trip_type === 'Airport Pickup', $b->trip_type === 'Airport Transfer' && (int) $b->air_port_drop === Type::AIRPORT_PICKUP => 'Airport pickup',
            $b->trip_type === 'Airport Drop', $b->trip_type === 'Airport Transfer' && (int) $b->air_port_drop === Type::AIRPORT_DROP => 'Airport drop',
            default => 'In-city ride',
        };
    }

    public static function cabLabel(BookingDetail $b): string
    {
        return match ((int) $b->cab_type) {
            Type::CAB_SEDAN_ID => Type::CAB_SEDAN,
            Type::CAB_SUV_ID => Type::CAB_SUV,
            Type::CAB_HATCHBACK_ID => Type::CAB_HATCHBACK,
            default => (string) ($b->cab_type ?: 'Cab'),
        };
    }

    /** Is this the only fleet operator with cabs? */
    private static function soleOperator(int $userId): bool
    {
        $operators = DB::table('users as u')->join('cabs as c', 'c.fleet_operator_id', '=', 'u.id')
            ->where('u.type', Type::FleetOperator)->whereNull('c.deleted_at')->whereNull('u.deleted_at')
            ->distinct()->pluck('u.id');

        return $operators->count() === 1 && (int) $operators->first() === $userId;
    }
}
