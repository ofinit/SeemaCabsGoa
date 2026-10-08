<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PaymentRequest;
use App\Models\Environment;
use Illuminate\Support\Facades\Log;

class PaymentController extends ResponseController
{
    public function createOrder(PaymentRequest $request)
    {
        $environment = Environment::whereIn('title', ['paymentkey', 'paymentsecrete'])->pluck('value', 'title');
        $key = $environment['paymentkey'] ?? null;
        $secret = $environment['paymentsecrete'] ?? null;

        $api = new \Razorpay\Api\Api($key, $secret);

        try {
            $order = $api->order->create([
                'amount' => intval($request->amount * 100),
                'currency' => 'INR',
                'payment_capture' => 1,
            ]);

            return response()->json([
                'order_id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createCashfreeOrder(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $cashfree = app(\App\Services\CashfreeService::class);
        if (!$cashfree->isConfigured()) {
            return response()->json(['error' => 'Cashfree is not configured.'], 500);
        }

        $booking = null;
        if ($request->filled('booking_id')) {
            $booking = \App\Models\BookingDetail::where('booking_id', $request->booking_id)->first();
        }

        $bookingIdString = $booking ? preg_replace('/[^A-Za-z0-9_-]/', '', $booking->booking_id) : 'BK';
        $orderId = 'CF_' . $bookingIdString . '_' . time();

        $customerDetails = [
            'id' => $request->customer_id ?? ($booking ? 'cust_' . $booking->customer_id : 'cust_' . time()),
            'name' => $request->name ?? ($booking ? ($booking->name ?? 'Customer') : 'Customer'),
            'email' => $request->email ?? ($booking ? ($booking->email ?? 'guest@seemacabsgoa.com') : 'guest@seemacabsgoa.com'),
            'phone' => $request->phone ?? ($booking ? ($booking->phone_number ?? '9999999999') : '9999999999'),
        ];

        // Easy Split with Fleet Operator if configured
        $orderSplits = null;
        $fleetOperatorPayment = $request->fleet_operator_payment ?? ($booking ? $booking->fleet_operator_payment : 0);
        $fleetOperator = \App\Models\FleetOperator::orderBy('id', 'ASC')->first();

        if ($fleetOperator && !empty($fleetOperator->cashfree_vendor_id) && $fleetOperatorPayment > 0) {
            // Cashfree vendor split amount cannot exceed total order amount
            $splitAmount = min(round((float) $fleetOperatorPayment, 2), round((float) $request->amount, 2));
            if ($splitAmount > 0) {
                $orderSplits = [
                    [
                        'vendor_id' => $fleetOperator->cashfree_vendor_id,
                        'amount' => $splitAmount,
                    ]
                ];
            }
        }

        try {
            $order = $cashfree->createOrder(
                $orderId,
                (float) $request->amount,
                $customerDetails,
                $orderSplits
            );

            return response()->json([
                'order_id' => $order['order_id'] ?? $orderId,
                'cf_order_id' => $order['cf_order_id'] ?? null,
                'payment_session_id' => $order['payment_session_id'] ?? null,
                'order_status' => $order['order_status'] ?? 'ACTIVE',
                'order_amount' => $order['order_amount'] ?? $request->amount,
                'order_currency' => $order['order_currency'] ?? 'INR',
                'mode' => $cashfree->getMode(),
            ]);
        } catch (\Exception $e) {
            Log::error('Cashfree Create Order Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
