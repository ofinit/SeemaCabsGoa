<?php

namespace App\Services\Platform;

use App\Models\Environment;
use App\Models\SettingChange;
use Illuminate\Support\Facades\Crypt;

/**
 * OfinIT ↔ Seema Holidays settlement settings (admin = OfinIT only).
 *
 *  - settlement mode: `split` (OfinIT's share split out at the gateway) or
 *    `invoice` (Seema collects everything; OfinIT invoices monthly)
 *  - OfinIT's own Razorpay keys, used only on the server to create and check
 *    the payment link for each invoice (encrypted at rest)
 *  - suspension state
 */
class PlatformSettings
{
    public const MODE = 'platform_settlement_mode';
    public const AUTO_SUSPEND = 'platform_auto_suspend';
    public const SUSPENDED = 'platform_suspended';
    public const SUSPENDED_AT = 'platform_suspended_at';
    public const SUSPENDED_REASON = 'platform_suspended_reason';
    public const RZP_KEY = 'ofinit_razorpay_key_id';
    public const RZP_SECRET = 'ofinit_razorpay_key_secret';
    public const RZP_WEBHOOK_SECRET = 'ofinit_razorpay_webhook_secret';

    public const SPLIT = 'split';
    public const INVOICE = 'invoice';

    /** Invoices are due on this day of the month; the service is suspended the next day. */
    public const DUE_DAY = 5;

    private static ?array $cache = null;

    public static function get(string $key, ?string $default = null): ?string
    {
        self::$cache ??= Environment::whereIn('title', [
            self::MODE, self::AUTO_SUSPEND, self::SUSPENDED, self::SUSPENDED_AT, self::SUSPENDED_REASON,
            self::RZP_KEY, self::RZP_SECRET, self::RZP_WEBHOOK_SECRET,
        ])->pluck('value', 'title')->all();

        $value = self::$cache[$key] ?? null;

        return ($value === null || $value === '') ? $default : (string) $value;
    }

    public static function set(string $key, ?string $value, bool $audit = true): void
    {
        if ($audit) {
            SettingChange::setEnvironment($key, $value);
        } else {
            Environment::updateOrCreate(['title' => $key], ['value' => $value]);
        }
        self::$cache = null;
    }

    public static function flush(): void
    {
        self::$cache = null;
    }

    public static function mode(): string
    {
        return self::get(self::MODE, self::INVOICE) === self::SPLIT ? self::SPLIT : self::INVOICE;
    }

    /** OfinIT's share is split out at the payment gateway (rides and ads). */
    public static function splitsEnabled(): bool
    {
        return self::mode() === self::SPLIT;
    }

    public static function autoSuspend(): bool
    {
        return self::get(self::AUTO_SUSPEND, '1') === '1';
    }

    public static function suspended(): bool
    {
        return self::get(self::SUSPENDED, '0') === '1';
    }

    /** Stores a secret encrypted; an empty value keeps the current one. */
    public static function setSecret(string $key, ?string $value): void
    {
        if ($value === null || trim($value) === '') {
            return;
        }
        Environment::updateOrCreate(['title' => $key], ['value' => Crypt::encryptString(trim($value))]);
        SettingChange::record($key, null, '(changed)');
        self::$cache = null;
    }

    public static function secret(string $key): ?string
    {
        $value = self::get($key);
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function razorpayConfigured(): bool
    {
        return (bool) self::get(self::RZP_KEY) && (bool) self::secret(self::RZP_SECRET);
    }
}
