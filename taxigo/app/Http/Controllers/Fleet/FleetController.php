<?php

namespace App\Http\Controllers\Fleet;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\FleetDeviceToken;
use App\Models\BookingDetail;
use App\Services\Fleet\FleetBookings;
use App\Services\Fleet\FleetTrips;
use App\Services\Platform\PlatformSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

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

        $booking = BookingDetail::find($details['id']);

        return view('fleet.show', [
            'b' => $details,
            'suspended' => PlatformSettings::suspended(),
            'drivers' => FleetTrips::drivers(Auth::user(), $booking),
            'driverMessage' => FleetTrips::driverMessage($details),
            'canNoShow' => $details['status'] === 'assigned' || $details['status'] === 'confirmed'
                ? now('Asia/Kolkata')->greaterThanOrEqualTo(\Carbon\Carbon::parse($booking->getRawOriginal('pickup_date') . ' ' . $booking->pickup_time, 'Asia/Kolkata'))
                : false,
            'canUndoNoShow' => $details['status'] === 'no_show' && $booking->no_show_at && \Carbon\Carbon::parse($booking->no_show_at)->isSameDay(now()),
            'canRefund' => $details['status'] === 'cancelled' && !((float) $booking->refund > 0),
        ]);
    }

    /** Trip actions from the booking page: assign, complete, cancel (+ refund), refund, no-show, undo. */
    public function action(Request $request, string $booking)
    {
        $details = FleetBookings::find(Auth::user(), $booking);
        abort_unless($details, 404);
        $b = BookingDetail::find($details['id']);
        $data = $request->validate([
            'action' => 'required|in:assign,complete,cancel,refund,no_show,undo_no_show',
            'driver_id' => 'required_if:action,assign|nullable|integer',
            'refund' => 'nullable|boolean',
            'reason' => 'required_if:action,no_show|nullable|string|max:255',
        ]);

        try {
            $message = match ($data['action']) {
                'assign' => tap('Driver assigned. The customer has been notified.', fn () => FleetTrips::assign(Auth::user(), $b, (int) $data['driver_id'])),
                'complete' => FleetTrips::complete($b),
                'cancel' => FleetTrips::cancel($b, (bool) ($data['refund'] ?? false)),
                'refund' => FleetTrips::refund($b),
                'no_show' => FleetTrips::noShow($b, $data['reason']),
                'undo_no_show' => FleetTrips::undoNoShow($b),
            };
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('fleet.show', $b->id)->with('success', $message);
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
