<?php

namespace App\Http\Controllers\Fleet;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\FleetDeviceToken;
use App\Services\Fleet\FleetBookings;
use App\Services\Platform\PlatformSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The Seema Cabs Fleet app (Android shell in fleet-app/ loads these pages):
 * new-booking alerts, bookings by tab and each booking's details and status.
 * Fleet operator (Seema) and admin (OfinIT) logins only; 2FA as in the admin.
 */
class FleetController extends Controller
{
    public function loginForm()
    {
        if (self::allowed(Auth::user())) {
            return redirect()->route('fleet.index');
        }

        return view('fleet.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|string|max:191', 'password' => 'required|string|max:191']);
        $ok = Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
            'type' => [Type::ADMIN, Type::FleetOperator],
            'status' => Type::ActiveUser,
        ], true);
        if (!$ok) {
            return back()->withInput(['email' => $data['email']])->with('error', 'Wrong email or password.');
        }
        $request->session()->regenerate();
        if (Auth::user()->security) {
            // Google Authenticator first (admin 2FA page, same rule as the admin login), then back here.
            session(['url.intended' => route('fleet.index')]);

            return redirect()->route('admin.twoFaSecurityCheck');
        }

        return redirect()->route('fleet.index');
    }

    public function logout(Request $request)
    {
        if ($token = $request->input('token')) {
            FleetDeviceToken::where('token', $token)->where('user_id', Auth::id())->delete();
        }
        if (Auth::user()?->security) {
            Auth::user()->forceFill(['authenticate' => 0])->save(); // as the admin logout does
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('fleet.login');
    }

    public function index(Request $request)
    {
        $tab = array_key_exists($request->query('tab'), FleetBookings::TABS) ? $request->query('tab') : 'upcoming';
        $user = Auth::user();

        return view('fleet.index', [
            'tab' => $tab,
            'bookings' => FleetBookings::forTab($user, $tab),
            'counts' => FleetBookings::counts($user),
            'suspended' => PlatformSettings::suspended(),
        ]);
    }

    public function show(string $booking)
    {
        $details = FleetBookings::find(Auth::user(), $booking);
        abort_unless($details, 404);

        return view('fleet.show', ['b' => $details, 'suspended' => PlatformSettings::suspended()]);
    }

    /** The app's Firebase token for this phone (called by the page inside the app). */
    public function registerToken(Request $request)
    {
        $data = $request->validate(['token' => 'required|string|min:20|max:255', 'platform' => 'nullable|string|max:20']);
        FleetDeviceToken::updateOrCreate(
            ['token' => $data['token']],
            ['user_id' => Auth::id(), 'platform' => $data['platform'] ?? 'android', 'last_seen_at' => now()]
        );

        return response()->json(['status' => true]);
    }

    public static function allowed($user): bool
    {
        return $user && in_array((int) $user->type, [Type::ADMIN, Type::FleetOperator], true)
            && (int) $user->status === Type::ActiveUser;
    }
}
