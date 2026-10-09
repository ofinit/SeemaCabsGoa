<?php

namespace App\Services\Ads;

use App\Enums\Type;
use App\Models\AdCampaign;
use App\Models\Environment;
use App\Models\FleetOperator;
use App\Models\Payment;
use App\Services\CashfreeService;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use RuntimeException;

/**
 * Ad payments on Seema Holidays' own gateway (plan §12), with the same
 * safeguards as rides: the amount comes from the stored campaign, payment is
 * confirmed with the gateway (never the browser), and OfinIT's share + GST is
 * split out — Razorpay Route transfer to OfinIT's linked account, or a
 * Cashfree Easy Split to OfinIT's vendor id (both configured on the fleet
 * operator). Refunds reverse OfinIT's share proportionally.
 */
class AdPaymentService
{
    private const TOLERANCE_PAISE = 100;

    /** Cashfree order ids for ads: ADS_<campaign id>_<timestamp>. */
    public const CASHFREE_PREFIX = 'ADS_';

    public function createRazorpayOrder(AdCampaign $campaign): array
    {
        $order = $this->razorpay()->order->create([
            'amount' => (int) $campaign->total_amount,
            'currency' => 'INR',
            'payment_capture' => 1,
            'receipt' => $campaign->reference,
            'notes' => ['ad_campaign' => $campaign->reference, 'purpose' => 'Advertising'],
        ]);
        $campaign->forceFill(['payment_gateway' => 'razorpay', 'pg_order_id' => $order->id])->save();

        return ['order_id' => $order->id, 'amount' => $order->amount, 'currency' => $order->currency];
    }

    public function createCashfreeOrder(AdCampaign $campaign): array
    {
        $cashfree = app(CashfreeService::class);
        if (!$cashfree->isConfigured()) {
            throw new RuntimeException('Cashfree is not configured.');
        }

        $orderId = self::CASHFREE_PREFIX . $campaign->id . '_' . time();
        $vendorId = trim((string) FleetOperator::orderBy('id')->value('cashfree_vendor_id'));
        $splits = ($vendorId !== '' && $campaign->ofinit_total > 0)
            ? [['vendor_id' => $vendorId, 'amount' => round(min($campaign->ofinit_total, $campaign->total_amount) / 100, 2)]]
            : null;
        $advertiser = $campaign->advertiser;

        $order = $cashfree->createOrder(
            $orderId,
            $campaign->total_amount / 100,
            [
                'id' => 'adv_' . $advertiser->id,
                'name' => $advertiser->contact_name,
                'email' => $advertiser->email,
                'phone' => $advertiser->phone,
            ],
            $splits,
            route('customer.ads.cashfree-return') . '?order_id={order_id}',
        );
        $campaign->forceFill(['payment_gateway' => 'cashfree', 'pg_order_id' => $orderId])->save();

        return [
            'order_id' => $order['order_id'] ?? $orderId,
            'payment_session_id' => $order['payment_session_id'] ?? null,
            'mode' => $cashfree->getMode(),
        ];
    }

    /**
     * Confirms with the gateway that the campaign is paid.
     *
     * @return array{ok: bool, message: string, transaction_id?: string}
     */
    public function verify(AdCampaign $campaign, string $gateway, ?string $reference): array
    {
        try {
            return $gateway === 'cashfree'
                ? $this->verifyCashfree($campaign, $reference ?: $campaign->pg_order_id)
                : $this->verifyRazorpay($campaign, $reference);
        } catch (\Throwable $e) {
            Log::error("Ad payment verification error for {$campaign->reference}: " . $e->getMessage());

            return ['ok' => false, 'message' => 'We could not verify your payment yet. If money was deducted, it will be confirmed automatically or refunded.'];
        }
    }

    private function verifyRazorpay(AdCampaign $campaign, ?string $paymentId): array
    {
        if (empty($paymentId) || !str_starts_with($paymentId, 'pay_')) {
            return ['ok' => false, 'message' => 'Invalid payment reference.'];
        }
        if ($this->referenceInUse($campaign, $paymentId)) {
            return ['ok' => false, 'message' => 'This payment is already linked to something else.'];
        }

        $payment = $this->razorpay()->payment->fetch($paymentId);
        if (($payment->order_id ?? null) !== $campaign->pg_order_id) {
            return ['ok' => false, 'message' => 'This payment does not belong to this ad.'];
        }
        if (!in_array($payment->status, ['captured', 'authorized'], true)) {
            return ['ok' => false, 'message' => 'Payment is not completed (status: ' . $payment->status . ').'];
        }
        if (strtoupper($payment->currency) !== 'INR' || (int) $payment->amount + self::TOLERANCE_PAISE < (int) $campaign->total_amount) {
            return ['ok' => false, 'message' => 'Paid amount does not match the ad amount.'];
        }

        return ['ok' => true, 'message' => 'Verified', 'transaction_id' => $paymentId];
    }

    private function verifyCashfree(AdCampaign $campaign, ?string $orderId): array
    {
        if (empty($orderId) || !str_starts_with($orderId, self::CASHFREE_PREFIX . $campaign->id . '_')) {
            return ['ok' => false, 'message' => 'This payment does not belong to this ad.'];
        }
        if ($this->referenceInUse($campaign, $orderId)) {
            return ['ok' => false, 'message' => 'This payment is already linked to something else.'];
        }

        $order = app(CashfreeService::class)->getOrder($orderId);
        if (strtoupper((string) ($order['order_status'] ?? '')) !== 'PAID') {
            return ['ok' => false, 'message' => 'Payment is not completed (status: ' . ($order['order_status'] ?? 'unknown') . ').'];
        }
        $paid = (int) round(((float) ($order['order_amount'] ?? 0)) * 100);
        if (strtoupper((string) ($order['order_currency'] ?? 'INR')) !== 'INR' || $paid + self::TOLERANCE_PAISE < (int) $campaign->total_amount) {
            return ['ok' => false, 'message' => 'Paid amount does not match the ad amount.'];
        }
        if ($campaign->pg_order_id !== $orderId) {
            $campaign->forceFill(['pg_order_id' => $orderId, 'payment_gateway' => 'cashfree'])->save();
        }

        return ['ok' => true, 'message' => 'Verified', 'transaction_id' => $orderId];
    }

    /**
     * Looks up an unconfirmed order at the gateway (browser closed after
     * paying, no webhook). Returns the verified transaction id, or null.
     */
    public function reconcile(AdCampaign $campaign): ?string
    {
        if ($campaign->isPaid() || !$campaign->pg_order_id) {
            return null;
        }
        try {
            if ($campaign->payment_gateway === 'cashfree') {
                $result = $this->verifyCashfree($campaign, $campaign->pg_order_id);

                return $result['ok'] ? $result['transaction_id'] : null;
            }
            foreach ($this->razorpay()->order->fetch($campaign->pg_order_id)->payments()->items ?? [] as $payment) {
                if (in_array($payment->status, ['captured', 'authorized'], true)) {
                    $result = $this->verifyRazorpay($campaign, $payment->id);
                    if ($result['ok']) {
                        return $result['transaction_id'];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::info("Ad payment reconcile skipped for {$campaign->reference}: " . $e->getMessage());
        }

        return null;
    }

    private function referenceInUse(AdCampaign $campaign, string $reference): bool
    {
        return AdCampaign::where('id', '!=', $campaign->id)
            ->where(fn ($q) => $q->where('transaction_id', $reference)->orWhere('pg_order_id', $reference))->exists()
            || Payment::where('transaction_id', $reference)->orWhere('pg_order_id', $reference)->exists();
    }

    /**
     * Splits OfinIT's share + GST out of a paid campaign. Idempotent.
     *
     * @return array{ok: bool, message: string}
     */
    public function transfer(AdCampaign $campaign, bool $isRetry = false): array
    {
        if (!$campaign->isPaid()) {
            return ['ok' => false, 'message' => 'Ad is not paid.'];
        }
        if ($campaign->transfer_reference) {
            return ['ok' => true, 'message' => 'Already transferred (' . $campaign->transfer_reference . ').'];
        }
        $amount = (int) min($campaign->ofinit_total, $campaign->total_amount);
        if ($amount <= 0) {
            return $this->recordTransfer($campaign, false, null, 0, 'No OfinIT share on this ad.');
        }
        $operator = FleetOperator::orderBy('id')->first();
        if (!$operator) {
            return $this->recordTransfer($campaign, false, null, $amount, 'No fleet operator (payment account) is set up.');
        }

        try {
            return $campaign->payment_gateway === 'cashfree'
                ? $this->cashfreeSplit($campaign, $operator, $amount, $isRetry)
                : $this->razorpayTransfer($campaign, $operator, $amount);
        } catch (\Throwable $e) {
            Log::warning("OfinIT ad share transfer failed for {$campaign->reference}: " . $e->getMessage());

            return $this->recordTransfer($campaign, false, null, $amount, $e->getMessage());
        }
    }

    private function razorpayTransfer(AdCampaign $campaign, FleetOperator $operator, int $amount): array
    {
        $account = trim((string) $operator->razorpay_account);
        if ($account === '') {
            return $this->recordTransfer($campaign, false, null, $amount, "OfinIT's Razorpay linked account (acc_…) is not set on the fleet operator.");
        }
        $payment = $this->razorpay()->payment->fetch($campaign->transaction_id);
        if ($payment->status === 'authorized') {
            $payment = $payment->capture(['amount' => $payment->amount, 'currency' => 'INR']);
        }
        if ($payment->status !== 'captured') {
            return $this->recordTransfer($campaign, false, null, $amount, "Payment is {$payment->status}, not captured.");
        }
        foreach ($payment->transfers()->items ?? [] as $existing) {
            if (($existing->recipient ?? null) === $account && !in_array($existing->status ?? '', ['failed', 'reversed'], true)) {
                return $this->recordTransfer($campaign, true, $existing->id, (int) ($existing->amount ?? 0), null);
            }
        }

        $result = $payment->transfer(['transfers' => [[
            'account' => $account,
            'amount' => $amount,
            'currency' => 'INR',
            'notes' => ['ad_campaign' => $campaign->reference, 'purpose' => 'OfinIT ad platform share incl. GST'],
            'on_hold' => false,
        ]]]);
        $transfer = $result->items[0] ?? null;
        if (!$transfer || empty($transfer->id)) {
            return $this->recordTransfer($campaign, false, null, $amount, 'Razorpay did not return a transfer id.');
        }

        return $this->recordTransfer($campaign, true, $transfer->id, $amount, null);
    }

    private function cashfreeSplit(AdCampaign $campaign, FleetOperator $operator, int $amount, bool $isRetry): array
    {
        $vendorId = trim((string) $operator->cashfree_vendor_id);
        if ($vendorId === '') {
            return $this->recordTransfer($campaign, false, null, $amount, "OfinIT's Cashfree vendor id is not set on the fleet operator.");
        }
        $cashfree = app(CashfreeService::class);
        $order = $cashfree->getOrder($campaign->pg_order_id);
        foreach ((array) ($order['order_splits'] ?? []) as $split) {
            if (($split['vendor_id'] ?? null) === $vendorId) {
                return $this->recordTransfer($campaign, true, 'cf_split:' . $campaign->pg_order_id, (int) round(((float) ($split['amount'] ?? 0)) * 100) ?: $amount, null);
            }
        }
        if (!$isRetry) {
            return $this->recordTransfer($campaign, false, null, $amount, 'Order was created without the OfinIT split; retry from the ad review page.');
        }
        $cashfree->createSplit($campaign->pg_order_id, [['vendor_id' => $vendorId, 'amount' => round($amount / 100, 2)]]);

        return $this->recordTransfer($campaign, true, 'cf_split_after:' . $campaign->pg_order_id, $amount, null);
    }

    private function recordTransfer(AdCampaign $campaign, bool $ok, ?string $reference, int $amount, ?string $error): array
    {
        $campaign->forceFill([
            'transfer_status' => $ok ? 'done' : 'failed',
            'transfer_reference' => $reference,
            'transfer_amount' => $amount,
            'transfer_error' => $error ? mb_substr($error, 0, 500) : null,
            'transferred_at' => $ok ? now() : null,
        ])->save();

        return ['ok' => $ok, 'message' => $ok ? 'Transferred ' . AdCampaign::rupees($amount) . " to OfinIT ({$reference})." : (string) $error];
    }

    /**
     * Refunds part or all of a paid campaign; OfinIT's split is reversed in
     * the same proportion. Returns the refunded amount in paise.
     */
    public function refund(AdCampaign $campaign, int $amount, string $note): int
    {
        if (!$campaign->isPaid()) {
            return 0;
        }
        $amount = min($amount, (int) $campaign->total_amount - (int) $campaign->refund_amount);
        if ($amount <= 0) {
            return 0;
        }
        $ofinitBack = $campaign->transfer_reference
            ? (int) round(((int) $campaign->transfer_amount) * $amount / max(1, (int) $campaign->total_amount))
            : 0;

        try {
            if ($campaign->payment_gateway === 'cashfree') {
                $vendorId = trim((string) FleetOperator::orderBy('id')->value('cashfree_vendor_id'));
                $refund = app(CashfreeService::class)->createRefund(
                    $campaign->pg_order_id,
                    'adref_' . $campaign->id . '_' . time(),
                    round($amount / 100, 2),
                    mb_substr($note, 0, 100),
                    ($vendorId && $ofinitBack > 0) ? [['vendor_id' => $vendorId, 'amount' => round($ofinitBack / 100, 2)]] : null,
                );
                $reference = (string) ($refund['refund_id'] ?? $refund['cf_refund_id'] ?? 'cashfree');
            } else {
                $payment = $this->razorpay()->payment->fetch($campaign->transaction_id);
                $isFull = $amount + (int) $campaign->refund_amount >= (int) $payment->amount;
                if ($ofinitBack > 0 && !$isFull && str_starts_with((string) $campaign->transfer_reference, 'trf_')) {
                    // Partial refund: pull back OfinIT's share of it first.
                    $this->razorpay()->transfer->fetch($campaign->transfer_reference)->reverse(['amount' => $ofinitBack]);
                }
                $refund = $payment->refund(array_filter([
                    'amount' => $amount,
                    'reverse_all' => ($isFull && $campaign->transfer_reference) ? 1 : null,
                    'notes' => ['ad_campaign' => $campaign->reference, 'reason' => mb_substr($note, 0, 200)],
                ]));
                $reference = (string) ($refund->id ?? 'razorpay');
            }
        } catch (\Throwable $e) {
            $campaign->forceFill(['refund_error' => mb_substr($e->getMessage(), 0, 500)])->save();
            throw new RuntimeException('Refund failed: ' . $e->getMessage(), 0, $e);
        }

        $campaign->forceFill([
            'refund_amount' => (int) $campaign->refund_amount + $amount,
            'refund_reference' => $reference,
            'refund_error' => null,
            'refunded_at' => now(),
        ])->save();

        return $amount;
    }

    private function razorpay(): Api
    {
        $keys = Environment::whereIn('title', [Type::PAYMENT_KEY, Type::PAYMENT_SECRETE])->pluck('value', 'title');

        return new Api($keys[Type::PAYMENT_KEY] ?? '', $keys[Type::PAYMENT_SECRETE] ?? '');
    }
}
