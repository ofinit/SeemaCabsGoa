<?php

namespace App\Services\Pricing;

use App\Enums\Type;
use App\Models\Environment;
use Carbon\Carbon;

/**
 * Typed, defaulted view over the pricing / tax rows in the `environments`
 * table. FareCalculator takes a plain array so it can be unit-tested without
 * a database; this class is the only place that reads those rows.
 */
class PricingSettings
{
    // Environment titles introduced for pricing v2 (markup + GST).
    public const MARKUP_RIDES = 'faremarkuppercent';
    public const MARKUP_PACKAGES = 'faremarkuppercentpackages';
    public const GST_ENABLED = 'gst_enabled';
    public const GST_START_AT = 'gst_start_at';
    public const GST_RATE_RIDES = 'gst_rate_rides';
    public const GST_RATE_PACKAGES = 'gst_rate_packages';
    public const GST_SAC_RIDES = 'gst_sac_rides';
    public const GST_SAC_PACKAGES = 'gst_sac_packages';
    public const GST_RATE_PLATFORM_FEE = 'gst_rate_platform_fee';
    public const GST_SAC_PLATFORM_FEE = 'gst_sac_platform_fee';

    public const DEFAULTS = [
        self::MARKUP_RIDES => '20',
        self::MARKUP_PACKAGES => '0',
        self::GST_ENABLED => '0',
        self::GST_START_AT => '',
        self::GST_RATE_RIDES => '5',
        self::GST_RATE_PACKAGES => '5',
        self::GST_SAC_RIDES => '',
        self::GST_SAC_PACKAGES => '',
        self::GST_RATE_PLATFORM_FEE => '18',
        self::GST_SAC_PLATFORM_FEE => '',
        Type::AIRPORT_PICKUP_PERCENTAGE => '0',
        Type::SURGE_PRICE => '',
        Type::TotalCommission => '0',
        Type::CompanyCommission => '0',
        Type::FleetOperatorCommission => '0',
        Type::PackageAggregatorCommission => '0',
        Type::PackageOperatorCommission => '0',
        Type::TdsTitle => '0',
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

    /**
     * GST applies only when switched on, and only to bookings created at or
     * after the configured start (IST). Earlier bookings are never touched.
     */
    public static function gstAppliesAt(array $settings, Carbon $bookedAt): bool
    {
        if (($settings[self::GST_ENABLED] ?? '0') !== '1') {
            return false;
        }

        $start = trim((string) ($settings[self::GST_START_AT] ?? ''));
        if ($start === '') {
            return true;
        }

        return $bookedAt->copy()->timezone('Asia/Kolkata')
            ->greaterThanOrEqualTo(Carbon::parse($start, 'Asia/Kolkata'));
    }
}
