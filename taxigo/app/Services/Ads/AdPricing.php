<?php

namespace App\Services\Ads;

use App\Models\AdPlacement;
use App\Models\BusinessProfile;
use App\Support\Gstin;
use Illuminate\Support\Collection;

/**
 * Ad price maths (plan §2, §5), in integer paise. Prices are per placement per
 * day for the advertiser's tier; 14+ / 30+ day discounts apply to the whole
 * booking; GST is added on top (CGST + SGST, or IGST when the advertiser's
 * GSTIN is from another state). Seema Holidays keeps its % of the net price;
 * OfinIT gets the rest plus GST on it, split at the gateway.
 */
class AdPricing
{
    /**
     * @param  Collection<AdPlacement>  $placements
     */
    public static function quote(Collection $placements, string $tier, int $days, ?string $gstin = null, ?array $settings = null): array
    {
        $settings = $settings ?? AdSettings::load();

        $items = $placements->map(fn (AdPlacement $p) => [
            'placement_id' => $p->id,
            'code' => $p->code,
            'name' => $p->name,
            'price_per_day' => $p->priceFor($tier),
            'days' => $days,
            'subtotal' => $p->priceFor($tier) * $days,
        ])->values()->all();

        $list = array_sum(array_column($items, 'subtotal'));
        $discountPercent = self::discountPercent($days, $settings);
        $discount = (int) round($list * $discountPercent / 100);
        $net = $list - $discount;

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
            'discount_percent' => $discountPercent,
            'discount_amount' => $discount,
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
            'days', 'tier', 'list_amount', 'discount_percent', 'discount_amount', 'net_amount', 'gst_rate', 'inter_state',
            'cgst_amount', 'sgst_amount', 'igst_amount', 'total_amount', 'seema_percent', 'ofinit_amount', 'ofinit_gst', 'ofinit_total',
        ]));
    }
}
