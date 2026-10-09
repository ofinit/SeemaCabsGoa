<?php

namespace App\Services\Payments;

use App\Enums\Type;
use App\Models\BookingDetail;
use App\Models\Environment;
use App\Models\FleetOperator;
use App\Models\Payment;
use App\Services\CashfreeService;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

/**
 * Pays the fleet operator its share of a booking's online advance
 * (settlement `fleet_operator_total_payment`), through the gateway the
 * customer paid with:
 *
 *   Razorpay — Route transfer from the payment to the operator's linked
 *              account (`fleet_operators.razorpay_account`).
 *   Cashfree — Easy Split to the operator's vendor (`cashfree_vendor_id`).
 *              Normally attached when the order is created; a retry uses
 *              Split After Payment (must be enabled on the Cashfree account).
 *
 * OfinIT's fee + its GST, and any ride GST in the advance, stay in the
 * merchant account. Idempotent: a recorded gateway reference means "done",
 * and existing Razorpay transfers on the payment are detected before
 * creating a new one.
 */
class FleetPayoutService
{
    /**
     * @return array{ok: bool, message: string}
     */
    public function payout(BookingDetail $booking, ?Payment $payment = null, bool $isRetry = false): array
    {
        $payment = $payment ?? Payment::where('booking_id', $booking->id)->where('status', Type::PAID)->orderBy('id')->first();
        if (!$payment || (int) $payment->status !== Type::PAID) {
            return ['ok' => false, 'message' => 'Booking is not paid.'];
        }
        if (!empty($payment->transfer_reference)) {
            return ['ok' => true, 'message' => 'Already paid out (' . $payment->transfer_reference . ').'];
        }

        $amount = $this->amountFor($payment);
        if ($amount <= 0) {
            return $this->record($payment, false, null, 0, 'No fleet-operator share on this booking.');
        }

        $operator = FleetOperator::orderBy('id')->first();
        if (!$operator) {
            return $this->record($payment, false, null, $amount, 'No active fleet operator is set up.');
        }

        try {
            return ($payment->payment_gateway === 'cashfree')
                ? $this->cashfree($payment, $operator, $amount, $isRetry)
                : $this->razorpay($payment, $operator, $amount);
        } catch (\Throwable $e) {
            Log::warning("Fleet payout failed for booking {$booking->booking_id}: " . $e->getMessage());

            return $this->record($payment, false, null, $amount, $e->getMessage());
        }
    }

    private function razorpay(Payment $payment, FleetOperator $operator, float $amount): array
    {
        $account = trim((string) $operator->razorpay_account);
        if ($account === '') {
            return $this->record($payment, false, null, $amount, 'Fleet operator has no Razorpay linked account (acc_…).');
        }
        if (empty($payment->transaction_id) || !str_starts_with($payment->transaction_id, 'pay_')) {
            return $this->record($payment, false, null, $amount, 'No Razorpay payment id recorded for this booking.');
        }

        $keys = Environment::whereIn('title', [Type::PAYMENT_KEY, Type::PAYMENT_SECRETE])->pluck('value', 'title');
        $api = new Api($keys[Type::PAYMENT_KEY] ?? '', $keys[Type::PAYMENT_SECRETE] ?? '');
        $rzpPayment = $api->payment->fetch($payment->transaction_id);

        // Orders are created with payment_capture=1, so payments are normally
        // captured already; only capture the rare "authorized" one.
        if ($rzpPayment->status === 'authorized') {
            $rzpPayment = $rzpPayment->capture(['amount' => $rzpPayment->amount, 'currency' => 'INR']);
        }
        if ($rzpPayment->status !== 'captured') {
            return $this->record($payment, false, null, $amount, "Payment is {$rzpPayment->status}, not captured.");
        }

        // A transfer may already exist (e.g. an earlier attempt succeeded but
        // its response was misread) — reuse it rather than paying twice.
        foreach ($rzpPayment->transfers()->items ?? [] as $existing) {
            if (($existing->recipient ?? null) === $account && !in_array($existing->status ?? '', ['failed', 'reversed'], true)) {
                return $this->record($payment, true, $existing->id, ($existing->amount ?? 0) / 100, null);
            }
        }

        $paise = (int) round($amount * 100);
        if ($paise > (int) $rzpPayment->amount) {
            return $this->record($payment, false, null, $amount, 'Payout exceeds the captured amount.');
        }

        $result = $rzpPayment->transfer(['transfers' => [[
            'account' => $account,
            'amount' => $paise,
            'currency' => 'INR',
            'notes' => ['booking_id' => (string) optional($payment->bookingDetails)->booking_id, 'purpose' => 'Fleet operator commission'],
            'on_hold' => false,
        ]]]);
        $transfer = $result->items[0] ?? null;
        if (!$transfer || empty($transfer->id)) {
            return $this->record($payment, false, null, $amount, 'Razorpay did not return a transfer id.');
        }

        return $this->record($payment, true, $transfer->id, $amount, null);
    }

    private function cashfree(Payment $payment, FleetOperator $operator, float $amount, bool $isRetry): array
    {
        $vendorId = trim((string) $operator->cashfree_vendor_id);
        if ($vendorId === '') {
            return $this->record($payment, false, null, $amount, 'Fleet operator has no Cashfree vendor id.');
        }
        $orderId = $payment->pg_order_id ?: $payment->transaction_id;
        if (empty($orderId)) {
            return $this->record($payment, false, null, $amount, 'No Cashfree order id recorded for this booking.');
        }

        $cashfree = app(CashfreeService::class);
        $order = $cashfree->getOrder($orderId);
        if (strtoupper((string) ($order['order_status'] ?? '')) !== 'PAID') {
            return $this->record($payment, false, null, $amount, 'Cashfree order is not paid.');
        }

        // Split attached at order creation: Cashfree settles it automatically.
        foreach ((array) ($order['order_splits'] ?? []) as $split) {
            if (($split['vendor_id'] ?? null) === $vendorId) {
                return $this->record($payment, true, 'cf_split:' . $orderId, (float) ($split['amount'] ?? $amount), null);
            }
        }

        // No split on the order: only an admin retry creates one after payment
        // (Cashfree asks for ~2 minutes after payment, and the feature must be enabled).
        if (!$isRetry) {
            return $this->record($payment, false, null, $amount, 'Order was created without a vendor split; retry from Admin → Fleet Payouts.');
        }

        $cashfree->createSplit($orderId, [['vendor_id' => $vendorId, 'amount' => round($amount, 2)]]);

        return $this->record($payment, true, 'cf_split_after:' . $orderId, $amount, null);
    }

    private function amountFor(Payment $payment): float
    {
        $settlement = json_decode((string) $payment->amount_settlement, true) ?: [];
        $amount = (float) str_replace(',', '', (string) ($settlement['fleet_operator_total_payment'] ?? 0));

        return max(0, min(round($amount, 2), (float) $payment->amount));
    }

    private function record(Payment $payment, bool $ok, ?string $reference, float $amount, ?string $error): array
    {
        $payment->transfer_amount_status = $ok ? Type::ACTIVE : Type::INACTIVE;
        $payment->transfer_reference = $reference;
        $payment->transfer_amount = $amount;
        $payment->transfer_error = $error ? mb_substr($error, 0, 500) : null;
        $payment->transferred_at = $ok ? now() : null;
        $payment->save();

        return ['ok' => $ok, 'message' => $ok ? "Paid ₹{$amount} to the fleet operator ({$reference})." : (string) $error];
    }
}
