<?php

namespace App\Http\Controllers\Api;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\Environment;
use App\Models\Payment;
use App\Models\UserFcmToken;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

/**
 * Public endpoint Razorpay calls directly (not the browser) whenever a
 * refund is processed — regardless of whether that refund was issued
 * through this app's own Cancel & Refund flow (Admin\RazorpayController)
 * or manually via Razorpay's own dashboard. Without this, only refunds
 * issued through the app itself ever get recorded in booking_details/
 * payments; anything done directly in Razorpay's dashboard silently goes
 * unrecorded (exactly how the 5 bookings found by the Reconcile Cancelled
 * Payments report went unrecorded — see buglog bug-011).
 *
 * Setup required in the Razorpay Dashboard (Settings → Webhooks), done by
 * the user, not this code: add a webhook pointing at this route's public
 * URL, subscribe to the `refund.processed` event, and set a secret — then
 * store that same secret as the `razorpaywebhooksecret` Environment row
 * (Type::RAZORPAY_WEBHOOK_SECRET) so verifyWebhookSignature() below can
 * confirm requests genuinely came from Razorpay.
 *
 * Idempotent: safe to receive the same event more than once (Razorpay
 * retries on anything but a 200 response) — re-applying the same refund
 * amount/status is a no-op the second time.
 */
class RazorpayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        $secretRow = Environment::where('title', Type::RAZORPAY_WEBHOOK_SECRET)->first();
        if (!$secretRow || empty($secretRow->value)) {
            Log::error('Razorpay webhook received but no webhook secret is configured (Environment title=razorpaywebhooksecret).');
            return response()->json(['status' => false, 'message' => 'Webhook not configured.'], 500);
        }

        if (empty($signature)) {
            Log::warning('Razorpay webhook received with no X-Razorpay-Signature header.');
            return response()->json(['status' => false, 'message' => 'Missing signature.'], 400);
        }

        try {
            (new Api('', ''))->utility->verifyWebhookSignature($rawBody, $signature, $secretRow->value);
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Invalid signature.'], 400);
        }

        $event = json_decode($rawBody, true);
        Log::info('Razorpay webhook received: ' . ($event['event'] ?? 'unknown event'));

        if (($event['event'] ?? null) === 'refund.processed') {
            $this->handleRefundProcessed($event);
        }
        // Other events (payment.captured, payment.failed, etc.) are
        // deliberately ignored — this endpoint's only job right now is
        // keeping refund status in sync. Always ack with 200 regardless,
        // so Razorpay doesn't retry events we intentionally don't act on.

        return response()->json(['status' => true]);
    }

    private function handleRefundProcessed(array $event): void
    {
        $paymentEntity = $event['payload']['payment']['entity'] ?? null;
        $refundEntity = $event['payload']['refund']['entity'] ?? null;

        if (!$paymentEntity || !$refundEntity) {
            Log::warning('Razorpay refund.processed webhook missing expected payload.payment/payload.refund entities.');
            return;
        }

        $razorpayPaymentId = $paymentEntity['id'] ?? null;
        $amountRefunded = ($paymentEntity['amount_refunded'] ?? 0) / 100;
        $refundStatus = $paymentEntity['refund_status'] ?? null; // 'full' or 'partial'

        if (!$razorpayPaymentId || $amountRefunded <= 0) {
            Log::warning('Razorpay refund.processed webhook had no usable payment id / refund amount.');
            return;
        }

        $payment = Payment::where('transaction_id', $razorpayPaymentId)->first();
        if (!$payment) {
            Log::info("Razorpay refund.processed for {$razorpayPaymentId} — no matching Payment row in our DB (not one of our bookings, or transaction_id was never recorded).");
            return;
        }

        $bookingDetail = BookingDetail::find($payment->booking_id);
        if (!$bookingDetail) {
            Log::warning("Razorpay refund.processed for {$razorpayPaymentId} — Payment row {$payment->id} has no matching BookingDetail (booking_id={$payment->booking_id}).");
            return;
        }

        $alreadyRecorded = $bookingDetail->refund !== null && (float) $bookingDetail->refund >= $amountRefunded;

        $bookingDetail->refund = $amountRefunded;
        $bookingDetail->status = Type::REFUND;
        $bookingDetail->save();

        $payment->status = Type::REFUND;
        $payment->save();

        Log::info("Razorpay refund.processed for {$razorpayPaymentId}: booking {$bookingDetail->booking_id} refund recorded as ₹{$amountRefunded} ({$refundStatus}).");

        if ($alreadyRecorded) {
            // Already up to date from a prior delivery of this same event — skip re-notifying the customer.
            return;
        }

        try {
            $notification = new NotificationService();
            $title = 'Refund Processed';
            $body = "Your refund for booking {$bookingDetail->booking_id} has been processed. Will be credited in 5-7 working days.";
            $data = ['booking_id' => $bookingDetail->booking_id];

            $notification->storeUserNotification($bookingDetail->customer_id, $title, $body, $data, '');

            $userFcmToken = UserFcmToken::where('user_id', $bookingDetail->customer_id)->first();
            if ($userFcmToken) {
                $notification->sendUserNotification($userFcmToken->token, $title, $body, $data, $bookingDetail->customer_id);
            }
        } catch (\Throwable $e) {
            Log::warning('Razorpay webhook: customer notification failed (refund itself was still recorded): ' . $e->getMessage());
        }
    }
}
