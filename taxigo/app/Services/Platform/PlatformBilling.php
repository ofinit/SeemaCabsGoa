<?php

namespace App\Services\Platform;

use App\Mail\PlatformBillingMail;
use App\Models\AdCampaign;
use App\Models\BusinessProfile;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SettingChange;
use App\Services\Invoicing\InvoiceService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

/**
 * OfinIT → Seema Holidays billing when the gateways don't split payments.
 *
 *   1st 06:00 IST  invoices for last month are issued (ride platform fee + GST,
 *                  ad platform share incl. GST, credit notes); amount due =
 *                  total − anything already collected by split − credit notes;
 *                  a Razorpay payment link (OfinIT's account) per invoice;
 *                  emailed to Seema with the links
 *   3rd, 5th       reminders (the 5th is the due date — no extra grace days)
 *   6th 00:00      anything still unpaid → the service is suspended
 *   on payment     invoice marked paid; service restored once nothing is overdue
 */
class PlatformBilling
{
    /** Notices already sent for an invoice (bit flags on invoices.notices_sent). */
    public const NOTICE_ISSUED = 1;
    public const NOTICE_REMINDER = 2;
    public const NOTICE_DUE_TODAY = 4;

    public const DOC_TYPES = [Invoice::PLATFORM_FEE, Invoice::AD_PLATFORM_FEE];

    public function __construct(
        private readonly InvoiceService $invoices,
        private readonly OfinitPaymentLinks $links,
    ) {
    }

    /** OfinIT → Seema documents (invoices and their credit notes), newest first. */
    public static function documents(): \Illuminate\Database\Eloquent\Builder
    {
        return Invoice::where(fn ($q) => $q->whereIn('type', self::DOC_TYPES)
            ->orWhere(fn ($q) => $q->where('type', Invoice::CREDIT_NOTE)->whereRaw("JSON_EXTRACT(meta, '$.platform') = true")))
            ->orderByDesc('period_start')->orderByDesc('id');
    }

    /** Issued invoices with money still owed. */
    public static function unpaid(): \Illuminate\Database\Eloquent\Builder
    {
        return Invoice::whereIn('type', self::DOC_TYPES)->where('status', Invoice::ISSUED)
            ->whereNull('paid_at')->where('amount_due', '>', 0);
    }

    public static function overdue(): Collection
    {
        return self::unpaid()->whereNotNull('due_date')
            ->where('due_date', '<', now('Asia/Kolkata')->toDateString())->get();
    }

    // ----------------------------------------------------------- monthly run

    /**
     * Issue last month's OfinIT invoices, work out what is due, create the
     * payment links and email Seema. Safe to run again (skips what's done).
     *
     * @return array{issued: string[], links: int, problems: string[]}
     */
    public function monthlyRun(Carbon $month): array
    {
        $problems = [];
        foreach (['platformFeeDraft', 'adPlatformDraft'] as $draft) {
            try {
                $this->invoices->{$draft}($month);
            } catch (RuntimeException $e) {
                // "Already issued" for this month is fine.
                if (!str_contains($e->getMessage(), 'already issued')) {
                    $problems[] = $e->getMessage();
                }
            }
        }

        $start = $month->copy()->startOfMonth()->toDateString();
        $period = self::documents()->whereDate('period_start', $start)->get();
        $issued = [];
        foreach ($period->where('status', Invoice::DRAFT) as $doc) {
            try {
                $this->invoices->issue($doc);
                $issued[] = $doc->fresh()->number;
            } catch (RuntimeException $e) {
                $problems[] = $e->getMessage();
            }
        }

        $links = 0;
        $period = self::documents()->whereDate('period_start', $start)->get();
        foreach ($period->whereIn('type', self::DOC_TYPES)->where('status', Invoice::ISSUED) as $invoice) {
            $this->settle($invoice, $period);
            $invoice->refresh();
            if (!$invoice->paid_at && (float) $invoice->amount_due > 0 && !$invoice->payment_link_url) {
                try {
                    $this->links->create($invoice);
                    $links++;
                } catch (\Throwable $e) {
                    $problems[] = "Payment link for {$invoice->number}: " . $e->getMessage();
                }
            }
        }

        $this->sendNotices();

        return ['issued' => $issued, 'links' => $links, 'problems' => array_values(array_unique($problems))];
    }

    /** Due date, amount collected by split, credit notes and the balance due. */
    public function settle(Invoice $invoice, ?Collection $period = null): void
    {
        if ($invoice->paid_at) {
            return;
        }
        $collected = $this->collectedBySplit($invoice);
        $period ??= self::documents()->whereDate('period_start', optional($invoice->period_start)->toDateString())->get();
        $isAds = $invoice->type === Invoice::AD_PLATFORM_FEE;
        $credits = (float) $period->where('type', Invoice::CREDIT_NOTE)->where('status', Invoice::ISSUED)
            ->filter(fn ($cn) => (bool) ($cn->meta['ads'] ?? false) === $isAds)->sum('total');

        $due = round(max(0, (float) $invoice->total - $collected - $credits), 2);
        $issuedOn = Carbon::parse($invoice->issue_date ?? now('Asia/Kolkata'), 'Asia/Kolkata');
        $dueDate = $issuedOn->copy()->day(PlatformSettings::DUE_DAY);
        // Issued after the 5th (e.g. a late manual run): give the same 4 days.
        if ($issuedOn->day > PlatformSettings::DUE_DAY) {
            $dueDate = $issuedOn->copy()->addDays(4);
        }

        $invoice->forceFill([
            'collected_by_split' => min($collected, (float) $invoice->total),
            'credit_applied' => $credits,
            'amount_due' => $due,
            'due_date' => $invoice->due_date ?? $dueDate->toDateString(),
        ])->save();

        if ($due <= 0) {
            $this->markPaid($invoice, $collected > 0 ? 'split' : 'credit', null, null);
        }
    }

    /** OfinIT's share already received through gateway splits for the invoice lines. */
    private function collectedBySplit(Invoice $invoice): float
    {
        $lines = $invoice->lines()->get();
        if ($invoice->type === Invoice::AD_PLATFORM_FEE) {
            $paise = AdCampaign::whereIn('id', $lines->pluck('ad_campaign_id')->filter())
                ->where('transfer_status', 'done')->sum('transfer_amount');

            return round($paise / 100, 2);
        }

        return round((float) Payment::whereIn('booking_id', $lines->pluck('booking_id')->filter())
            ->whereNotNull('transfer_reference')->where('transfer_amount_status', Type::ACTIVE)->sum('transfer_amount'), 2);
    }

    // ---------------------------------------------------------------- payment

    /** Checks a payment link with Razorpay; marks the invoice paid if it is. */
    public function refreshLink(Invoice $invoice): bool
    {
        if ($invoice->paid_at || !$invoice->payment_link_id) {
            return (bool) $invoice->paid_at;
        }
        $status = $this->links->status($invoice);
        $invoice->forceFill(['payment_link_status' => $status['status']])->save();
        if ($status['status'] === 'paid' && $status['amount_paid'] + 100 >= (int) round(((float) $invoice->amount_due) * 100)) {
            $this->markPaid($invoice, 'razorpay_link', $status['payment_id'] ?? $invoice->payment_link_id, null);

            return true;
        }

        return false;
    }

    public function markPaid(Invoice $invoice, string $via, ?string $reference, ?int $byUserId, ?Carbon $paidOn = null): void
    {
        $invoice->forceFill([
            'paid_at' => $paidOn ?? now(),
            'paid_via' => $via,
            'payment_reference' => $reference ? mb_substr($reference, 0, 80) : null,
        ])->save();
        SettingChange::record("platform_invoice.{$invoice->number}.paid", null, $via . ($reference ? " {$reference}" : ''));
        Log::info("OfinIT invoice {$invoice->number} marked paid via {$via}.");

        if (PlatformSettings::suspended() && self::overdue()->isEmpty()) {
            $this->restore($byUserId, 'All overdue invoices paid');
        }
    }

    // ------------------------------------------------------ notices & checks

    /**
     * Hourly: check unpaid payment links, send the issue / 3rd / 5th notices,
     * and suspend once anything is unpaid after the 5th.
     *
     * @return array{paid: int, notices: int, suspended: bool}
     */
    public function hourly(): array
    {
        $paid = $this->checkLinks();
        $notices = $this->sendNotices();

        $suspended = false;
        if (PlatformSettings::mode() === PlatformSettings::INVOICE && !PlatformSettings::suspended()) {
            // Only invoices Seema was actually sent count towards suspension.
            $overdue = self::overdue()->filter(fn ($i) => ($i->notices_sent & self::NOTICE_ISSUED) === self::NOTICE_ISSUED);
            if ($overdue->isNotEmpty()) {
                $this->suspend(null, 'Unpaid: ' . $overdue->pluck('number')->implode(', '));
                $suspended = true;
            }
        }

        return compact('paid', 'notices', 'suspended');
    }

    /**
     * Ask Razorpay about every unpaid payment link; a captured payment marks the
     * invoice paid and restores the service at once. Runs every minute as the
     * backup for the payment_link.paid webhook.
     */
    public function checkLinks(): int
    {
        if (!PlatformSettings::razorpayConfigured()) {
            return 0;
        }
        $paid = 0;
        foreach (self::unpaid()->whereNotNull('payment_link_id')->get() as $invoice) {
            try {
                $paid += $this->refreshLink($invoice) ? 1 : 0;
            } catch (\Throwable $e) {
                Log::info("Payment link check skipped for {$invoice->number}: " . $e->getMessage());
            }
        }

        return $paid;
    }

    /** Issue email, reminder on the 3rd, final reminder on the 5th (09:00 IST onwards). */
    public function sendNotices(): int
    {
        $today = now('Asia/Kolkata');
        $sent = 0;
        $unpaid = self::unpaid()->get();
        $groups = [
            'issued' => $unpaid->filter(fn ($i) => !($i->notices_sent & self::NOTICE_ISSUED)),
            'reminder' => $today->hour >= 9 ? $unpaid->filter(fn ($i) => $i->due_date && $today->toDateString() >= $i->due_date->copy()->subDays(2)->toDateString()
                && $today->toDateString() < $i->due_date->toDateString() && !($i->notices_sent & self::NOTICE_REMINDER)) : collect(),
            'due_today' => $today->hour >= 9 ? $unpaid->filter(fn ($i) => $i->due_date && $today->toDateString() === $i->due_date->toDateString()
                && !($i->notices_sent & self::NOTICE_DUE_TODAY)) : collect(),
        ];
        $flags = ['issued' => self::NOTICE_ISSUED, 'reminder' => self::NOTICE_REMINDER, 'due_today' => self::NOTICE_DUE_TODAY];
        foreach ($groups as $kind => $invoices) {
            if ($invoices->isEmpty()) {
                continue;
            }
            if ($this->mailSeema($kind, $invoices)) {
                foreach ($invoices as $invoice) {
                    $invoice->forceFill(['notices_sent' => $invoice->notices_sent | $flags[$kind]])->save();
                }
                $sent++;
            }
        }

        return $sent;
    }

    // ------------------------------------------------------------ suspension

    public function suspend(?int $byUserId, string $reason): void
    {
        if (PlatformSettings::suspended()) {
            return;
        }
        PlatformSettings::set(PlatformSettings::SUSPENDED, '1');
        PlatformSettings::set(PlatformSettings::SUSPENDED_AT, now()->toDateTimeString(), false);
        PlatformSettings::set(PlatformSettings::SUSPENDED_REASON, mb_substr($reason, 0, 250), false);
        PlatformSuspension::syncStaticPages();
        Log::warning("Platform suspended: {$reason}");
        $this->mailSeema('suspended', self::overdue());
    }

    public function restore(?int $byUserId, string $reason): void
    {
        if (!PlatformSettings::suspended()) {
            return;
        }
        PlatformSettings::set(PlatformSettings::SUSPENDED, '0');
        PlatformSettings::set(PlatformSettings::SUSPENDED_REASON, mb_substr($reason, 0, 250), false);
        PlatformSuspension::syncStaticPages();
        Log::info("Platform restored: {$reason}");
        $this->mailSeema('restored', collect());
    }

    /**
     * Where Seema's billing emails go: the addresses OfinIT entered on the
     * Billing page, or else Seema's business-profile email. Never every
     * fleet-operator login.
     */
    public static function seemaEmails(): array
    {
        $configured = array_filter(preg_split('/[,;\s]+/', (string) PlatformSettings::get(PlatformSettings::BILLING_EMAILS, '')));

        return collect($configured ?: [BusinessProfile::supplier()?->email])
            ->map(fn ($e) => strtolower(trim((string) $e)))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))->unique()->values()->all();
    }

    private function mailSeema(string $kind, Collection $invoices): bool
    {
        $to = self::seemaEmails();
        if (!$to) {
            Log::warning("Platform billing '{$kind}' email not sent: no Seema Holidays email on file.");

            return false;
        }
        try {
            Mail::to($to)->send(new PlatformBillingMail($kind, $invoices->values()));

            return true;
        } catch (\Throwable $e) {
            Log::warning("Platform billing '{$kind}' email failed: " . $e->getMessage());

            return false;
        }
    }
}
