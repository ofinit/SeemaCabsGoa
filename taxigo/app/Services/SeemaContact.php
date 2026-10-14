<?php

namespace App\Services;

use App\Models\Environment;

/** Seema Holidays' contact numbers shown to customers (Admin → Settings → Booking & Cancellation). */
class SeemaContact
{
    /** Environment title of the WhatsApp number that receives booking details. */
    public const WHATSAPP = 'bookingwhatsappnumber';

    /** The number on the marketing site, used until an admin sets one. */
    private const DEFAULT_WHATSAPP = '919765040490';

    /** WhatsApp number as digits with country code (wa.me format), e.g. 919765040490. */
    public static function whatsapp(): string
    {
        $digits = preg_replace('/\D/', '', (string) Environment::where('title', self::WHATSAPP)->value('value'));
        if (strlen($digits) === 10) {
            $digits = '91' . $digits;
        }

        return strlen($digits) >= 11 ? $digits : self::DEFAULT_WHATSAPP;
    }
}
