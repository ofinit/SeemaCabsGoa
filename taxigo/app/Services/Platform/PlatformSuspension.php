<?php

namespace App\Services\Platform;

use App\Enums\BookingEnum;
use App\Models\BookingDetail;
use App\Models\Environment;
use Illuminate\Support\Facades\Log;

/**
 * What still works while the service is suspended for unpaid OfinIT invoices:
 * OfinIT's admin login, Seema's fleet operator login (Billing page only), SOS,
 * rides already under way (their trip screens), payment webhooks. Everything
 * else — marketing pages (MarketingPageController), the PWA, the customer
 * app — shows "temporarily unavailable". Future bookings are not served.
 */
class PlatformSuspension
{
    /** A ride is "under way" from 1 hour before pickup until 12 hours after. */
    private const BEFORE_PICKUP_MINUTES = 60;
    private const AFTER_PICKUP_HOURS = 12;

    /** Is this booking (id or booking id) of this customer a ride under way right now? */
    public static function rideUnderWay(int|string|null $booking, ?int $customerId): bool
    {
        if (!$booking || !$customerId) {
            return false;
        }
        $ride = self::underWay()->where('customer_id', $customerId)
            ->where(fn ($q) => $q->where('id', is_numeric($booking) ? (int) $booking : 0)->orWhere('booking_id', (string) $booking))
            ->first();

        return (bool) $ride;
    }

    /** The customer's ride under way, if any (for the "open your trip" link). */
    public static function currentRide(?int $customerId): ?BookingDetail
    {
        return $customerId ? self::underWay()->where('customer_id', $customerId)->orderBy('pickup_date')->first() : null;
    }

    private static function underWay()
    {
        $now = now('Asia/Kolkata');

        return BookingDetail::query()
            ->whereNotIn('status', [BookingEnum::CANCEL, BookingEnum::COMPLETE, BookingEnum::NO_SHOW])
            ->whereNotNull('assigned_driver_id')
            ->whereRaw('TIMESTAMP(pickup_date, pickup_time) <= ?', [$now->copy()->addMinutes(self::BEFORE_PICKUP_MINUTES)->format('Y-m-d H:i:s')])
            ->whereRaw('TIMESTAMP(pickup_date, pickup_time) >= ?', [$now->copy()->subHours(self::AFTER_PICKUP_HOURS)->format('Y-m-d H:i:s')]);
    }

    /** SOS phone numbers shown on the "unavailable" page. */
    public static function sosNumbers(): array
    {
        $raw = Environment::whereIn('title', ['sos_number', 'sosnumber', 'sos_numbers', 'police_number', 'ambulance_number'])->pluck('value')->all();
        $numbers = [];
        foreach ($raw as $value) {
            foreach (preg_split('/[,\s]+/', (string) $value) as $n) {
                if (preg_match('/^\+?\d{3,15}$/', $n)) {
                    $numbers[] = $n;
                }
            }
        }

        return array_values(array_unique($numbers ?: ['112']));
    }
}
