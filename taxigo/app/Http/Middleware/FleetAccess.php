<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Fleet\FleetController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Fleet app pages: fleet operator or admin login, with Google Authenticator done when it's on. */
class FleetAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!FleetController::allowed($user)) {
            return $request->expectsJson()
                ? response()->json(['status' => false, 'message' => 'Please log in.'], 401)
                : redirect()->route('fleet.login');
        }
        if ($user->security && !$user->authenticate) {
            session(['url.intended' => $request->fullUrl()]);

            return redirect()->route('admin.twoFaSecurityCheck');
        }

        return $next($request);
    }
}
