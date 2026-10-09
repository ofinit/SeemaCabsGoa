<?php

namespace App\Services\Payments;

use App\Enums\Type;
use App\Models\BookingDetail;
use App\Models\Environment;
use App\Models\Payment;
use App\Services\CashfreeService;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

/**
 * Confirms with the payment gateway — never with the client — that a booking
 * has really been paid: the payment exists, succeeded, is in INR, covers the
 * amount the server expects for this booking, and isn't already attached to a
 * different booking.
 */
class PaymentVerifier
{
    /** Allowed shortfall, in paise, for rounding on the gateway side. */
    private const TOLERANCE_PAISE = 100;

    /**
     * @return array{ok: bool, message: string, amount: ?float}
     */
    public function verify(BookingDetail $booking, string $gateway, ?string $transactionId, ?string $pgOrderId, float $expectedRupees): array
    {
        $expected = (int) round($expectedRupees * 100);

        try {
            return $gateway === 'cashfree'
                ? $this->cashfree($booking, $pgOrderId ?: $transactionId, $expected)
                : $this->razorpay($booking, $transactionId, $expected);
        } catch (\Throwable $e) {
            Log::error("Payment verification error for booking {$booking->booking_id}: " . $e->getMessage());

            return $this->fail('We could not verify your payment yet. If money was deducted, it will be confirmed automatically or refunded.');
        }
    }

    private function razorpay(BookingDetail $booking, ?string $paymentId, int $expected): array
    {
        if (empty($paymentId) || !str_starts_with($paymentId, 'pay_')) {
            return $this->fail('Invalid payment reference.');
        }
        if ($this->usedByAnotherBooking($booking, $paymentId)) {
            return $this->fail('This payment is already linked to another booking.');
        }

        $keys = Environment::whereIn('title', [Type::PAYMENT_KEY, Type::PAYMENT_SECRETE])->pluck('value', 'title');
        $payment = (new Api($keys[Type::PAYMENT_KEY] ?? '', $keys[Type::PAYMENT_SECRETE] ?? ''))->payment->fetch($paymentId);

        if (!in_array($payment->status, ['captured', 'authorized'], true)) {
            return $this->fail('Payment is not completed (status: ' . $payment->status . ').');
        }
        if (strtoupper($payment->currency) !== 'INR' || (int) $payment->amount + self::TOLERANCE_PAISE < $expected) {
            Log::warning("Razorpay amount mismatch for {$booking->booking_id}: paid {$payment->amount} paise, expected {$expected}.");

            return $this->fail('Paid amount does not match the booking amount.');
        }

        return ['ok' => true, 'message' => 'Verified', 'amount' => $payment->amount / 100];
    }

    private function cashfree(BookingDetail $booking, ?string $orderId, int $expected): array
    {
        if (empty($orderId)) {
            return $this->fail('Invalid payment reference.');
        }

        // Our Cashfree order ids embed the booking id: CF_<bookingid without dashes>_<timestamp>.
        $embedded = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $booking->booking_id);
        if (!str_starts_with($orderId, 'CF_' . $embedded . '_')) {
            return $this->fail('This payment does not belong to this booking.');
        }
        if ($this->usedByAnotherBooking($booking, $orderId)) {
            return $this->fail('This payment is already linked to another booking.');
        }

        $order = app(CashfreeService::class)->getOrder($orderId);
        if (strtoupper((string) ($order['order_status'] ?? '')) !== 'PAID') {
            return $this->fail('Payment is not completed (status: ' . ($order['order_status'] ?? 'unknown') . ').');
        }

        $paid = (int) round(((float) ($order['order_amount'] ?? 0)) * 100);
        if (strtoupper((string) ($order['order_currency'] ?? 'INR')) !== 'INR' || $paid + self::TOLERANCE_PAISE < $expected) {
            Log::warning("Cashfree amount mismatch for {$booking->booking_id}: paid {$paid} paise, expected {$expected}.");

            return $this->fail('Paid amount does not match the booking amount.');
        }

        return ['ok' => true, 'message' => 'Verified', 'amount' => $paid / 100];
    }

    private function usedByAnotherBooking(BookingDetail $booking, string $reference): bool
    {
        return Payment::where('booking_id', '!=', $booking->id)
            ->where(function ($q) use ($reference) {
                $q->where('transaction_id', $reference)->orWhere('pg_order_id', $reference);
            })
            ->exists();
    }

    private function fail(string $message): array
    {
        return ['ok' => false, 'message' => $message, 'amount' => null];
    }
}
