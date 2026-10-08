<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Writer;

class SecurityController extends Controller
{
    public function index()
    {
        return view('security');
    }

    public function qrCodeGenerate(Request $request)
    {
        try {
            $qrCodeHtml = '';
            $message = '';
            $user = Auth::user();
            if ($request->enable == 'true') {
                // if (empty($user->google2fa_secret)) {
                $google2fa = new Google2FA();
                $google2fa_secret = $google2fa->generateSecretKey();
                $user->google2fa_secret = $google2fa_secret;
                $user->save();
                $message = 'QR code generated successfully.';
                $writer = new Writer(new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd()));
                $qrCode = $writer->writeString(
                    $google2fa->getQRCodeUrl(config('app.name'), $user->email, $google2fa_secret)
                );
                $qrCodeHtml = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
                // } else {
                //     $user->security = Type::ENABLED;
                //     $user->save();
                //     $message = '2Fa security Enabled successfully.';
                // }
            } else {
                $user->google2fa_secret = null;
                $user->security = Type::DISABLED;
                $user->save();
                $message = '2FA security Disabled successfully.';
            }
            return response()->json(['status' => true, 'message' => $message, 'qr' => $qrCodeHtml]);
        } catch (\Exception $e) {
            Log::error('Getting error of generate qr code :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
    public function otpVerify(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate(['otp' => 'required']);
            $google2fa = new Google2FA();
            if (!$google2fa->verifyKey($user->google2fa_secret, $request->otp)) {
                $user->google2fa_secret = null;
                $user->save();
                return redirect()->back()->with('error', 'Invalid Google Authenticator code.');
            }
            $user->security = Type::ENABLED;
            $user->save();
            return redirect()->back()->with('success', '2FA security enabled successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of check otp code :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
