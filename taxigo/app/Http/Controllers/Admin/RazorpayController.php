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

                $refundResult = $cashfree->createRefund($orderId, $refundId, $refundAmount, 'Admin booking cancellation refund');
                $amount = $refundAmount;
            } else {
                $payment = $this->razorpay->payment->fetch($paymentDetails->transaction_id);
                Log::info(json_encode($payment));

                $amountPaisa = $payment->amount;
                $refund = $payment->refund([
                    'amount' => $amountPaisa ? $amountPaisa : null
                ]);

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


    public function transferAmount($paymentId, $amount)
    {
        try {
            $transfer = $this->razorpay->payment->fetch($paymentId)->transfer([
                [
                    'account' => 'acc_vendor123',
                    'amount' => $amount,
                    'currency' => 'INR',
                    'notes' => ['purpose' => 'Vendor payout'],
                    'on_hold' => false,
                ]
            ]);

            if ($transfer->status === 'error') {
                Log::info('Transfer amount failed : ' . $transfer->message);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::info('Transfer amount failed : ' . $e->getMessage());
            return false;
        }
    }
}
