<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangePasswordRequest;
use Illuminate\Http\Request;
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
