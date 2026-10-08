<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Environment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $envoirements = Environment::pluck('value', 'title')->toArray();
        return view('settings.payment-gateway', compact('envoirements'));
    }

    public function StoreUpdate(Request $request)
    {
        try {
            $keysToSave = [
                'razorpay_enabled' => $request->has('razorpay_enabled') ? '1' : '0',
                'cashfree_enabled' => $request->has('cashfree_enabled') ? '1' : '0',
                'primary_payment_gateway' => $request->input('primary_payment_gateway', 'both'),
                'cashfree_mode' => $request->input('cashfree_mode', 'sandbox'),
                'paymentkey' => $request->input('paymentkey', ''),
                'paymentsecrete' => $request->input('paymentsecrete', ''),
                'razorpaywebhooksecret' => $request->input('razorpaywebhooksecret', ''),
                'cashfree_app_id' => $request->input('cashfree_app_id', ''),
                'cashfree_secret_key' => $request->input('cashfree_secret_key', ''),
                'cashfree_webhook_secret' => $request->input('cashfree_webhook_secret', ''),
            ];

            foreach ($keysToSave as $title => $val) {
                Environment::updateOrCreate(
                    ['title' => $title],
                    ['value' => $val]
                );
            }

            // Also support dynamic title/value arrays if passed for backward compatibility
            if ($request->has('title') && is_array($request->title)) {
                foreach ($request->title as $key => $value) {
                    $slug = Str::slug($value);
                    if (!isset($keysToSave[$slug])) {
                        Environment::updateOrCreate(
                            ['title' => $slug],
                            ['value' => $request->value[$key] ?? '']
                        );
                    }
                }
            }

            return redirect()->back()->with('success', 'Payment gateway settings saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store and update payment keys :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again later.');
        }
    }
}
