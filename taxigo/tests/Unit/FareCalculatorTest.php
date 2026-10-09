<?php

namespace Tests\Unit;

use App\Enums\Type;
use App\Services\Pricing\FareCalculator;
use App\Services\Pricing\PricingSettings;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class FareCalculatorTest extends TestCase
{
    private function settings(array $overrides = []): array
    {
        return array_merge(PricingSettings::DEFAULTS, [
            PricingSettings::MARKUP_RIDES => '20',
            Type::TotalCommission => '20',
            Type::CompanyCommission => '10',
            Type::FleetOperatorCommission => '10',
            Type::AIRPORT_PICKUP_PERCENTAGE => '0',
        ], $overrides);
    }

    public function test_markup_is_folded_into_the_fare_and_gst_is_zero_when_off(): void
    {
        $fare = (new FareCalculator($this->settings()))->ride(1111, 3);

        $this->assertSame(111100, $fare->farePreMarkup);
        $this->assertSame(133300, $fare->fare);        // 1111 × 1.2 = 1333.20 → ₹1333
        $this->assertSame(22200, $fare->markup);
        $this->assertSame(0, $fare->gst);
        $this->assertSame(133300, $fare->total);
        $this->assertSame(26700, $fare->advance);      // 20% of 1333 = 266.60 → ₹267
        $this->assertSame(106600, $fare->balance);
    }

    public function test_customer_total_matches_the_old_fare_plus_20_percent_tax(): void
    {
        // Old: price 1111 + "tax" round(222.2) = 1333; advance round(222.2 + 44.44) = 267.
        $fare = (new FareCalculator($this->settings()))->ride(1111, 3);

        $this->assertSame(133300, $fare->total);
        $this->assertSame(26700, $fare->advance);
    }

    public function test_commissions_are_a_percentage_of_the_fare_including_markup(): void
    {
        $fare = (new FareCalculator($this->settings()))->ride(1000, 3); // fare ₹1200, advance ₹240

        $this->assertSame(12000, $fare->platformFee);          // OfinIT: 10% of ₹1200
        $this->assertSame(12000, $fare->operatorCommission);   // fleet operator: 10% of ₹1200 (old code: 100× too high)
        $this->assertSame(12000, $fare->fleetOperatorPayment);
        $this->assertSame($fare->advance, $fare->platformFee + $fare->operatorCommission);
    }

    public function test_rounding_never_pays_out_more_than_the_advance(): void
    {
        // Markup 0 so the fare is exactly ₹1245: 10% = 124.50 rounds up twice, advance 20% = 249.
        $fare = (new FareCalculator($this->settings([PricingSettings::MARKUP_RIDES => '0'])))->ride(1245, 3);

        $this->assertSame(24900, $fare->advance);
        $this->assertSame(12500, $fare->platformFee);
        $this->assertSame(12400, $fare->fleetOperatorPayment);
        $this->assertLessThanOrEqual($fare->advance, $fare->platformFee + $fare->fleetOperatorPayment);
    }

    public function test_gst_inside_the_advance_is_not_paid_out_as_commission(): void
    {
        $fare = (new FareCalculator($this->settings([PricingSettings::GST_ENABLED => '1'])))->ride(1000, 3);

        $this->assertSame(25200, $fare->advance);              // 20% of ₹1260
        $this->assertSame(12000, $fare->platformFee);
        $this->assertSame(12000, $fare->fleetOperatorPayment);
        $this->assertSame(1200, $fare->advance - $fare->platformFee - $fare->fleetOperatorPayment); // GST share, stays with supplier
    }

    public function test_gst_applies_only_from_the_start_date(): void
    {
        $calc = new FareCalculator($this->settings([
            PricingSettings::GST_ENABLED => '1',
            PricingSettings::GST_START_AT => '2026-11-01 00:00',
            PricingSettings::GST_SAC_RIDES => '996412',
        ]));

        $before = $calc->ride(1000, 3, null, Carbon::parse('2026-10-31 23:59', 'Asia/Kolkata'));
        $after = $calc->ride(1000, 3, null, Carbon::parse('2026-11-01 00:00', 'Asia/Kolkata'));

        $this->assertSame(0, $before->gst);
        $this->assertNull($before->bookingAttributes()['gst_rate']);

        $this->assertSame(120000, $after->fare);
        $this->assertSame(6000, $after->gst);                  // 5% of ₹1200
        $this->assertSame(3000, $after->cgst);
        $this->assertSame(3000, $after->sgst);
        $this->assertSame(126000, $after->total);
        $this->assertSame(25200, $after->advance);             // 20% of the GST-inclusive total
        $this->assertSame('996412', $after->sac);
    }

    public function test_odd_gst_splits_into_paise_without_losing_a_paisa(): void
    {
        $calc = new FareCalculator($this->settings([PricingSettings::GST_ENABLED => '1']));
        $fare = $calc->ride(1050, 3); // fare 1260 → GST 63

        $this->assertSame(6300, $fare->gst);
        $this->assertSame(3150, $fare->cgst);
        $this->assertSame(3150, $fare->sgst);
        $this->assertSame($fare->gst, $fare->cgst + $fare->sgst);
    }

    public function test_markup_can_be_reduced_or_negative(): void
    {
        $discount = (new FareCalculator($this->settings([PricingSettings::MARKUP_RIDES => '-10'])))->ride(1000, 3);
        $this->assertSame(90000, $discount->fare);
        $this->assertSame(-10000, $discount->markup);

        $holdTotals = (new FareCalculator($this->settings([
            PricingSettings::MARKUP_RIDES => '14.29',
            PricingSettings::GST_ENABLED => '1',
        ])))->ride(1000, 3);
        $this->assertSame(114300, $holdTotals->fare);
        $this->assertSame(120000, $holdTotals->total);         // same ₹1200 the customer paid before GST
    }

    public function test_airport_pickup_percentage_and_surge_window(): void
    {
        $calc = new FareCalculator($this->settings([
            Type::AIRPORT_PICKUP_PERCENTAGE => '10',
            Type::SURGE_PRICE => json_encode([
                'surge_enable' => 'on',
                'surge_start_date' => ['2026-12-24'],
                'surge_end_date' => ['2026-12-26'],
                'surge_start_time' => ['00:00'],
                'surge_end_time' => ['23:59'],
                'surge_percentage' => ['25'],
            ]),
        ]));

        $normal = $calc->ride(1000, Type::AIRPORT_PICKUP, Carbon::parse('2026-12-20 10:00', 'Asia/Kolkata'));
        $this->assertSame(110000, $normal->farePreMarkup);

        $surged = $calc->ride(1000, Type::AIRPORT_PICKUP, Carbon::parse('2026-12-25 10:00', 'Asia/Kolkata'));
        $this->assertSame(27500, $surged->surge);
        $this->assertSame(137500, $surged->farePreMarkup);
    }

    public function test_packages_are_paid_in_full_online_with_their_own_markup(): void
    {
        $calc = new FareCalculator($this->settings([
            PricingSettings::MARKUP_PACKAGES => '0',
            Type::PackageAggregatorCommission => '5',
            PricingSettings::GST_ENABLED => '1',
            PricingSettings::GST_RATE_PACKAGES => '5',
        ]));

        $pkg = $calc->package(3000);
        $this->assertSame(300000, $pkg->fare);
        $this->assertSame(15000, $pkg->gst);
        $this->assertSame(315000, $pkg->total);
        $this->assertSame(315000, $pkg->advance);
        $this->assertSame(0, $pkg->balance);
        $this->assertSame(15000, $pkg->platformFee);
        $this->assertGreaterThanOrEqual(0, $pkg->fleetOperatorPayment); // old code produced a negative value
    }
}
