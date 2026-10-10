<?php

namespace App\Http\Controllers\Api;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\UserFcmToken;
use App\Services\Ads\AdCampaignService;
use App\Services\Ads\AdPaymentService;
use App\Services\CashfreeService;
use App\Services\Invoicing\InvoiceService;
use App\Services\Payments\PaymentVerifier;
use App\Services\Payments\PlatformFeeTransferService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CashfreeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('x-webhook-signature');
        $timestamp = $request->header('x-webhook-timestamp');

        $cashfree = app(CashfreeService::class);

        if (empty($signature) || empty($timestamp)) {
            Log::warning('Cashfree webhook received without required signature or timestamp headers.');
            return response()->json(['status' => false, 'message' => 'Missing signature headers.'], 400);
        }

        if (!$cashfree->verifyWebhookSignature($rawBody, $signature, $timestamp)) {
            Log::warning('Cashfree webhook signature verification failed.');
            return response()->json(['status' => false, 'message' => 'Invalid webhook signature.'], 400);
        }

        $event = json_decode($rawBody, true);
        $eventType = $event['type'] ?? 'UNKNOWN';
        Log::info("Cashfree webhook received: {$eventType}");

        switch ($eventType) {
            case 'PAYMENT_SUCCESS_WEBHOOK':
            case 'ORDER_PAID':
                $this->handlePaymentSuccess($event);
                break;

            case 'REFUND_STATUS_WEBHOOK':
                $this->handleRefundStatus($event);
                break;

            default:
                Log::info("Cashfree webhook event {$eventType} ignored.");
                break;
        }

        // Always return 200 OK so Cashfree knows event was received
        return response()->json(['status' => true]);
    }

    private function handlePaymentSuccess(array $event): void
    {
        $orderData = $event['data']['order'] ?? [];
        $paymentData = $event['data']['payment'] ?? [];

        $orderId = $orderData['order_id'] ?? null;
        $cfPaymentId = (string) ($paymentData['payment_id'] ?? ($paymentData['cf_payment_id'] ?? ''));
        $amount = (float) ($paymentData['payment_amount'] ?? ($orderData['order_amount'] ?? 0));

        if (!$orderId) {
            Log::warning('Cashfree payment webhook missing order_id.');
            return;
        }

        // Self-serve ad orders (ADS_<campaign id>_<time>) — verified with Cashfree, never trusted from the webhook body.
        if (preg_match('/^' . AdPaymentService::CASHFREE_PREFIX . '(\d+)_\d+$/', $orderId, $m)) {
            $campaign = AdCampaign::find($m[1]);
            if ($campaign && !$campaign->isPaid()) {
                $result = app(AdPaymentService::class)->verify($campaign, 'cashfree', $orderId);
                if ($result['ok']) {
                    app(AdCampaignService::class)->paymentReceived($campaign, 'cashfree', $result['transaction_id']);
                    Log::info("Ad {$campaign->reference} marked as paid via Cashfree webhook.");
                } else {
                    Log::warning("Cashfree webhook for ad order {$orderId} not confirmed: {$result['message']}");
                }
            }
            return;
        }

        // Booking orders are CF_<booking id>_<time>; booking ids keep their hyphen (CF_SC-GOA0001_…).
        $booking = null;
        if (preg_match('/^CF_([A-Za-z0-9-]+)_\d+$/', $orderId, $matches)) {
            $extractedId = $matches[1];
            $booking = BookingDetail::where('booking_id', $extractedId)->first();
            if (!$booking && str_starts_with($extractedId, 'SCGOA')) {
                $booking = BookingDetail::where('booking_id', 'SC-GOA' . substr($extractedId, 5))->first();
            }
        }

        // Confirm with Cashfree against the amount stored for the booking *before* this
        // webhook writes anything (AGENTS.md: payment confirmation goes through PaymentVerifier).
        if ($booking && (int) $booking->payment_status !== 1) {
            $expected = (float) (Payment::where('booking_id', $booking->id)->orderBy('id')->value('amount') ?? $booking->part_payment);
            $check = app(PaymentVerifier::class)->verify($booking, 'cashfree', $cfPaymentId ?: $orderId, $orderId, $expected);
            if (!$check['ok']) {
                Log::warning("Cashfree webhook: {$orderId} not confirmed for booking {$booking->booking_id}: {$check['message']}");
                return;
            }
            $amount = (float) ($check['amount'] ?? $amount);
        }

        // Existing Payment row for this order (never match on an empty payment id).
        $payment = Payment::where('pg_order_id', $orderId)
            ->when($cfPaymentId !== '', fn ($q) => $q->orWhere('transaction_id', $cfPaymentId))
            ->first();

        if (!$payment) {
            Payment::create([
                'booking_id' => $booking?->id,
                'customer_id' => $booking?->customer_id,
                'transaction_id' => $cfPaymentId ?: $orderId,
                'payment_gateway' => 'cashfree',
                'pg_order_id' => $orderId,
                'date' => now()->toDateString(),
                'amount' => $amount,
                'amount_settlement' => json_encode($orderData),
                'status' => Type::PAID,
                'transfer_amount_status' => !empty($orderData['order_splits']) ? 1 : 0,
            ]);
        } else {
            $payment->update([
                'status' => Type::PAID,
                'transaction_id' => $cfPaymentId ?: $payment->transaction_id,
                'payment_gateway' => 'cashfree',
                'pg_order_id' => $orderId,
                'transfer_amount_status' => !empty($orderData['order_splits']) ? 1 : $payment->transfer_amount_status,
            ]);
        }

        if ($booking && (int) $booking->payment_status !== 1) {
            $booking->update([
                'payment_status' => 1,
                'status' => $booking->status == Type::UNPAID ? Type::PAID : $booking->status,
            ]);
            Log::info("Booking {$booking->booking_id} marked as paid via Cashfree webhook.");

            $payment = Payment::where('booking_id', $booking->id)->where('pg_order_id', $orderId)->first();
            try {
                app(InvoiceService::class)->receiptVoucher($booking->fresh(), $payment);
            } catch (\Throwable $e) {
                Log::error("Receipt voucher failed for {$booking->booking_id}: " . $e->getMessage());
            }
            try {
                if ($payment) {
                    app(PlatformFeeTransferService::class)->transfer($booking->fresh(), $payment->fresh());
                }
            } catch (\Throwable $e) {
                Log::error("OfinIT fee transfer record failed for {$booking->booking_id}: " . $e->getMessage());
            }
        }
    }

    private function handleRefundStatus(array $event): void
    {
        $refundData = $event['data']['refund'] ?? [];
        $orderId = $refundData['order_id'] ?? null;
        $refundStatus = strtoupper($refundData['refund_status'] ?? '');
        $refundAmount = (float) ($refundData['refund_amount'] ?? 0);

        if ($refundStatus !== 'SUCCESS' || !$orderId) {
            return;
        }
        // Ad refunds are recorded when they are made (AdPaymentService::refund).
        if (str_starts_with($orderId, AdPaymentService::CASHFREE_PREFIX)) {
            return;
        }

        $payment = Payment::where('pg_order_id', $orderId)
            ->orWhere('transaction_id', $orderId)
            ->first();

        if ($payment) {
            $payment->update([
                'status' => Type::REFUND,
            ]);

            if ($payment->booking_id) {
                $booking = BookingDetail::find($payment->booking_id);
                if ($booking) {
                    $booking->status = Type::REFUND;
                    $booking->refund = $refundAmount ?: $booking->refund;
                    $booking->save();

                    try {
                        app(InvoiceService::class)->refundVoucher($booking, (float) $booking->refund);
                    } catch (\Throwable $e) {
                        Log::error("Refund voucher failed for {$booking->booking_id}: " . $e->getMessage());
                    }

                    // Notify customer
                    $userFcmToken = UserFcmToken::where('user_id', $booking->customer_id)->first();
                    $notification = new NotificationService();
                    $title = "Refund Successful";
                    $body = "Your booking amount for {$booking->booking_id} has been refunded via Cashfree.";
                    $data = ['booking_id' => $booking->booking_id];

                    $notification->storeUserNotification($booking->customer_id, $title, $body, $data, "");
                    if (!empty($userFcmToken)) {
                        $notification->sendUserNotification($userFcmToken->token, $title, $body, $data, $booking->customer_id);
                    }
                }
            }
        }
    }
}
