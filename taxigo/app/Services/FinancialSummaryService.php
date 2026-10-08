<?php

namespace App\Services;

use App\Enums\Type;
use Illuminate\Support\Facades\DB;

/**
 * Computes the "Financial Summary" / "Revenue by Trip Type" figures shown on
 * both the admin dashboard (company-wide) and the vendor/fleet-operator
 * dashboard (scoped to that operator's own cabs) — deliberately the same
 * computation for both, so the two dashboards can never drift apart, per
 * `compute(null)` = admin (all bookings) vs `compute($fleetOperatorId)` =
 * vendor (only bookings on that operator's cabs).
 *
 * Only bookings with payment_status = ACTIVE (i.e. actually paid for) count
 * as revenue, matching the existing convention in
 * Admin\Reports\FleetOperatorPaymentController.
 */
class FinancialSummaryService
{
    public static function compute(?int $fleetOperatorId = null): array
    {
        $bookings = DB::table('booking_details as bd')
            ->when($fleetOperatorId, function ($query) use ($fleetOperatorId) {
                $query->join('cabs as c', 'c.id', '=', 'bd.cab_id')
                    ->where('c.fleet_operator_id', $fleetOperatorId);
            })
            ->whereNull('bd.deleted_at')
            ->where('bd.payment_status', Type::ACTIVE);

        $totals = (clone $bookings)->selectRaw('
                COALESCE(SUM(bd.total_payment), 0) as gross_amount,
                COALESCE(SUM(bd.base_fare), 0) as base_fare,
                COALESCE(SUM(bd.surge_price), 0) as surge_pricing,
                COALESCE(SUM(bd.tax_amount), 0) as other_charges,
                COALESCE(SUM(bd.part_payment), 0) as collected_online,
                COALESCE(SUM(bd.cash_with_driver), 0) as cash_to_drivers
            ')
            ->first();

        // trip_type isn't a single consistent convention across the app's history:
        // the customer PWA sends 'Airport Transfer' + an air_port_drop flag, while
        // older/native-app bookings instead use the direct literals 'Airport Pickup',
        // 'Airport Drop', and 'In-City Ride' (singular, vs the PWA's 'In-City Rides').
        // All variants are matched here so real bookings don't fall through into
        // "Sightseeing/Other" just because of which client created them — confirmed
        // via a full `GROUP BY trip_type` audit of booking_details.
        $byTripType = (clone $bookings)
            ->selectRaw("
                CASE
                    WHEN bd.sight_seeing_package_id IS NOT NULL THEN 'sightseeing'
                    WHEN bd.trip_type = 'Airport Pickup' THEN 'airport_pickup'
                    WHEN bd.trip_type = 'Airport Drop' THEN 'airport_drop'
                    WHEN bd.trip_type = 'Airport Transfer' AND bd.air_port_drop = ? THEN 'airport_pickup'
                    WHEN bd.trip_type = 'Airport Transfer' AND bd.air_port_drop = ? THEN 'airport_drop'
                    WHEN bd.trip_type IN ('In-City Rides', 'In-City Ride') THEN 'in_city'
                    ELSE 'sightseeing'
                END as bucket,
                COUNT(*) as booking_count,
                COALESCE(SUM(bd.total_payment), 0) as amount
            ", [Type::AIRPORT_PICKUP, Type::AIRPORT_DROP])
            ->groupBy('bucket')
            ->get()
            ->keyBy('bucket');

        // Commissions live only in payments.amount_settlement (a JSON string
        // written at booking time — see Api\BookingController::store), never
        // as their own booking_details columns. Values inside are formatted
        // with number_format() (e.g. "1,111.00"), so they're pulled as text
        // and summed here rather than cast in SQL.
        $settlements = (clone $bookings)
            ->join('payments as p', 'p.booking_id', '=', 'bd.id')
            ->whereNull('p.deleted_at')
            ->where('p.status', Type::PAID)
            ->pluck('p.amount_settlement');

        $seemaCabsCommission = 0.0;
        $platformCommission = 0.0;
        foreach ($settlements as $json) {
            $settlement = json_decode($json, true);
            if (!is_array($settlement)) {
                continue;
            }
            $seemaCabsCommission += (float) str_replace(',', '', $settlement['company_commission'] ?? 0);
            $platformCommission += (float) str_replace(',', '', $settlement['fleet_operator_commission'] ?? 0);
        }

        $bucket = fn (string $key) => [
            'amount' => (float) ($byTripType[$key]->amount ?? 0),
            'count' => (int) ($byTripType[$key]->booking_count ?? 0),
        ];

        return [
            'gross_amount' => (float) $totals->gross_amount,
            'base_fare' => (float) $totals->base_fare,
            'surge_pricing' => (float) $totals->surge_pricing,
            'other_charges' => (float) $totals->other_charges,
            'collected_online' => (float) $totals->collected_online,
            'seema_cabs_commission' => $seemaCabsCommission,
            'platform_commission' => $platformCommission,
            'cash_to_drivers' => (float) $totals->cash_to_drivers,
            'by_trip_type' => [
                'airport_pickup' => $bucket('airport_pickup'),
                'airport_drop' => $bucket('airport_drop'),
                'in_city' => $bucket('in_city'),
                'sightseeing' => $bucket('sightseeing'),
            ],
        ];
    }
}
