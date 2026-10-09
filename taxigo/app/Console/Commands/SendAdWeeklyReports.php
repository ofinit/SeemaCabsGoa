<?php

namespace App\Console\Commands;

use App\Services\Ads\AdReporting;
use Illuminate\Console\Command;

/** Monday morning: email every advertiser last week's results (if enabled). */
class SendAdWeeklyReports extends Command
{
    protected $signature = 'ads:weekly-reports';

    protected $description = 'Email advertisers their ad results for the last 7 days';

    public function handle(AdReporting $reporting): int
    {
        $this->info('Sent ' . $reporting->sendWeeklyReports() . ' weekly ad report(s).');

        return self::SUCCESS;
    }
}
