<?php

namespace App\Services\Payments;

use App\Enums\BookingEnum;
use App\Enums\Type;
use App\Http\Controllers\Api\BookingController;
use App\Models\BookingDetail;
use App\Models\Environment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

/**
 * Confirms bookings whose advance was paid on Razorpay but whose confirmation
 * never reached us (the browser lost its connection after the UPI app, the
 * tab was closed, …). Asks Razorpay for the booking's orders (by our stored
 * order id, and by receipt = booking id) and, if one holds a captured
 * payment, confirms through the normal path (BookingController::
 * updatePaymentStatus → PaymentVerifier), so nothing is trusted blindly and
 * nothing happens twice.
 */
class BookingPaymentRecovery
{
    /** How far back the scheduled check looks for unconfirmed bookings. */
    private const LOOKBACK_HOURS = 72;

    /** Confirm one booking if Razorpay holds a payment for it. */
    public function recover(BookingDetail $booking): bool
    {
        if ((int) $booking->payment_status === Type::ACTIVE) {
            return true;
        }
        $payment = Payment::where('booking_id', $booking->id)->first();
        if (!$payment || (int) $payment->status === Type::REFUND || $payment->payment_gateway === 'cashfree') {
            return false;
        }

        $api = $this->api();
        $orderIds = [];
        if (str_starts_with((string) $payment->pg_order_id, 'order_')) {
            $orderIds[] = $payment->pg_order_id;
        }
        foreach ($api->order->all(['receipt' => $booking->booking_id, 'count' => 10])->items ?? [] as $order) {
            $orderIds[] = $order->id;
        }

        foreach (array_unique($orderIds) as $orderId) {
            foreach ($api->order->fetch($orderId)->payments()->items ?? [] as $paid) {
                if (!in_array($paid->status, ['captured', 'authorized'], true)) {
                    continue;
                }
                $result = app(BookingController::class)->updatePaymentStatus(new Request([
                    'booking_id' => $booking->booking_id,
                    'status' => 1,
                    'transaction_id' => $paid->id,
                    'pg_order_id' => $orderId,
                    'payment_gateway' => 'razorpay',
                ]))->getData(true);
                if (!empty($result['status'])) {
                    Log::info("Booking {$booking->booking_id} confirmed from Razorpay payment {$paid->id} (recovered).");

                    return true;
                }
                Log::warning("Recovered payment {$paid->id} for {$booking->booking_id} not accepted: " . ($result['message'] ?? 'unknown'));
            }
        }

        return false;
    }

    /** Booking for a Razorpay order (our stored order id, else the order's receipt / notes). */
    public function bookingForOrder(string $orderId): ?BookingDetail
    {
        $bookingId = Payment::where('pg_order_id', $orderId)->value('booking_id');
        if ($bookingId) {
            return BookingDetail::find($bookingId);
        }
        $order = $this->api()->order->fetch($orderId);
        $reference = $order->receipt ?: ($order->notes['booking_id'] ?? null);

        return $reference ? BookingDetail::where('booking_id', $reference)->first() : null;
    }

    /**
     * Scheduled: every unconfirmed, uncancelled booking from the last few days.
     *
     * @return array{checked: int, confirmed: int}
     */
    public function recoverRecent(): array
    {
        $checked = $confirmed = 0;
        BookingDetail::query()
            ->where(fn ($q) => $q->whereNull('payment_status')->orWhere('payment_status', '!=', Type::ACTIVE))
            ->where(fn ($q) => $q->whereNull('status')->orWhereNotIn('status', [BookingEnum::CANCEL, BookingEnum::NO_SHOW]))
            ->where('created_at', '>', now()->subHours(self::LOOKBACK_HOURS))
            ->whereExists(fn ($q) => $q->from('payments')->whereColumn('payments.booking_id', 'booking_details.id')
                ->whereNull('payments.deleted_at')->where('payments.status', '!=', Type::REFUND)
                ->where(fn ($g) => $g->whereNull('payments.payment_gateway')->orWhere('payments.payment_gateway', '!=', 'cashfree')))
            ->orderByDesc('id')->limit(100)->get()
            ->each(function (BookingDetail $booking) use (&$checked, &$confirmed) {
                $checked++;
                try {
                    $confirmed += $this->recover($booking) ? 1 : 0;
                } catch (\Throwable $e) {
                    Log::info("Payment recovery skipped for {$booking->booking_id}: " . $e->getMessage());
                }
            });

        return ['checked' => $checked, 'confirmed' => $confirmed];
    }

    protected function api(): Api
    {
        $keys = Environment::whereIn('title', [Type::PAYMENT_KEY, Type::PAYMENT_SECRETE])->pluck('value', 'title');

        return new Api($keys[Type::PAYMENT_KEY] ?? '', $keys[Type::PAYMENT_SECRETE] ?? '');
    }
}
