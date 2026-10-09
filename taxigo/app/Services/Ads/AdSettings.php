<?php

namespace App\Services\Ads;

use App\Models\Environment;

/**
 * Self-serve ad settings, stored in `environments` (edited and audited in
 * Admin → Advertisements → Placements & pricing).
 */
class AdSettings
{
    public const ENABLED = 'ads_selfserve_enabled';
    public const SEEMA_PERCENT = 'ads_seema_commission_percent';
    public const GST_RATE = 'ads_gst_rate';
    public const SAC_SEEMA = 'ads_sac_seema';
    public const SAC_OFINIT = 'ads_sac_ofinit';
    public const MIN_DAYS = 'ads_min_days';
    public const MAX_DAYS = 'ads_max_days';
    public const DISCOUNT_14 = 'ads_discount_14_percent';
    public const DISCOUNT_30 = 'ads_discount_30_percent';
    public const HOLD_MINUTES = 'ads_hold_minutes';
    public const SECOND_APPROVAL = 'ads_second_approval';

    public const DEFAULTS = [
        self::ENABLED => '1',
        self::SEEMA_PERCENT => '10',
        self::GST_RATE => '18',
        self::SAC_SEEMA => '998365',
        self::SAC_OFINIT => '',
        self::MIN_DAYS => '7',
        self::MAX_DAYS => '90',
        self::DISCOUNT_14 => '10',
        self::DISCOUNT_30 => '20',
        self::HOLD_MINUTES => '15',
        self::SECOND_APPROVAL => '0',
    ];

    public const LABELS = [
        self::ENABLED => 'Self-serve ads open to advertisers (1 = yes, 0 = no)',
        self::SEEMA_PERCENT => "Seema Holidays' commission (% of net ad price)",
        self::GST_RATE => 'GST rate on ads (%)',
        self::SAC_SEEMA => 'SAC — internet advertising space, Seema → advertiser (CA to confirm)',
        self::SAC_OFINIT => 'SAC — ad platform & operations, OfinIT → Seema (CA to confirm)',
        self::MIN_DAYS => 'Minimum booking (days)',
        self::MAX_DAYS => 'Maximum booking (days)',
        self::DISCOUNT_14 => 'Discount for 14+ days (%)',
        self::DISCOUNT_30 => 'Discount for 30+ days (%)',
        self::HOLD_MINUTES => 'Slot hold while paying (minutes)',
        self::SECOND_APPROVAL => 'Casino and flagged ads need a second admin (1 = yes, 0 = no)',
    ];

    public static function load(): array
    {
        $rows = Environment::whereIn('title', array_keys(self::DEFAULTS))->pluck('value', 'title')->all();
        $settings = [];
        foreach (self::DEFAULTS as $key => $default) {
            $value = $rows[$key] ?? null;
            $settings[$key] = ($value === null || $value === '') ? $default : (string) $value;
        }

        return $settings;
    }

    public static function get(string $key): string
    {
        return self::load()[$key];
    }

    public static function enabled(): bool
    {
        return self::get(self::ENABLED) === '1';
    }
}
