<?php

namespace App\Mail;

use App\Models\AdCampaign;
use App\Models\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/** Weekly or final results of a self-serve ad, sent to the advertiser. */
class AdReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdCampaign $campaign,
        public string $kind,
        public array $stats,
        public string $periodFrom,
        public string $periodTo,
    ) {
    }

    public function build()
    {
        $env = Environment::whereIn('title', ['smtpusername', 'fromname'])->pluck('value', 'title');
        $subject = ($this->kind === 'final' ? 'Final results' : 'Weekly results') . ' for your ad ' . $this->campaign->reference . ' — Seema Cabs Goa';
        $mail = $this->subject($subject)->view('mail.ad-report', [
            'campaign' => $this->campaign,
            'kind' => $this->kind,
            'stats' => $this->stats,
            'from' => $this->periodFrom,
            'to' => $this->periodTo,
        ]);
        if (!empty($env['smtpusername'])) {
            $mail->from($env['smtpusername'], $env['fromname'] ?? 'Seema Cabs Goa');
        }

        return $mail;
    }
}
