<?php

namespace App\Services;

use App\Enums\Type;
use App\Models\Cab;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Platform-wide headcount/inventory stats shown only on the admin's own
 * dashboard (never the vendor/fleet-operator view — those figures don't
 * make sense scoped to one operator's own fleet).
 */
class PlatformOverviewService
{
    public static function compute(): array
    {
        return [
            'total_customers' => User::where('type', Type::CUSTOMER)->count(),
            // Counting every User row with type=FleetOperator overcounts wildly —
            // most are signups that never actually onboarded a vehicle (verified:
            // of 5 such rows, only 1 has any cabs). A "fleet operator" only means
            // something operationally once they have a real fleet, so this counts
            // distinct operators who own at least one non-deleted cab instead.
            'total_fleet_operators' => DB::table('users as u')
                ->join('cabs as c', 'c.fleet_operator_id', '=', 'u.id')
                ->where('u.type', Type::FleetOperator)
                ->whereNull('c.deleted_at')
                ->distinct('u.id')
                ->count('u.id'),
            'total_drivers' => Driver::count(),
            'total_cabs' => Cab::count(),
            'total_bookings' => DB::table('booking_details')->whereNull('deleted_at')->count(),
        ];
    }
}
