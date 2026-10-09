<?php

namespace App\Console\Commands;

use App\Services\Ads\AdCampaignService;
use Illuminate\Console\Command;

/**
 * Hourly self-serve ads housekeeping: expire ended ads (final report email),
 * 3-day renewal reminders, recover payments the browser never confirmed,
 * close stale drafts, pause ads with expired licences or broken links.
 */
class MaintainAds extends Command
{
    protected $signature = 'ads:maintain';

    protected $description = 'Expire ended ads, send renewal reminders and reconcile unconfirmed ad payments';

    public function handle(AdCampaignService $ads): int
    {
        $result = $ads->maintain();
        $this->info("Expired {$result['expired']}, reminded {$result['reminded']}, recovered {$result['recovered']} payment(s), closed {$result['stale']} stale draft(s), "
            . "paused {$result['licences']} for expired licences and {$result['links']} for broken links, sent {$result['pushes']} sponsored push(es).");

        return self::SUCCESS;
    }
}
