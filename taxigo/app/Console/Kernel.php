<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('store:in-city-rides')->daily();

        // Draft (not issue) last month's OfinIT platform-fee invoice for admin review.
        // Requires `php artisan schedule:run` every minute (Coolify scheduled task).
        $schedule->command('invoices:platform-fee')->monthlyOn(1, '06:00')->timezone('Asia/Kolkata');
        $schedule->command('ads:maintain')->hourly()->withoutOverlapping();
        $schedule->command('ads:weekly-reports')->weeklyOn(1, '10:00')->timezone('Asia/Kolkata');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
