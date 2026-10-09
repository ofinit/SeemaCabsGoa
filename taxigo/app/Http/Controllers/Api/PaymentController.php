<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PaymentRequest;
use App\Enums\Type;
use App\Models\BookingDetail;
use App\Models\Environment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentController extends ResponseController
{
    /**
     * Resolve the booking an order is for. Customers may only pay for their
     * own bookings. Returns [booking|null, error|null].
     */
    private function bookingFor(\Illuminate\Http\Request $request): array
    {
        if (!$request->filled('booking_id')) {
            return [null, null];
        }

        $booking = BookingDetail::where('booking_id', $request->booking_id)->first();
        $user = auth()->user() ?? $request->user('sanctum');
        if (!$booking || ($user instanceof User && (int) $user->type === Type::CUSTOMER && (int) $booking->customer_id !== (int) $user->id)) {
            return [null, 'Booking not found.'];
        }

        return [$booking, null];
    }

    /** Amount due online for a booking, as stored server-side (never the client's figure). */
    private function amountDue(BookingDetail $booking): float
    {
        $amount = Payment::where('booking_id', $booking->id)->value('amount');

        return round((float) ($amount ?? $booking->part_payment), 2);
    }

    public function createOrder(PaymentRequest $request)
    {
        $environment = Environment::whereIn('title', ['paymentkey', 'paymentsecrete'])->pluck('value', 'title');
        $key = $environment['paymentkey'] ?? null;
        $secret = $environment['paymentsecrete'] ?? null;

        $api = new \Razorpay\Api\Api($key, $secret);

        [$booking, $error] = $this->bookingFor($request);
        if ($error) {
            return response()->json(['error' => $error], 404);
        }
        // Older app builds don't send booking_id; their amount is still checked
        // against the booking when the payment is confirmed (PaymentVerifier).
        $amount = $booking ? $this->amountDue($booking) : (float) $request->amount;

        try {
            $order = $api->order->create(array_filter([
                'amount' => (int) round($amount * 100),
                'currency' => 'INR',
                'payment_capture' => 1,
                'receipt' => $booking?->booking_id,
                'notes' => $booking ? ['booking_id' => $booking->booking_id] : null,
            ]));

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
            'amount' => 'required_without:booking_id|numeric|min:1',
        ]);

        $cashfree = app(\App\Services\CashfreeService::class);
        if (!$cashfree->isConfigured()) {
            return response()->json(['error' => 'Cashfree is not configured.'], 500);
        }

        [$booking, $error] = $this->bookingFor($request);
        if ($error) {
            return response()->json(['error' => $error], 404);
        }
        $amount = $booking ? $this->amountDue($booking) : (float) $request->amount;

        $bookingIdString = $booking ? preg_replace('/[^A-Za-z0-9_-]/', '', $booking->booking_id) : 'BK';
        $orderId = 'CF_' . $bookingIdString . '_' . time();

        $customerDetails = [
            'id' => $request->customer_id ?? ($booking ? 'cust_' . $booking->customer_id : 'cust_' . time()),
            'name' => $request->name ?? ($booking ? ($booking->name ?? 'Customer') : 'Customer'),
            'email' => $request->email ?? ($booking ? ($booking->email ?? 'guest@seemacabsgoa.com') : 'guest@seemacabsgoa.com'),
            'phone' => $request->phone ?? ($booking ? ($booking->phone_number ?? '9999999999') : '9999999999'),
        ];

        // Easy Split with Fleet Operator if configured. The split comes from the
        // booking's server-side settlement, not from the request.
        $orderSplits = null;
        $settlement = $booking ? json_decode((string) Payment::where('booking_id', $booking->id)->value('amount_settlement'), true) : null;
        $fleetOperatorPayment = (float) ($settlement['fleet_operator_total_payment'] ?? 0);
        $fleetOperator = \App\Models\FleetOperator::orderBy('id', 'ASC')->first();

        if ($fleetOperator && !empty($fleetOperator->cashfree_vendor_id) && $fleetOperatorPayment > 0) {
            // Cashfree vendor split amount cannot exceed total order amount
            $splitAmount = min(round($fleetOperatorPayment, 2), round($amount, 2));
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
                $amount,
                $customerDetails,
                $orderSplits
            );

            return response()->json([
                'order_id' => $order['order_id'] ?? $orderId,
                'cf_order_id' => $order['cf_order_id'] ?? null,
                'payment_session_id' => $order['payment_session_id'] ?? null,
                'order_status' => $order['order_status'] ?? 'ACTIVE',
                'order_amount' => $order['order_amount'] ?? $amount,
                'order_currency' => $order['order_currency'] ?? 'INR',
                'mode' => $cashfree->getMode(),
            ]);
        } catch (\Exception $e) {
            Log::error('Cashfree Create Order Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
