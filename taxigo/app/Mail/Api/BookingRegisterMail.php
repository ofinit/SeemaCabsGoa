<?php

namespace App\Mail\Api;

use App\Models\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user_name;
    public $password;
    public $subject_title;
    /**
     * Create a new message instance.
     */
    public function __construct($user_name, $password, $subject_title)
    {
        $this->user_name = $user_name;
        $this->password = $password;
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
            ->view('mail.Api.booking-register', [
                'user_name' => $this->user_name,
                'password' => $this->password,
            ]);
    }
}
