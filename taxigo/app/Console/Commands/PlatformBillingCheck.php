<?php

namespace App\Console\Commands;

use App\Services\Platform\PlatformBilling;
use Illuminate\Console\Command;

/** Hourly (and just after midnight): payment links, reminders, suspension after the due date. */
class PlatformBillingCheck extends Command
{
    protected $signature = 'platform:billing {--at-midnight : Run of the day right after midnight (suspension on the 6th)}';

    protected $description = 'Check OfinIT invoice payment links, send reminders and suspend for overdue invoices';

    public function handle(PlatformBilling $billing): int
    {
        $result = $billing->hourly();
        $this->info("Paid via link: {$result['paid']}; notices sent: {$result['notices']}" . ($result['suspended'] ? '; service SUSPENDED.' : '.'));

        return self::SUCCESS;
    }
}
