<?php

namespace App\Mail\Api;

use App\Models\Environment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
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
        return $this->subject($this->subject_title)
            ->view('mail.Api.order-place', [
                'bookingDetails' => $this->bookingDetails
            ])->from($envoirements['smtpusername'], ($envoirements['fromname'] ?? ''));

    }
}
