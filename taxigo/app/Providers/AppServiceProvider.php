<?php

namespace App\Providers;

use App\Models\Environment;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the default string length for database columns
        Schema::defaultStringLength(191);

        // SMTP settings live in the environments table. The database isn't
        // reachable during the Docker build (composer runs package:discover)
        // or before migrations, so fall back to the .env mail config then.
        try {
            $settings = Environment::whereIn('title', [
                'smtppassword',
                'smtphost',
                'smtpport',
                'smtpusername',
                'fromaddress',
                'smtpauthentication'
            ])->pluck('value', 'title');
        } catch (\Throwable $e) {
            return;
        }

        if ($settings->isNotEmpty()) {
            Config::set('mail.mailers.smtp.host', $settings['smtphost'] ?? '');
            Config::set('mail.mailers.smtp.port', $settings['smtpport'] ?? '');
            Config::set('mail.mailers.smtp.username', $settings['smtpusername'] ?? '');
            Config::set('mail.mailers.smtp.password', $settings['smtppassword'] ?? '');
            Config::set('mail.mailers.smtp.encryption', $settings['smtpauthentication'] ?? '');
            Config::set('mail.from.address', $settings['fromaddress'] ?? '');
            Config::set('mail.from.name', 'no-reply');
        }
    }
}
