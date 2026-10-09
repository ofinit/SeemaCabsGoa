<?php

namespace App\Mail;

use App\Models\Environment;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Sends a customer their GST document as a secure link. */
class InvoiceIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
    }

    public function build()
    {
        $env = Environment::whereIn('title', ['smtpusername', 'fromname'])->pluck('value', 'title');
        $mail = $this->subject($this->invoice->label() . ' ' . $this->invoice->number . ' — Seema Cabs Goa')
            ->view('mail.invoice-issued', ['invoice' => $this->invoice]);

        if (!empty($env['smtpusername'])) {
            $mail->from($env['smtpusername'], $env['fromname'] ?? 'Seema Cabs Goa');
        }

        return $mail;
    }
}
