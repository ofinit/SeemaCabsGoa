<?php

namespace App\Mail;

use App\Models\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/** OfinIT → Seema Holidays billing notices: issued, reminder, due today, suspended, restored. */
class PlatformBillingMail extends Mailable
{
    use Queueable, SerializesModels;

    public const SUBJECTS = [
        'issued' => 'OfinIT invoices for your Seema Cabs Goa platform — pay by the 5th',
        'reminder' => 'Reminder: OfinIT invoice due on the 5th',
        'due_today' => 'Due today: OfinIT invoice — service will be suspended tomorrow if unpaid',
        'suspended' => 'Seema Cabs Goa service suspended — OfinIT invoice unpaid',
        'restored' => 'Seema Cabs Goa service restored — thank you',
    ];

    public function __construct(public string $kind, public Collection $invoices)
    {
    }

    public function build()
    {
        $env = Environment::whereIn('title', ['smtpusername', 'fromname'])->pluck('value', 'title');
        $mail = $this->subject(self::SUBJECTS[$this->kind] ?? 'OfinIT billing')
            ->view('mail.platform-billing', ['kind' => $this->kind, 'invoices' => $this->invoices]);
        if (!empty($env['smtpusername'])) {
            $mail->from($env['smtpusername'], $env['fromname'] ?? 'Seema Cabs Goa');
        }

        return $mail;
    }
}
