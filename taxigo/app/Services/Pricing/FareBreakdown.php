<?php

namespace App\Services\Pricing;

/**
 * Result of a fare calculation. Every amount is in **paise** (int) so no
 * floating-point rounding leaks into money; convert with rupees() only at the
 * edges (API responses, DB decimal columns).
 */
class FareBreakdown
{
    public function __construct(
        public readonly string $kind,              // 'ride' | 'package'
        public readonly int $farePreMarkup,        // base + airport % + surge, before markup
        public readonly int $surge,
        public readonly float $markupPercent,
        public readonly int $markup,
        public readonly int $fare,                 // what the customer sees as the fare (incl. markup, excl. GST)
        public readonly float $gstRate,            // 0 when GST doesn't apply
        public readonly int $cgst,
        public readonly int $sgst,
        public readonly int $gst,                  // cgst + sgst
        public readonly ?string $sac,
        public readonly int $total,                // fare + gst
        public readonly int $advance,              // paid online to confirm
        public readonly int $balance,              // paid to the driver
        public readonly float $platformFeePercent,
        public readonly int $platformFee,          // OfinIT platform fee (% of fare incl. markup, excl. GST)
        public readonly int $platformFeeGst,       // GST on that fee (default 18%), also taken from the advance
        public readonly int $operatorCommission,
        public readonly int $fleetOperatorPayment,
        public readonly int $tds,
    ) {
    }

    public static function rupees(int $paise): float
    {
        return round($paise / 100, 2);
    }

    /** Rupee string as the existing API returns it ("1334", "31.50"). */
    public static function display(int $paise): string
    {
        return $paise % 100 === 0 ? (string) intdiv($paise, 100) : number_format($paise / 100, 2, '.', '');
    }

    public function gstApplies(): bool
    {
        return $this->gstRate > 0;
    }

    /** Columns persisted on booking_details for pricing v2. */
    public function bookingAttributes(): array
    {
        return [
            // 3 = commissions on the fare incl. markup (2 = on the fare before markup).
            'pricing_version' => 3,
            'fare_before_markup' => self::rupees($this->farePreMarkup),
            'markup_percent' => $this->markupPercent,
            'markup_amount' => self::rupees($this->markup),
            'base_fare' => self::rupees($this->fare),
            'surge_price' => self::rupees($this->surge),
            'taxable_amount' => self::rupees($this->fare),
            'gst_rate' => $this->gstApplies() ? $this->gstRate : null,
            'cgst_amount' => $this->gstApplies() ? self::rupees($this->cgst) : null,
            'sgst_amount' => $this->gstApplies() ? self::rupees($this->sgst) : null,
            'igst_amount' => null,
            'sac_code' => $this->gstApplies() ? $this->sac : null,
            'tax_amount' => self::rupees($this->gst),
            'total_payment' => self::rupees($this->total),
            'part_payment' => self::rupees($this->advance),
            'cash_with_driver' => self::rupees($this->balance),
            'platform_fee_percent' => $this->platformFeePercent,
            'platform_fee_amount' => self::rupees($this->platformFee),
        ];
    }

    /** Settlement JSON stored on the payments row (same keys as before). */
    public function settlement(): array
    {
        return [
            'fleet_operator_commission' => self::rupees($this->operatorCommission),
            'company_commission' => self::rupees($this->platformFee),
            'platform_fee_gst' => self::rupees($this->platformFeeGst),
            'platform_fee_total' => self::rupees($this->platformFee + $this->platformFeeGst),
            'gst_amount' => self::rupees($this->gst),
            'tds_amount' => self::rupees($this->tds),
            'fleet_operator_total_payment' => self::rupees($this->fleetOperatorPayment),
        ];
    }
}
