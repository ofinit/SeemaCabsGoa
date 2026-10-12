<?php

namespace App\Console\Commands;

use App\Services\Platform\PlatformSettings;
use App\Services\Platform\PlatformSuspension;
use Illuminate\Console\Command;

/** Every minute: hide / restore the static marketing pages to match the suspension state (a deploy restores the files). */
class PlatformSyncPages extends Command
{
    protected $signature = 'platform:sync-pages';

    protected $description = 'Keep the static marketing pages in step with the platform suspension state';

    public function handle(): int
    {
        PlatformSettings::flush();
        $changed = PlatformSuspension::syncStaticPages();
        if ($changed) {
            $this->info("{$changed} page(s) " . (PlatformSettings::suspended() ? 'hidden.' : 'restored.'));
        }

        return self::SUCCESS;
    }
}
