<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function doLogin(LoginRequest $request)
    {
        try {
            $remember = $request->has('remember');
            $field = [
                'email' => $request->email,
                'password' => $request->password,
                'type' => [Type::ADMIN, Type::FleetOperator],
                'status' => Type::ActiveUser
            ];
            if (Auth::attempt($field, $remember)) {
                $user = Auth::user();
                if ($user->security) {
                    return redirect()->route('admin.twoFaSecurityCheck')->with('success', 'Logged in successfully.');
                } else {
                    return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully.');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid credentials, please try again.');
            }
        } catch (\Throwable $th) {
            Log::error('Getting error of do login :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    public function reset()
    {
        return view('auth.passwords.email');
    }

    public function resetPassword(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (empty($user)) {
                return redirect()->back()->with('error', 'Invalid email address.');
            }
            $id = encrypt($user->id);
            $url = route('admin.password', $id);
            $title = 'Forgot password mail';
            try {
                Mail::to($request->email)->send(new PasswordReset($user->name, $url, $title));
            } catch (\Throwable $th) {
                Log::error('Getting error of send mail for forgot password :' . $th->getMessage());
            }

            return redirect()->back()->with('success', 'Otp send successfully on your registered email.');
        } catch (\Throwable $th) {
            Log::error('Getting error of send mail for forgot password :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    public function password($id)
    {
        $userId = decrypt($id);
        return view('auth.passwords.reset', compact('userId'));
    }

    public function passwordUpdate(UpdatePasswordRequest $request)
    {
        try {
            $user = User::find($request->id);

            if (empty($user)) {
                return redirect()->back()->with('error', 'Invalid user details.');
            }
            $user->password = $request->password;
            $user->save();

            return redirect()->route('admin.login')->with('success', 'Password changed successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of update password :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->authenticate = 0;
            $user->save();
            Auth::logout();
            return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of admin log out :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }
    public function twoFaSecurityCheck()
    {
        return view('auth.authintication');
    }

    public function authentication(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate(['otp' => 'required']);
            $google2fa = new Google2FA();
            if (!$google2fa->verifyKey($user->google2fa_secret, $request->otp)) {
                return redirect()->back()->with('error', 'Invalid Google Authenticator code.');
            }
            $user->authenticate = 1;
            $user->save();
            return redirect()->route('admin.dashboard')->with('success', 'Authenticate successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of check 2 fa authentication :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
