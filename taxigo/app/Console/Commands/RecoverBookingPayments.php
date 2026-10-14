<?php

namespace App\Console\Commands;

use App\Services\Payments\BookingPaymentRecovery;
use Illuminate\Console\Command;

/** Every 5 minutes: confirm bookings paid on Razorpay whose confirmation never reached us. */
class RecoverBookingPayments extends Command
{
    protected $signature = 'payments:recover';

    protected $description = 'Confirm bookings whose Razorpay payment was captured but never confirmed';

    public function handle(BookingPaymentRecovery $recovery): int
    {
        $result = $recovery->recoverRecent();
        if ($result['confirmed']) {
            $this->info("Confirmed {$result['confirmed']} of {$result['checked']} unconfirmed booking(s).");
        }

        return self::SUCCESS;
    }
}
