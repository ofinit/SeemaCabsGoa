<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Platform\OfinitPaymentLinks;
use App\Services\Platform\PlatformBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook from OfinIT's own Razorpay account (event: payment_link.paid).
 * The signature is checked with OfinIT's webhook secret, and the link is then
 * re-checked with Razorpay before the invoice is marked paid.
 */
class OfinitRazorpayWebhookController extends Controller
{
    public function handle(Request $request, OfinitPaymentLinks $links, PlatformBilling $billing)
    {
        $body = $request->getContent();
        if (!$links->verifyWebhook($body, $request->header('X-Razorpay-Signature'))) {
            Log::warning('OfinIT Razorpay webhook: invalid signature.');

            return response()->json(['status' => false], 400);
        }

        $event = json_decode($body, true) ?: [];
        if (($event['event'] ?? '') === 'payment_link.paid') {
            $linkId = $event['payload']['payment_link']['entity']['id'] ?? null;
            $invoice = $linkId ? Invoice::where('payment_link_id', $linkId)->first() : null;
            if ($invoice && !$invoice->paid_at) {
                try {
                    $billing->refreshLink($invoice);
                } catch (\Throwable $e) {
                    // The hourly check will pick it up.
                    Log::warning("OfinIT payment link {$linkId} check failed: " . $e->getMessage());
                }
            }
        }

        return response()->json(['status' => true]);
    }
}
