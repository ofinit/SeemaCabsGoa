<?php

namespace App\Http\Controllers\Customer;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\OtpVerifyRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\SendForgotPasswordMailRequest;
use App\Http\Requests\Api\UpdatePasswordRequest;
use App\Mail\Api\PasswordReset;
use App\Models\Country;
use App\Models\Environment;
use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('customer.auth.login', ['googleClientId' => $this->googleClientId()]);
    }

    /**
     * Reuses the same FormRequest (and therefore the exact same validation
     * rules/messages) as the mobile API's login endpoint. Deliberately does
     * NOT call Api\AuthController::login() because that issues a Sanctum
     * bearer token — this surface uses a plain Blade session instead.
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'type' => Type::CUSTOMER,
            'status' => Type::ActiveUser,
        ];

        if (! Auth::guard('customer')->attempt($credentials)) {
            return response()->json(['status' => false, 'message' => 'Invalid credentials.'], 400);
        }

        $request->session()->regenerate();

        return response()->json([
            'status' => true,
            'message' => 'Logged in successfully.',
            'data' => ['redirect' => route('customer.home')],
        ]);
    }

    public function showSignup()
    {
        // Pre-shaped here (rather than mapped inline in the Blade @json() call)
        // because Blade's @json() directive splits its argument on every comma
        // to separate value/flags/depth — a map closure's array literal commas
        // corrupt that split and silently drop the HTML-safe encoding flags.
        $countries = Country::orderBy('name')->get(['id', 'name'])
            ->map(fn ($c) => ['id' => (string) $c->id, 'name' => $c->name])
            ->values();

        return view('customer.auth.signup', array_merge(
            compact('countries'),
            ['googleClientId' => $this->googleClientId()]
        ));
    }

    public function register(RegisterRequest $request)
    {
        $user = User::store($request->all());

        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later.'], 500);
        }

        Auth::guard('customer')->login($user);
        $request->session()->regenerate();

        return response()->json([
            'status' => true,
            'message' => 'Account created successfully.',
            'data' => ['redirect' => route('customer.home')],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }

    public function showForgotPassword()
    {
        return view('customer.auth.forgot-password');
    }

    public function sendResetOtp(SendForgotPasswordMailRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $otp = rand(1111, 9999);
        $user->otp = $otp;
        $user->save();

        try {
            Mail::to($request->email)->send(new PasswordReset($user->name, $otp, 'Forgot password mail'));
        } catch (\Throwable $th) {
            Log::error('Customer forgot-password mail failed: ' . $th->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email.',
            'data' => ['redirect' => route('customer.forgot-password.otp', ['email' => $request->email])],
        ]);
    }

    public function showOtp(Request $request)
    {
        return view('customer.auth.otp', ['email' => $request->query('email', '')]);
    }

    public function verifyOtp(OtpVerifyRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->otp != $request->otp) {
            return response()->json(['status' => false, 'message' => 'Invalid OTP.'], 400);
        }

        $user->otp = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully.',
            'data' => ['redirect' => route('customer.forgot-password.reset', ['email' => $request->email])],
        ]);
    }

    public function showReset(Request $request)
    {
        return view('customer.auth.reset-password', ['email' => $request->query('email', '')]);
    }

    public function resetPassword(UpdatePasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $user->password = $request->password;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully.',
            'data' => ['redirect' => route('customer.login')],
        ]);
    }

    /**
     * Handles both "Google Login" and "Google Sign Up" — like the mobile
     * API's login() social-login branch, the first sign-in with a given
     * Google account auto-creates the customer record. Unlike the mobile
     * API (which trusts a client-supplied email/social_login flag with no
     * verification), this endpoint cryptographically verifies the Google
     * ID token server-side before trusting anything in it — a client can't
     * forge a token for an email they don't control.
     */
    public function googleLogin(Request $request)
    {
        $request->validate(['credential' => 'required|string']);

        $clientId = $this->googleClientId();
        if (! $clientId) {
            return response()->json(['status' => false, 'message' => 'Google sign-in is not configured yet.'], 400);
        }

        try {
            $payload = $this->verifyGoogleIdToken($request->credential, $clientId);
        } catch (\Throwable $e) {
            Log::warning('Google ID token verification failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Google sign-in failed. Please try again.'], 401);
        }

        $email = $payload->email ?? null;
        if (! $email || empty($payload->email_verified)) {
            return response()->json(['status' => false, 'message' => 'Your Google account has no verified email.'], 401);
        }

        $user = User::where('email', $email)->where('type', Type::CUSTOMER)->first();

        if ($user && $user->status !== Type::ActiveUser) {
            return response()->json(['status' => false, 'message' => 'Your account is inactive or suspended.'], 403);
        }

        if (! $user) {
            $user = User::store([
                'name' => $payload->name ?? explode('@', $email)[0],
                'email' => $email,
                'password' => randomPasswordFromEmail($email),
                'social_login' => Type::GOOGLE,
            ]);

            if (! $user) {
                return response()->json(['status' => false, 'message' => 'Something went wrong, please try again later.'], 500);
            }
        }

        Auth::guard('customer')->login($user);
        $request->session()->regenerate();

        return response()->json([
            'status' => true,
            'message' => 'Logged in successfully.',
            'data' => ['redirect' => route('customer.home')],
        ]);
    }

    private function googleClientId(): ?string
    {
        return Environment::where('title', 'google_client_id')->value('value') ?: null;
    }

    /**
     * Verifies a Google Identity Services ID token: signature (against
     * Google's published JWKS), audience (must match our client ID), and
     * issuer. Throws on any failure — callers must catch and reject.
     */
    private function verifyGoogleIdToken(string $idToken, string $clientId): object
    {
        $keys = Cache::remember('google_jwks', now()->addHours(6), function () {
            $response = Http::timeout(5)->get('https://www.googleapis.com/oauth2/v3/certs');
            $response->throw();
            return $response->json();
        });

        $payload = JWT::decode($idToken, JWK::parseKeySet($keys));

        if ($payload->aud !== $clientId) {
            throw new \RuntimeException('Token audience does not match this app\'s Google client ID.');
        }

        if (! in_array($payload->iss, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            throw new \RuntimeException('Unexpected token issuer.');
        }

        return $payload;
    }
}
