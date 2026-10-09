<?php

namespace App\Services\Ads;

use App\Models\AdBundle;
use App\Models\AdCoupon;
use App\Models\AdPlacement;
use App\Models\AdPricingRule;
use App\Models\BusinessProfile;
use App\Support\Gstin;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

/**
 * Ad price maths (plan §2, §5), in integer paise, in this order:
 *
 *   1. per placement: price/day for the tier × each day's peak multiplier × units
 *      (P18 is per cab per 30 days and P11 per send: no peak, exclusivity or
 *      duration discount on those)
 *   2. − bundle saving (placements sold together at a bundle price)
 *   3. + category exclusivity add-on (% of 1–2)
 *   4. − duration discount (14+ / 30+ days)
 *   5. − launch offer or coupon, whichever is larger (they don't stack)
 *   = net (taxable) → + GST (CGST + SGST, or IGST for another state's GSTIN)
 *
 * Seema Holidays keeps its % of the net; OfinIT gets the rest + GST on it.
 */
class AdPricing
{
    /**
     * @param  Collection<AdPlacement>  $placements
     * @param  array{start_date?: string, units?: array<int,int>, exclusive_category?: bool, coupon?: ?AdCoupon, first_booking?: bool}  $options
     */
    public static function quote(Collection $placements, string $tier, int $days, ?string $gstin = null, ?array $settings = null, array $options = []): array
    {
        $settings = $settings ?? AdSettings::load();
        $start = Carbon::parse($options['start_date'] ?? now('Asia/Kolkata')->addDay()->toDateString())->startOfDay();
        $factors = self::dayFactors($start, max(1, $days));
        $factorSum = array_sum($factors);
        $units = $options['units'] ?? [];

        // 1. Placements, with peak pricing.
        $items = [];
        $plain = 0;
        $fixed = 0;
        foreach ($placements as $p) {
            $n = max(1, (int) ($units[$p->id] ?? 1));
            $price = $p->priceFor($tier);
            $billing = $p->billing ?? AdPlacement::PER_DAY;
            if ($billing === AdPlacement::PER_MONTH) {
                $subtotal = $price * self::months($days) * $n;
                $fixed += $subtotal;
                $plain += $subtotal;
            } elseif ($billing === AdPlacement::PER_SEND) {
                $subtotal = $price * $n;
                $fixed += $subtotal;
                $plain += $subtotal;
            } else {
                $subtotal = (int) round($price * $factorSum) * $n;
                $plain += $price * $days * $n;
            }
            $items[] = [
                'placement_id' => $p->id, 'code' => $p->code, 'name' => $p->name, 'billing' => $billing,
                'price_per_day' => $price, 'days' => $days, 'units' => $n, 'subtotal' => $subtotal,
            ];
        }
        $list = array_sum(array_column($items, 'subtotal'));

        // 2. Bundles: biggest first, each placement used once, only when cheaper.
        $bundleDiscount = 0;
        $bundles = [];
        $byCode = collect($items)->filter(fn ($i) => $i['units'] === 1 && $i['billing'] === AdPlacement::PER_DAY)->keyBy('code');
        foreach (AdBundle::where('active', true)->get()->sortByDesc(fn ($b) => count($b->placement_codes)) as $bundle) {
            $codes = $bundle->placement_codes;
            if (array_diff($codes, $byCode->keys()->all())) {
                continue;
            }
            $separate = $byCode->only($codes)->sum('subtotal');
            $together = (int) round($bundle->priceFor($tier) * $factorSum);
            if ($together < $separate) {
                $bundleDiscount += $separate - $together;
                $bundles[] = $bundle->name;
                $byCode = $byCode->except($codes);
            }
        }

        // 3. Category exclusivity add-on (daily placements).
        $afterBundle = $list - $fixed - $bundleDiscount;
        $exclusive = !empty($options['exclusive_category']);
        $exclusivity = $exclusive ? (int) round($afterBundle * (float) $settings[AdSettings::EXCLUSIVITY_PERCENT] / 100) : 0;
        $base = $afterBundle + $exclusivity;

        // 4. Duration discount (daily placements).
        $discountPercent = self::discountPercent($days, $settings);
        $discount = (int) round($base * $discountPercent / 100);
        $afterDuration = $base - $discount + $fixed;

        // 5. Launch offer or coupon (the larger one).
        $launchPercent = !empty($options['first_booking']) ? AdSettings::launchPercent($settings) : 0.0;
        $launch = (int) round($afterDuration * $launchPercent / 100);
        $coupon = $options['coupon'] ?? null;
        $couponDiscount = $coupon ? $coupon->discountOn($afterDuration) : 0;
        $useCoupon = $coupon && $couponDiscount > 0 && $couponDiscount >= $launch;
        $promo = $useCoupon ? $couponDiscount : $launch;
        $promoLabel = $useCoupon ? 'Coupon ' . $coupon->code . ' (' . $coupon->label() . ')'
            : ($launch > 0 ? 'Launch offer (' . rtrim(rtrim(number_format($launchPercent, 2), '0'), '.') . '% off your first ad)' : null);
        $net = $afterDuration - $promo;

        $rate = (float) $settings[AdSettings::GST_RATE];
        $tax = (int) round($net * $rate / 100);
        $interState = self::isInterState($gstin);
        $cgst = $interState ? 0 : intdiv($tax, 2);
        $sgst = $interState ? 0 : $tax - $cgst;
        $igst = $interState ? $tax : 0;

        $seemaPercent = (float) $settings[AdSettings::SEEMA_PERCENT];
        $ofinit = $net - (int) round($net * $seemaPercent / 100);
        $ofinitGst = (int) round($ofinit * $rate / 100);

        return [
            'items' => $items,
            'days' => $days,
            'tier' => $tier,
            'list_amount' => $list,
            'peak_amount' => $list - $plain,
            'peak_days' => count(array_filter($factors, fn ($f) => $f > 1)),
            'bundle_discount' => $bundleDiscount,
            'bundles' => $bundles,
            'exclusive_category' => $exclusive,
            'exclusivity_amount' => $exclusivity,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discount,
            'promo_discount' => $promo,
            'promo_label' => $promoLabel,
            'coupon_id' => $useCoupon ? $coupon->id : null,
            'coupon_code' => $coupon?->code,
            'coupon_note' => $coupon && !$useCoupon ? ($launch > 0 ? 'The launch offer saves more, so it is used instead of the coupon.' : 'The coupon gives no discount on this booking.') : null,
            'net_amount' => $net,
            'gst_rate' => $rate,
            'inter_state' => $interState,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => $igst,
            'total_amount' => $net + $tax,
            'seema_percent' => $seemaPercent,
            'ofinit_amount' => $ofinit,
            'ofinit_gst' => $ofinitGst,
            'ofinit_total' => $ofinit + $ofinitGst,
        ];
    }

    /** Months billed for monthly placements (P18): 30 days = 1 month. */
    public static function months(int $days): int
    {
        return max(1, (int) ceil($days / 30));
    }

    /** Price multiplier for each day (peak windows; the highest applies). */
    public static function dayFactors(Carbon $start, int $days): array
    {
        $end = $start->copy()->addDays($days - 1);
        $rules = AdPricingRule::where('active', true)
            ->where('start_date', '<=', $end->toDateString())->where('end_date', '>=', $start->toDateString())->get();
        $factors = [];
        foreach (CarbonPeriod::create($start, $end) as $day) {
            $d = $day->toDateString();
            $factors[$d] = (float) max([1.0, ...$rules->filter(fn ($r) => $r->start_date->toDateString() <= $d && $r->end_date->toDateString() >= $d)->pluck('multiplier')->all()]);
        }

        return $factors;
    }

    public static function discountPercent(int $days, array $settings): float
    {
        if ($days >= 30) {
            return (float) $settings[AdSettings::DISCOUNT_30];
        }
        if ($days >= 14) {
            return (float) $settings[AdSettings::DISCOUNT_14];
        }

        return 0.0;
    }

    /** IGST when a valid GSTIN is registered outside the supplier's state (Goa = 30). */
    public static function isInterState(?string $gstin): bool
    {
        if (!Gstin::isValid($gstin)) {
            return false;
        }
        $supplierState = optional(BusinessProfile::supplier())->state_code ?: '30';

        return Gstin::stateCode(Gstin::normalize($gstin)) !== $supplierState;
    }

    /** Keys of a quote that are stored on ad_campaigns. */
    public static function campaignColumns(array $quote): array
    {
        return array_intersect_key($quote, array_flip([
            'days', 'tier', 'list_amount', 'peak_amount', 'bundle_discount', 'exclusivity_amount', 'discount_percent', 'discount_amount',
            'promo_discount', 'promo_label', 'coupon_id', 'net_amount', 'gst_rate', 'inter_state',
            'cgst_amount', 'sgst_amount', 'igst_amount', 'total_amount', 'seema_percent', 'ofinit_amount', 'ofinit_gst', 'ofinit_total',
        ]));
    }
}
