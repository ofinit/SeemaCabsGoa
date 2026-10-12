<?php

namespace App\Http\Middleware;

use App\Enums\Type;
use App\Services\Platform\PlatformSettings;
use App\Services\Platform\PlatformSuspension;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * While the service is suspended for unpaid OfinIT invoices, only these keep
 * working (see PlatformSuspension):
 *  - OfinIT's admin login (everything); Seema's fleet operator login: Billing only
 *  - SOS and rides already under way (PWA trip pages, app booking screens)
 *  - payment-gateway webhooks
 * Everything else gets the "temporarily unavailable" page (or a 503 JSON).
 */
class PlatformSuspensionGate
{
    /** Paths that always work (login screens, webhooks, signed invoice links). */
    private const ALWAYS = [
        'login', 'logout', 'security-check', 'authentication-check', 'reset', 'password/*', 'password-update',
        'api/razorpay/webhook', 'api/cashfree/webhook', 'api/ofinit/razorpay/webhook',
        'invoices/*', 'up',
    ];

    /** Customer app (API) endpoints kept for rides under way and SOS. */
    private const API_KEEP = [
        'api/login', 'api/settings', 'api/current-booking', 'api/user-details', 'api/notification/list',
        'api/get-page-content/*', 'api/vendor-token-update',
    ];

    /** PWA pages and actions kept (login, current ride, SOS numbers). */
    private const PWA_KEEP = [
        'app', 'app/login', 'app/logout', 'app/actions/settings', 'app/actions/bookings/current', 'app/actions/page-content/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!PlatformSettings::suspended() || $request->is(...self::ALWAYS)) {
            return $next($request);
        }

        // Admin area: OfinIT keeps full access; Seema's fleet operator only the Billing page.
        if ($request->is('admin', 'admin/*')) {
            $user = $request->user();
            if (!$user || (int) $user->type === Type::ADMIN || $request->routeIs('admin.billing.*')) {
                return $next($request);
            }

            return $request->expectsJson()
                ? response()->json(['status' => false, 'message' => 'Service suspended — please pay the outstanding OfinIT invoice.'], 503)
                : redirect()->route('admin.billing.index');
        }

        // Customer app (API).
        if ($request->is('api/*')) {
            if ($request->is(...self::API_KEEP)) {
                return $next($request);
            }
            if ($request->is('api/booking-detail/*')) {
                $customer = $request->user('sanctum');
                if (PlatformSuspension::rideUnderWay($request->route('id') ?? basename($request->path()), $customer?->id)) {
                    return $next($request);
                }
            }

            return response()->json(['status' => false, 'message' => 'Seema Cabs Goa is temporarily unavailable.', 'data' => (object) []], 503);
        }

        // Customer PWA.
        if ($request->is('app', 'app/*')) {
            $customerId = auth('customer')->id();
            if ($request->is(...self::PWA_KEEP)) {
                return $next($request);
            }
            if ($request->is('app/trip/*', 'app/actions/bookings/*') && PlatformSuspension::rideUnderWay($request->route('booking') ?? $request->route('id'), $customerId)) {
                return $next($request);
            }

            return $this->unavailable($request, $customerId);
        }

        // Marketing pages, ads links, everything else.
        return $this->unavailable($request, auth('customer')->id());
    }

    private function unavailable(Request $request, ?int $customerId): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => false, 'message' => 'Seema Cabs Goa is temporarily unavailable.'], 503);
        }

        return response()->view('platform.unavailable', [
            'ride' => PlatformSuspension::currentRide($customerId),
            'sos' => PlatformSuspension::sosNumbers(),
        ], 503)->header('Retry-After', '3600')->header('Cache-Control', 'no-store');
    }
}
