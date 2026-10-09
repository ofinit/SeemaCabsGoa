<?php

namespace App\Services\Pricing;

use App\Enums\Type;
use Carbon\Carbon;

/**
 * The single source of truth for every customer price and money split.
 *
 * Quotes (cab lists), bookings and payment orders all call this, so a client
 * can never send its own amounts. All maths is in integer paise:
 *
 *   farePreMarkup = round₹(base [+ airport %] [+ surge %])
 *   fare          = round₹(farePreMarkup × (1 + markup %))   ← shown to customer
 *   GST           = round₹(fare × rate %), split CGST/SGST    ← only if it applies
 *   total         = fare + GST
 *   advance       = round₹(total × advance %)  (rides; packages pay 100%)
 *   platform fee  = round₹(fare × platform-fee %)      (OfinIT)
 *   operator comm = round₹(fare × operator %)          (fleet operator payout, rides)
 *
 * The markup is internal: it is never shown as a separate line to customers.
 */
class FareCalculator
{
    public function __construct(private readonly array $settings)
    {
    }

    public static function fromDatabase(): self
    {
        return new self(PricingSettings::load());
    }

    /**
     * @param  float   $baseFare     cab_price_types.base_fare (₹)
     * @param  ?int    $tab          cab_rates.tab (1 = airport pickup, …)
     * @param  ?Carbon $pickupAt     pickup date-time (IST) for surge windows; null = now
     * @param  ?Carbon $bookedAt     when the booking is made (GST start check); null = now
     */
    public function ride(float $baseFare, ?int $tab, ?Carbon $pickupAt = null, ?Carbon $bookedAt = null): FareBreakdown
    {
        $base = self::paise($baseFare);

        if ($tab === Type::AIRPORT_PICKUP) {
            $base += (int) round($base * $this->percent(Type::AIRPORT_PICKUP_PERCENTAGE) / 100);
        }
        $base = self::roundRupee($base);

        $surgePercent = $this->surgePercent($pickupAt ?? now('Asia/Kolkata'));
        $surge = (int) round($base * $surgePercent / 100);
        $farePreMarkup = self::roundRupee($base + $surge);

        return $this->finish(
            'ride',
            $farePreMarkup,
            $surge,
            $this->percent(PricingSettings::MARKUP_RIDES),
            PricingSettings::GST_RATE_RIDES,
            PricingSettings::GST_SAC_RIDES,
            $this->percent(Type::TotalCommission),
            $this->percent(Type::CompanyCommission),
            $this->percent(Type::FleetOperatorCommission),
            $bookedAt,
        );
    }

    /** Sightseeing package: fixed price per cab type, paid 100% online. */
    public function package(float $price, ?Carbon $bookedAt = null): FareBreakdown
    {
        return $this->finish(
            'package',
            self::roundRupee(self::paise($price)),
            0,
            $this->percent(PricingSettings::MARKUP_PACKAGES),
            PricingSettings::GST_RATE_PACKAGES,
            PricingSettings::GST_SAC_PACKAGES,
            100.0,
            $this->percent(Type::PackageAggregatorCommission),
            $this->percent(Type::PackageOperatorCommission),
            $bookedAt,
        );
    }

    private function finish(
        string $kind,
        int $farePreMarkup,
        int $surge,
        float $markupPercent,
        string $gstRateKey,
        string $sacKey,
        float $advancePercent,
        float $platformFeePercent,
        float $operatorPercent,
        ?Carbon $bookedAt,
    ): FareBreakdown {
        $fare = self::roundRupee((int) round($farePreMarkup * (100 + $markupPercent) / 100));
        $fare = max($fare, 0);
        $markup = $fare - $farePreMarkup;

        $gstRate = PricingSettings::gstAppliesAt($this->settings, $bookedAt ?? now()) ? $this->percent($gstRateKey) : 0.0;
        $gst = $gstRate > 0 ? self::roundRupee((int) round($fare * $gstRate / 100)) : 0;
        $cgst = intdiv($gst, 2);
        $sgst = $gst - $cgst;

        $total = $fare + $gst;
        $advance = min($total, self::roundRupee((int) round($total * $advancePercent / 100)));
        $balance = $total - $advance;

        // Commissions are a % of the fare the customer sees (markup included,
        // ride GST excluded). OfinIT's fee carries its own GST (18% by default),
        // which is also taken out of the online advance. The ride GST inside
        // the advance stays with Seema Holidays for remittance. The fleet
        // operator gets what is left, up to its own commission:
        //   ₹1,200 fare, 10% + 10%, 20% advance → OfinIT ₹120 + ₹21.60 GST,
        //   fleet operator ₹240 − ₹141.60 = ₹98.40.
        $platformFee = self::roundRupee((int) round($fare * $platformFeePercent / 100));
        $platformFeeGst = (int) round($platformFee * $this->percent(PricingSettings::GST_RATE_PLATFORM_FEE) / 100);
        $operatorCommission = self::roundRupee((int) round($fare * $operatorPercent / 100));
        $rideGstInAdvance = $gstRate > 0 ? $advance - (int) round($advance * 100 / (100 + $gstRate)) : 0;
        $fleetOperatorPayment = $kind === 'ride'
            ? max(0, min($operatorCommission, $advance - $rideGstInAdvance - $platformFee - $platformFeeGst))
            : max(0, $fare - $platformFee - $platformFeeGst);
        $tds = (int) round($fare * $this->percent(Type::TdsTitle) / 100);

        $sac = trim((string) ($this->settings[$sacKey] ?? ''));

        return new FareBreakdown(
            kind: $kind,
            farePreMarkup: $farePreMarkup,
            surge: $surge,
            markupPercent: $markupPercent,
            markup: $markup,
            fare: $fare,
            gstRate: $gstRate,
            cgst: $cgst,
            sgst: $sgst,
            gst: $gst,
            sac: $sac !== '' ? $sac : null,
            total: $total,
            advance: $advance,
            balance: $balance,
            platformFeePercent: $platformFeePercent,
            platformFee: $platformFee,
            platformFeeGst: $platformFeeGst,
            operatorCommission: $operatorCommission,
            fleetOperatorPayment: $fleetOperatorPayment,
            tds: $tds,
        );
    }

    /** Surge % for the first configured window containing $at (0 if none / disabled). */
    private function surgePercent(Carbon $at): float
    {
        $surge = json_decode((string) ($this->settings[Type::SURGE_PRICE] ?? ''), true);
        if (!is_array($surge) || ($surge['surge_enable'] ?? null) !== Type::SURGE_PRICE_ENABLED) {
            return 0.0;
        }

        $starts = (array) ($surge['surge_start_date'] ?? []);
        foreach ($starts as $i => $startDate) {
            try {
                $from = Carbon::parse($startDate . ' ' . ($surge['surge_start_time'][$i] ?? '00:00'), 'Asia/Kolkata');
                $to = Carbon::parse(($surge['surge_end_date'][$i] ?? $startDate) . ' ' . ($surge['surge_end_time'][$i] ?? '23:59'), 'Asia/Kolkata');
            } catch (\Throwable) {
                continue;
            }
            if ($at->betweenIncluded($from, $to)) {
                return (float) ($surge['surge_percentage'][$i] ?? 0);
            }
        }

        return 0.0;
    }

    private function percent(string $key): float
    {
        $value = $this->settings[$key] ?? 0;

        return is_numeric($value) ? (float) $value : 0.0;
    }

    public static function paise(float|int|string|null $rupees): int
    {
        return (int) round(((float) $rupees) * 100);
    }

    /** Round half-up to the nearest whole rupee (in paise). */
    public static function roundRupee(int $paise): int
    {
        return (int) (round($paise / 100) * 100);
    }
}
