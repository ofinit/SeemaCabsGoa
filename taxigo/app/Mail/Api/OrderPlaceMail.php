<?php

namespace App\Mail\Api;

use App\Models\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlaceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bookingDetails;
    public $subject_title;
    /**
     * Create a new message instance.
     */
    public function __construct($bookingDetails, $subject_title)
    {
        $this->bookingDetails = $bookingDetails;
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
            ->view('mail.Api.order-place', [
                'bookingDetails' => $this->bookingDetails
            ]);
    }
}
