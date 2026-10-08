<?php

namespace App\Mail\Api;

use App\Models\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $user_name;
    public $otp;
    public $subject_title;
    /**
     * Create a new message instance.
     */
    public function __construct($user_name, $otp, $subject_title)
    {
        $this->user_name = $user_name;
        $this->otp = $otp;
        $this->subject_title = $subject_title;
    }

    public function build()
    {
        $envoirements = Environment::whereIn('title', [
            'smtpusername',
            'fromaddress',
            'fromname'
        ])->pluck('value', 'title');
        return $this->from(($envoirements['fromaddress'] ?? $envoirements['smtpusername']), ($envoirements['fromname'] ?? ''))
            ->subject($this->subject_title)
            ->view('mail.Api.reset-password', [
                'user_name' => $this->user_name,
                'otp' => $this->otp
            ]);
    }
}
