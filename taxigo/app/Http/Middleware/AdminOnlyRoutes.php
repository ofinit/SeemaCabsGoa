<?php

namespace App\Http\Middleware;

use App\Enums\Type;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The admin panel is shared by OfinIT (admin login) and Seema Holidays (fleet
 * operator login). These areas are OfinIT's only — enforced here, not just by
 * hiding menu items.
 */
class AdminOnlyRoutes
{
    public const ADMIN_ONLY = [
        'admin.platformBilling.*',
        'admin.invoices.*',
        'admin.advertisements.*',
        'admin.customers.*',
        'admin.report.feeTransfers*',
        'admin.setting.fleetOperators.*',
        'admin.setting.companyDetails.*',
        'admin.setting.paymentGateway.*',
        'admin.setting.pricing', 'admin.setting.savePricing',
        'admin.setting.gst', 'admin.setting.saveGst',
        'admin.setting.businessProfiles', 'admin.setting.saveBusinessProfile',
        'admin.setting.plateFormFee', 'admin.setting.storePlateFormFee',
        'admin.setting.plateFormTax', 'admin.setting.storePlateFormTax',
        'admin.setting.sosNumbers', 'admin.setting.storeSosNumbers',
        'admin.setting.appUpdate', 'admin.setting.storeAppUpdate',
        'admin.setting.logos.*',
        'admin.setting.index', 'admin.setting.save',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && (int) $user->type !== Type::ADMIN && $request->routeIs(...self::ADMIN_ONLY)) {
            abort(403, 'This page is for OfinIT administrators only.');
        }

        return $next($request);
    }
}
