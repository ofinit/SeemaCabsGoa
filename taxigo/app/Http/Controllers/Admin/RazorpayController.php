<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\Environment;
use App\Models\Payment;
use App\Models\UserFcmToken;
use App\Services\NotificationService;
use Log;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RazorpayController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $paymentKey = Environment::where('title', Type::PAYMENT_KEY)->first()->value;
        $paymentSecret = Environment::where('title', Type::PAYMENT_SECRETE)->first()->value;

        $this->razorpay = new Api(
            $paymentKey,
            $paymentSecret
        );
    }

    public function refundAmount(Request $request)
    {
        $minHours = Environment::where('title', Type::BOOKING_CANCELLATION_HOUR)->first()->value ?? 0;
        $cancelBeforeMinutes = $minHours * 60;

        try {
            $dateTime = now()->format('Y-m-d H:i:s');
            $bookingDetails = BookingDetail::select(
                    '*',
                    DB::raw("TIMESTAMPDIFF(MINUTE, '$dateTime', CONCAT(pickup_date, ' ', pickup_time)) AS time_diff_minutes")
                )
                ->where('id', $request->id)
                ->where('status', Type::BOOKING_CANCELLATION_STATUS)
                ->whereRaw("TIMESTAMPDIFF(MINUTE, '$dateTime', CONCAT(pickup_date, ' ', pickup_time)) <= ?", [$cancelBeforeMinutes])
                ->first();

            if (!$bookingDetails) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer not eligible for refund.',
                ]);
            }

            $paymentDetails = Payment::where('booking_id', $request->id)->first();
            if (!$paymentDetails) {
                return response()->json([
                    'status' => false,
                    'message' => 'No payment record found for this booking.',
                ], 404);
            }

            $isCashfree = ($paymentDetails->payment_gateway === 'cashfree')
                || !empty($paymentDetails->pg_order_id)
                || str_starts_with($paymentDetails->transaction_id ?? '', 'cf_')
                || str_starts_with($paymentDetails->transaction_id ?? '', 'CF_');

            if ($isCashfree) {
                $cashfree = app(\App\Services\CashfreeService::class);
                $orderId = $paymentDetails->pg_order_id ?: $paymentDetails->transaction_id;
                $refundId = 'ref_' . time() . '_' . rand(100, 999);
                $refundAmount = (float) $paymentDetails->amount;

                // OfinIT's split (fee + GST) is recovered with the refund.
                $refundSplits = null;
                $vendorId = \App\Models\FleetOperator::orderBy('id')->value('cashfree_vendor_id');
                if ($vendorId && $paymentDetails->transfer_reference && (float) $paymentDetails->transfer_amount > 0) {
                    $refundSplits = [['vendor_id' => $vendorId, 'amount' => round((float) $paymentDetails->transfer_amount, 2)]];
                }
                $refundResult = $cashfree->createRefund($orderId, $refundId, $refundAmount, 'Admin booking cancellation refund', $refundSplits);
                $amount = $refundAmount;
            } else {
                $payment = $this->razorpay->payment->fetch($paymentDetails->transaction_id);
                Log::info(json_encode($payment));

                $amountPaisa = $payment->amount;
                // Full refund: reverse_all also reverses the Route transfer of
                // OfinIT's fee + GST, so the merchant isn't left paying it.
                $refund = $payment->refund(array_filter([
                    'amount' => $amountPaisa ? $amountPaisa : null,
                    'reverse_all' => $paymentDetails->transfer_reference ? 1 : null,
                ]));

                if ($refund->status === 'error') {
                    return response()->json([
                        'status' => false,
                        'message' => 'Refund failed: ' . $refund->message,
                    ], 500);
                }
                $amount = $payment->amount / 100;
            }

            $bookingDetails = BookingDetail::find($request->id);
            $bookingDetails->status = Type::REFUND;
            $bookingDetails->refund = $amount ?? 0;
            $bookingDetails->save();

            $bookingPaymentDetails = Payment::where('booking_id', $request->id)->first();
            $bookingPaymentDetails->status = Type::REFUND;
            $bookingPaymentDetails->save();

            try {
                app(\App\Services\Invoicing\InvoiceService::class)->refundVoucher($bookingDetails, (float) $bookingDetails->refund);
            } catch (\Throwable $e) {
                Log::error("Refund voucher failed for {$bookingDetails->booking_id}: " . $e->getMessage());
            }

            //Send Notification to Customer
            $userFcmToken = UserFcmToken::where('user_id', $bookingDetails->customer_id)->first();
            $bookingId = $bookingDetails->booking_id;
            $notification = new NotificationService();

            $title = "Refund Successful";
            $body = "Your booking amount for $bookingId has been refunded.  Will be credited in 5-7 working days.";
            $data = ['booking_id' => $bookingId];

            $notification->storeUserNotification($bookingDetails->customer_id, $title, $body, $data, "");
            if(!empty($userFcmToken))
            {
                $notification->sendUserNotification($userFcmToken->token, $title, $body, $data, $bookingDetails->customer_id);

            }

            return response()->json([
                'status' => true,
                'message' => 'Amount refunded successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Refund failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function refundAmountOld(Request $request)
    {
        $id = $request->id;
        $paymentDetails = Payment::where('booking_id', $id)->first();
        $bookingDetails = BookingDetail::find($id);

        try {
            $payment = $this->razorpay->payment->fetch($paymentDetails->transaction_id);
            // $payment = $this->razorpay->payment->fetch('pay_QZrRhS4ID0EN6J');

            // if ($payment->status === 'authorized') {
            //     $payment = $payment->capture(['amount' => $payment->amount]);
            // }
            $amount = $payment->amount / 100; // paisa to rupee
            $refund = $payment->refund([
                'amount' => $amount ? $amount : null
            ]);

            if ($refund->status === 'error') {
                return response()->json([
                    'status' => false,
                    'message' => 'Refund failed: ' . $refund->message,
                ], 500);
            }

            $bookingDetails = BookingDetail::find($id);
            $bookingDetails->status = Type::REFUND;
            $bookingDetails->refund = $amount ?? 0;
            $bookingDetails->save();

            $bookingPaymentDetails = Payment::where('booking_id', $id)->first();
            $bookingPaymentDetails->status = Type::REFUND;
            $bookingPaymentDetails->save();

            return response()->json([
                'status' => true,
                'message' => 'Amount refunded successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Refund failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
