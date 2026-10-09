<?php

namespace App\Http\Controllers\Api;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\UserFcmToken;
use App\Services\CashfreeService;
use App\Services\Invoicing\InvoiceService;
use App\Services\Payments\FleetPayoutService;
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

        // Extract booking_id from order_id format (e.g. CF_SCGOA0001_172713...)
        $booking = null;
        if (preg_match('/^CF_([A-Za-z0-9]+)_/', $orderId, $matches)) {
            $extractedId = $matches[1];
            // Format back to SC-GOAXXXX if needed
            if (str_starts_with($extractedId, 'SCGOA')) {
                $formattedBookingId = 'SC-GOA' . substr($extractedId, 5);
                $booking = BookingDetail::where('booking_id', $formattedBookingId)->first();
            }
            if (!$booking) {
                $booking = BookingDetail::where('booking_id', $extractedId)->first();
            }
        }

        // Check if Payment record already exists
        $payment = Payment::where('transaction_id', $cfPaymentId)
            ->orWhere('pg_order_id', $orderId)
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

        if ($booking && $booking->payment_status != 1) {
            $expected = (float) (Payment::where('booking_id', $booking->id)->where('pg_order_id', $orderId)->value('amount')
                ?? Payment::where('booking_id', $booking->id)->orderBy('id')->value('amount')
                ?? $booking->part_payment);
            if ($amount + 1 < $expected) {
                Log::warning("Cashfree webhook: {$orderId} paid ₹{$amount} but booking {$booking->booking_id} expects ₹{$expected}; not confirming.");
                return;
            }

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
                    app(FleetPayoutService::class)->payout($booking->fresh(), $payment->fresh());
                }
            } catch (\Throwable $e) {
                Log::error("Fleet payout record failed for {$booking->booking_id}: " . $e->getMessage());
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
