<?php

namespace App\Services;

use App\Enums\BookingEnum;
use App\Enums\Type;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The admin and fleet-operator dashboard figures, from paid bookings only
 * (booking_details.payment_status = ACTIVE), split by what happened to the
 * ride:
 *
 *  - completed  (status COMPLETE): the revenue figures and trip-type cards
 *  - upcoming   (not completed / cancelled / no-show yet): shown separately
 *  - cancelled  (CANCEL / NO_SHOW): advance kept vs refunded, shown separately
 *
 * Per booking (all maths in paise):
 *  - price  = what the customer is charged for the ride. Pricing v2+ stores it
 *             in total_payment. Many v1 rows saved total_payment WITHOUT the
 *             fare markup (total = base, markup in tax_amount) although the
 *             customer paid it, so for those the markup is added back.
 *  - online = gateway payment records (payments.amount), else part_payment
 *  - GST    = tax_amount for v2+ only (v1 tax_amount is the old markup)
 *  - OfinIT fee = platform_fee_amount (+ GST on it) for v2+; v1 the
 *             settlement's company_commission. The v1 settlement's
 *             fleet_operator_commission was saved at 10× the fare and is
 *             never used.
 *  - Seema Holidays' earnings = price − ride GST − OfinIT fee (incl. its GST)
 *             − refunds.
 *
 * A fleet operator sees the rides whose assigned driver drives one of its
 * cabs (booking_details.cab_id is the cab *rate* row, not a vehicle); while it
 * is the only fleet operator it also sees rides with no driver assigned.
 */
class FinancialSummaryService
{
    public static function compute(?int $fleetOperatorId = null): array
    {
        $bookings = self::bookings($fleetOperatorId);
        $payments = self::payments($bookings->pluck('id'));

        $sum = array_fill_keys(['gross', 'base', 'surge', 'markup', 'gst', 'online', 'cash', 'ofinit_fee', 'ofinit_fee_gst', 'seema', 'refunded'], 0);
        $trip = array_fill_keys(['airport_pickup', 'airport_drop', 'in_city', 'sightseeing'], ['amount' => 0, 'count' => 0]);
        $completed = $upcoming = $upcomingValue = $upcomingAdvance = 0;
        $cancelled = ['count' => 0, 'kept_count' => 0, 'kept' => 0, 'refunded_count' => 0, 'refunded' => 0];

        foreach ($bookings as $b) {
            $pay = $payments[$b->id] ?? null;
            $price = self::price($b);
            $online = $pay ? $pay['amount'] : self::paise($b->part_payment);
            $refund = min($online, self::paise($b->refund));
            $status = (int) $b->status;
            // Old refund code stored 4 (= COMPLETE) on refunded bookings; a fully
            // refunded ride that never had a driver was cancelled.
            if ($status === BookingEnum::COMPLETE && $online > 0 && $refund >= $online && $b->assigned_driver_id === null) {
                $status = BookingEnum::CANCEL;
            }

            if (in_array($status, [BookingEnum::CANCEL, BookingEnum::NO_SHOW], true)) {
                $cancelled['count']++;
                if ($refund > 0) {
                    $cancelled['refunded_count']++;
                    $cancelled['refunded'] += $refund;
                }
                if ($online - $refund > 0) {
                    $cancelled['kept_count']++;
                    $cancelled['kept'] += $online - $refund;
                }
                continue;
            }
            if ($status !== BookingEnum::COMPLETE) {
                $upcoming++;
                $upcomingValue += $price;
                $upcomingAdvance += $online;
                continue;
            }

            $v2 = (int) $b->pricing_version >= 2;
            $gst = $v2 ? self::paise($b->tax_amount) : 0;
            $markup = self::paise($v2 ? $b->markup_amount : $b->tax_amount);
            $surge = self::paise($b->surge_price);
            [$fee, $feeGst] = self::ofinitFee($b, $pay);
            $gross = $price - $refund;

            $completed++;
            $sum['gross'] += $gross;
            $sum['base'] += max(0, $price - $gst - $markup - $surge);
            $sum['surge'] += $surge;
            $sum['markup'] += $markup;
            $sum['gst'] += $gst;
            $sum['online'] += $online - $refund;
            $sum['cash'] += max(0, $price - $online);
            $sum['ofinit_fee'] += $fee;
            $sum['ofinit_fee_gst'] += $feeGst;
            $sum['seema'] += $gross - $gst - $fee - $feeGst;
            $sum['refunded'] += $refund;

            $bucket = self::tripType($b);
            $trip[$bucket]['amount'] += $gross;
            $trip[$bucket]['count']++;
        }

        $r = fn (int $paise) => round($paise / 100, 2);

        return [
            'completed_rides' => $completed,
            'gross_amount' => $r($sum['gross']),
            'base_fare' => $r($sum['base']),
            'surge_pricing' => $r($sum['surge']),
            'fare_markup' => $r($sum['markup']),
            'gst_collected' => $r($sum['gst']),
            'collected_online' => $r($sum['online']),
            'cash_to_drivers' => $r($sum['cash']),
            'ofinit_fee' => $r($sum['ofinit_fee']),
            'ofinit_fee_gst' => $r($sum['ofinit_fee_gst']),
            'seema_earnings' => $r($sum['seema']),
            'refunded_on_completed' => $r($sum['refunded']),
            'upcoming' => ['count' => $upcoming, 'value' => $r($upcomingValue), 'advance' => $r($upcomingAdvance)],
            'cancelled' => [
                'count' => $cancelled['count'],
                'kept_count' => $cancelled['kept_count'], 'kept' => $r($cancelled['kept']),
                'refunded_count' => $cancelled['refunded_count'], 'refunded' => $r($cancelled['refunded']),
            ],
            'by_trip_type' => array_map(fn ($t) => ['amount' => $r($t['amount']), 'count' => $t['count']], $trip),
        ];
    }

    private static function bookings(?int $fleetOperatorId): Collection
    {
        return DB::table('booking_details as bd')
            ->whereNull('bd.deleted_at')
            ->where('bd.payment_status', Type::ACTIVE)
            ->when($fleetOperatorId, function ($query) use ($fleetOperatorId) {
                $query->leftJoin('drivers as dr', 'dr.id', '=', 'bd.assigned_driver_id')
                    ->leftJoin('cabs as c', 'c.id', '=', 'dr.cab_id')
                    ->where(function ($q) use ($fleetOperatorId) {
                        $q->where('c.fleet_operator_id', $fleetOperatorId);
                        if (self::soleOperator($fleetOperatorId)) {
                            $q->orWhereNull('bd.assigned_driver_id');
                        }
                    });
            })
            ->get(['bd.id', 'bd.status', 'bd.assigned_driver_id', 'bd.pricing_version', 'bd.total_payment', 'bd.base_fare', 'bd.markup_amount', 'bd.tax_amount',
                'bd.surge_price', 'bd.part_payment', 'bd.refund', 'bd.platform_fee_amount', 'bd.trip_type', 'bd.air_port_drop', 'bd.sight_seeing_package_id']);
    }

    /** Is this the only fleet operator that has cabs? */
    private static function soleOperator(int $fleetOperatorId): bool
    {
        $operators = DB::table('users as u')->join('cabs as c', 'c.fleet_operator_id', '=', 'u.id')
            ->where('u.type', Type::FleetOperator)->whereNull('c.deleted_at')->whereNull('u.deleted_at')
            ->distinct()->pluck('u.id');

        return $operators->count() === 1 && (int) $operators->first() === $fleetOperatorId;
    }

    /** Gateway payments per booking: amount received (paise) and the settlement JSON. */
    private static function payments(Collection $bookingIds): array
    {
        $out = [];
        foreach ($bookingIds->chunk(1000) as $ids) {
            $rows = DB::table('payments')->whereIn('booking_id', $ids)->whereNull('deleted_at')
                ->whereIn('status', [Type::PAID, Type::REFUND])->orderBy('id')
                ->get(['booking_id', 'amount', 'amount_settlement']);
            foreach ($rows as $row) {
                $out[$row->booking_id] ??= ['amount' => 0, 'settlement' => []];
                $out[$row->booking_id]['amount'] += self::paise($row->amount);
                $out[$row->booking_id]['settlement'] = (json_decode((string) $row->amount_settlement, true) ?: []) + $out[$row->booking_id]['settlement'];
            }
        }

        return $out;
    }

    /** What the customer is charged for the ride (paise), incl. markup and GST. */
    private static function price(object $b): int
    {
        $total = self::paise($b->total_payment);
        if ((int) $b->pricing_version >= 2) {
            return $total;
        }
        // v1: total_payment sometimes left out the markup the customer paid.
        $markup = self::paise($b->tax_amount);

        return ($markup > 0 && abs($total - self::paise($b->base_fare)) < 100) ? $total + $markup : $total;
    }

    /** [OfinIT platform fee, GST on it] in paise. */
    private static function ofinitFee(object $b, ?array $pay): array
    {
        $settlement = $pay['settlement'] ?? [];
        if ((int) $b->pricing_version >= 2 && $b->platform_fee_amount !== null) {
            return [self::paise($b->platform_fee_amount), max(0, self::paise($settlement['platform_fee_gst'] ?? 0))];
        }

        return [max(0, self::paise($settlement['company_commission'] ?? 0)), 0];
    }

    private static function tripType(object $b): string
    {
        // Older / native-app bookings use 'Airport Pickup', 'Airport Drop', 'In-City Ride';
        // the PWA uses 'Airport Transfer' + air_port_drop and 'In-City Rides'.
        return match (true) {
            $b->sight_seeing_package_id !== null => 'sightseeing',
            $b->trip_type === 'Airport Pickup' => 'airport_pickup',
            $b->trip_type === 'Airport Drop' => 'airport_drop',
            $b->trip_type === 'Airport Transfer' && (string) $b->air_port_drop === (string) Type::AIRPORT_PICKUP => 'airport_pickup',
            $b->trip_type === 'Airport Transfer' && (string) $b->air_port_drop === (string) Type::AIRPORT_DROP => 'airport_drop',
            in_array($b->trip_type, ['In-City Rides', 'In-City Ride'], true) => 'in_city',
            default => 'sightseeing',
        };
    }

    /** Rupee value (number or "1,234.50" string) → paise. */
    private static function paise(mixed $rupees): int
    {
        return (int) round(((float) str_replace(',', '', (string) ($rupees ?? 0))) * 100);
    }
}
