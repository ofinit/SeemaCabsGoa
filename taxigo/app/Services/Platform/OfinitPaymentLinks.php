<?php

namespace App\Services\Platform;

use App\Models\BusinessProfile;
use App\Models\Invoice;
use Razorpay\Api\Api;
use RuntimeException;

/**
 * Razorpay payment links on OfinIT's own Razorpay account — one per OfinIT →
 * Seema Holidays invoice. The keys are used only here, on the server; the
 * link itself is a Razorpay-hosted page (rzp.io), so nothing of OfinIT's
 * account is used on the Seema Cabs website.
 */
class OfinitPaymentLinks
{
    public function create(Invoice $invoice): Invoice
    {
        $amount = (int) round(((float) $invoice->amount_due) * 100);
        if ($amount <= 0) {
            throw new RuntimeException('Nothing is due on this invoice.');
        }
        $seema = BusinessProfile::supplier();
        $phone = preg_replace('/\D/', '', (string) ($seema->phone ?? ''));
        $attempt = $invoice->payment_link_id ? '-' . substr((string) time(), -5) : '';

        $link = $this->api()->paymentLink->create(array_filter([
            'amount' => $amount,
            'currency' => 'INR',
            'accept_partial' => false,
            'reference_id' => mb_substr($invoice->number . $attempt, 0, 40),
            'description' => mb_substr($invoice->label() . ' ' . $invoice->number . ' — ' . ($invoice->meta['description'] ?? 'OfinIT platform'), 0, 2048),
            'customer' => array_filter([
                'name' => $seema->legal_name ?? 'Seema Holidays',
                'email' => $seema->email ?? null,
                'contact' => $phone ? '+91' . substr($phone, -10) : null,
            ]),
            'notify' => ['sms' => (bool) $phone, 'email' => (bool) ($seema->email ?? null)],
            'reminder_enable' => true,
            'notes' => ['invoice_id' => (string) $invoice->id, 'invoice_number' => (string) $invoice->number, 'platform' => 'seemacabsgoa'],
            'callback_url' => route('admin.billing.index'),
            'callback_method' => 'get',
        ], fn ($v) => $v !== null && $v !== []));

        // Replacing an older link: cancel it so it can't be paid as well.
        if ($invoice->payment_link_id && $invoice->payment_link_id !== $link->id) {
            try {
                $this->api()->paymentLink->fetch($invoice->payment_link_id)->cancel();
            } catch (\Throwable $e) {
                // Already paid, expired or cancelled.
            }
        }

        $invoice->forceFill([
            'payment_link_id' => $link->id,
            'payment_link_url' => $link->short_url,
            'payment_link_status' => $link->status,
        ])->save();

        return $invoice;
    }

    /** @return array{status: string, amount_paid: int, payment_id: ?string} */
    public function status(Invoice $invoice): array
    {
        $link = $this->api()->paymentLink->fetch($invoice->payment_link_id);
        $paymentId = null;
        foreach ((array) ($link->payments ?? []) as $payment) {
            $payment = (array) $payment;
            if (($payment['status'] ?? '') === 'captured') {
                $paymentId = $payment['payment_id'] ?? null;
            }
        }

        return ['status' => (string) $link->status, 'amount_paid' => (int) ($link->amount_paid ?? 0), 'payment_id' => $paymentId];
    }

    public function verifyWebhook(string $body, ?string $signature): bool
    {
        $secret = PlatformSettings::secret(PlatformSettings::RZP_WEBHOOK_SECRET);
        if (!$secret || !$signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $body, $secret), $signature);
    }

    private function api(): Api
    {
        if (!PlatformSettings::razorpayConfigured()) {
            throw new RuntimeException("OfinIT's Razorpay keys are not set (Admin → Platform billing).");
        }

        return new Api(PlatformSettings::get(PlatformSettings::RZP_KEY), PlatformSettings::secret(PlatformSettings::RZP_SECRET));
    }
}
