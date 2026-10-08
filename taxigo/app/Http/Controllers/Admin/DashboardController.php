<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\AdminToken;
use App\Services\FinancialSummaryService;
use App\Services\PlatformOverviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fleet operators log into this same admin guard/dashboard (see
        // Admin\AuthController::doLogin, which accepts [ADMIN, FleetOperator]) —
        // they see the exact same Financial Summary layout as an admin, just
        // scoped to their own cabs' bookings instead of the whole company's.
        $fleetOperatorId = $user->type == Type::FleetOperator ? $user->id : null;

        $summary = FinancialSummaryService::compute($fleetOperatorId);
        $isVendor = (bool) $fleetOperatorId;

        // Platform-wide headcounts only make sense from the admin's seat —
        // a fleet operator's dashboard stays scoped to their own fleet.
        $platform = $isVendor ? null : PlatformOverviewService::compute();

        return view('dashboard', ['summary' => $summary, 'isVendor' => $isVendor, 'platform' => $platform]);
    }

    public function storeToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $admin = $request->user();
        $data = [
            'admin_id' => $admin->id,
            'token' => $request->token,
        ];

        AdminToken::updateOrCreate(
            ['token' => $data['token']],
            $data
        );

        return response()->json(['success' => true]);
    }
}
