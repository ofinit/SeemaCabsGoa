<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Models\SettingChange;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            return view('my-account', compact('user'));
        } catch (\Throwable $th) {
            Log::error('Getting error of display account page  :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    public function updateProfileImage(Request $request)
    {
        try {
            if (!isset($request->image) && $request->image != null) {
                return redirect()->back()->with('error', 'Image field is required.');
            }
            $user = Auth::user();
            // destroy old image if exists
            if ($user->image) {
                RemoveImage($user->image, 'customer');
            }
            // upload new image 
            if ($request->image) {
                $imageName = UploadImage($request->image, 'customer');
            }
            $user->image = $imageName;
            $user->save();

            return redirect()->back()->with('success', 'Profile image updated successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of display account page  :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    /** Change the logged-in admin's login email (requires the current password). */
    public function changeEmail(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:191', Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at')],
            'current_password' => 'required|string',
        ], [
            'email.unique' => 'This email is already used by another account.',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return redirect()->back()->withInput()->withErrors(['current_password' => 'Current password is not correct.']);
        }

        $old = $user->email;
        $user->email = strtolower(trim($data['email']));
        $user->save();
        SettingChange::record("account.{$user->id}.email", $old, $user->email);

        return redirect()->back()->with('success', 'Login email changed to ' . $user->email . '. Use it the next time you log in.');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password is not matched.');
            }
            $user->password = $request->new_password;
            $user->save();
            return redirect()->back()->with('success', 'Password changed successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of update password :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }
}
