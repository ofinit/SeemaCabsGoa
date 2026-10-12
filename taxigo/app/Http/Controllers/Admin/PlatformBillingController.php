<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Platform\OfinitPaymentLinks;
use App\Services\Platform\PlatformBilling;
use App\Services\Platform\PlatformSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

/**
 * OfinIT → Seema Holidays billing. The Billing page is for both logins (Seema
 * pays from it); settings, suspension and payment recording are OfinIT's
 * (admin) only — enforced again by the admin.only middleware.
 */
class PlatformBillingController extends Controller
{
    public function __construct(private readonly PlatformBilling $billing)
    {
    }

    private function isAdmin(): bool
    {
        return (int) optional(Auth::user())->type === Type::ADMIN;
    }

    private function authorizeAdmin(): void
    {
        abort_unless($this->isAdmin(), 403);
    }

    public function index()
    {
        return view('platform.billing', [
            'documents' => PlatformBilling::documents()->where('status', Invoice::ISSUED)->limit(60)->get(),
            'drafts' => $this->isAdmin() ? PlatformBilling::documents()->where('status', Invoice::DRAFT)->get() : collect(),
            'unpaid' => PlatformBilling::unpaid()->orderBy('due_date')->get(),
            'isAdmin' => $this->isAdmin(),
            'suspended' => PlatformSettings::suspended(),
            'suspendedAt' => PlatformSettings::get(PlatformSettings::SUSPENDED_AT),
            'suspendedReason' => PlatformSettings::get(PlatformSettings::SUSPENDED_REASON),
            'mode' => PlatformSettings::mode(),
            'billingEmails' => PlatformSettings::get(PlatformSettings::BILLING_EMAILS, ''),
            'razorpayKey' => PlatformSettings::get(PlatformSettings::RZP_KEY),
            'razorpayReady' => PlatformSettings::razorpayConfigured(),
            'webhookReady' => (bool) PlatformSettings::secret(PlatformSettings::RZP_WEBHOOK_SECRET),
            'seemaEmails' => PlatformBilling::seemaEmails(),
        ]);
    }

    /** An OfinIT → Seema document, viewable by both logins. */
    public function invoice(Invoice $invoice)
    {
        abort_unless(PlatformBilling::documents()->whereKey($invoice->id)->where('status', Invoice::ISSUED)->exists() || $this->isAdmin(), 404);
        $invoice->load('lines', 'related');

        return view('invoices.document', compact('invoice'));
    }

    public function saveSettings(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'mode' => 'required|in:split,invoice',
            'billing_emails' => 'nullable|string|max:500',
            'razorpay_key' => ['nullable', 'string', 'max:60', 'regex:/^rzp_(live|test)_[A-Za-z0-9]+$/'],
            'razorpay_secret' => 'nullable|string|max:100',
            'webhook_secret' => 'nullable|string|max:100',
        ]);
        $emails = array_values(array_filter(array_map('trim', preg_split('/[,;\s]+/', (string) ($data['billing_emails'] ?? '')))));
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return back()->with('error', "\"{$email}\" is not a valid email address.")->withInput();
            }
        }
        PlatformSettings::set(PlatformSettings::MODE, $data['mode']);
        PlatformSettings::set(PlatformSettings::BILLING_EMAILS, implode(', ', array_unique(array_map('strtolower', $emails))));
        if (!empty($data['razorpay_key'])) {
            PlatformSettings::set(PlatformSettings::RZP_KEY, $data['razorpay_key']);
        }
        PlatformSettings::setSecret(PlatformSettings::RZP_SECRET, $data['razorpay_secret'] ?? null);
        PlatformSettings::setSecret(PlatformSettings::RZP_WEBHOOK_SECRET, $data['webhook_secret'] ?? null);

        return back()->with('success', 'Platform billing settings saved.');
    }

    /** Issue a month's invoices and create the payment links now (normally automatic on the 1st). */
    public function run(Request $request)
    {
        $this->authorizeAdmin();
        $request->validate(['month' => 'required|date_format:Y-m']);
        $result = $this->billing->monthlyRun(Carbon::createFromFormat('Y-m', $request->input('month'), 'Asia/Kolkata')->startOfMonth());

        $message = ($result['issued'] ? 'Issued ' . implode(', ', $result['issued']) . '. ' : 'Nothing new to issue. ')
            . ($result['links'] ? "{$result['links']} payment link(s) created." : '');

        return back()->with($result['problems'] ? 'error' : 'success', trim($message . ($result['problems'] ? ' Problems: ' . implode(' | ', $result['problems']) : '')));
    }


    public function recordPayment(Request $request, Invoice $invoice)
    {
        $this->authorizeAdmin();
        abort_unless(in_array($invoice->type, PlatformBilling::DOC_TYPES, true), 404);
        $data = $request->validate(['reference' => 'required|string|max:80', 'paid_on' => 'required|date|before_or_equal:today']);
        $this->billing->markPaid($invoice, 'bank', $data['reference'], Auth::id(), Carbon::parse($data['paid_on'], 'Asia/Kolkata'));

        return back()->with('success', "{$invoice->number} recorded as paid." . (PlatformSettings::suspended() ? ' Other invoices are still overdue, so the service stays suspended.' : ''));
    }

    public function createLink(Invoice $invoice, OfinitPaymentLinks $links)
    {
        $this->authorizeAdmin();
        abort_unless(in_array($invoice->type, PlatformBilling::DOC_TYPES, true) && !$invoice->paid_at, 404);
        try {
            $this->billing->settle($invoice);
            $links->create($invoice->fresh());
        } catch (RuntimeException|\Throwable $e) {
            return back()->with('error', 'Payment link not created: ' . $e->getMessage());
        }

        return back()->with('success', "Payment link created for {$invoice->number}.");
    }

    public function checkLink(Invoice $invoice)
    {
        $this->authorizeAdmin();
        try {
            $paid = $this->billing->refreshLink($invoice);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not check the payment link: ' . $e->getMessage());
        }

        return back()->with('success', $paid ? "{$invoice->number} is paid." : "{$invoice->number} is not paid yet (" . $invoice->fresh()->payment_link_status . ').');
    }
}
