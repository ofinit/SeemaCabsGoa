<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

/**
 * Customer-facing views of issued GST documents:
 *  - signed, login-free link (emails, mobile apps) — route `invoices.public`
 *  - PWA link for the logged-in customer who owns the booking
 */
class InvoiceDocumentController extends Controller
{
    public function signed(Invoice $invoice)
    {
        abort_unless($invoice->isIssued(), 404);

        return $this->render($invoice);
    }

    public function customer(Invoice $invoice)
    {
        $customerId = auth('customer')->id();
        abort_unless(
            $invoice->isIssued() && $customerId && (
                (int) optional($invoice->booking)->customer_id === (int) $customerId
                || (int) optional($invoice->adCampaign)->user_id === (int) $customerId
            ),
            404
        );

        return $this->render($invoice);
    }

    private function render(Invoice $invoice)
    {
        $invoice->load('lines', 'booking', 'related', 'adCampaign');

        return response()
            ->view('invoices.document', compact('invoice'))
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'private, no-store');
    }
}
