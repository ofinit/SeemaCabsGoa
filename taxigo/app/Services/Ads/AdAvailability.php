<?php

namespace App\Services\Ads;

use App\Models\AdCampaign;
use App\Models\AdPlacement;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

/**
 * Slot availability (plan §14). A placement-day is sold out when the ads
 * occupying it reach the placement's slots. Occupying: campaigns in review,
 * awaiting changes, approved or paused, campaigns being paid for (while their
 * hold is valid), and legacy admin-created ads that are approved and paid.
 */
class AdAvailability
{
    /**
     * Website ads (P9) are counted per page group: pass the groups being
     * booked and only ads sharing one of them count.
     *
     * @return array<int, array<string, int>> placement_id => [Y-m-d => ads]
     */
    public static function usage(array $placementIds, string $from, string $to, ?int $excludeCampaignId = null, array $pageGroups = []): array
    {
        $usage = array_fill_keys($placementIds, []);
        if (!$placementIds) {
            return $usage;
        }

        $campaignRows = DB::table('ad_campaign_placements as cp')
            ->join('ad_campaigns as c', 'c.id', '=', 'cp.campaign_id')
            ->whereIn('cp.placement_id', $placementIds)
            ->where('c.start_date', '<=', $to)
            ->where('c.end_date', '>=', $from)
            ->when($excludeCampaignId, fn ($q) => $q->where('c.id', '!=', $excludeCampaignId))
            ->where(function ($q) {
                $q->whereIn('c.status', AdCampaign::BOOKED)
                    ->orWhere(fn ($q) => $q->where('c.status', AdCampaign::PENDING_PAYMENT)->where('c.hold_expires_at', '>', now()));
            })
            ->get(['cp.placement_id', 'cp.units', 'c.start_date', 'c.end_date', 'c.targeting']);
        $websiteIds = AdPlacement::whereIn('id', $placementIds)->where('screen_id', AdServer::SCREEN_WEBSITE)->pluck('id')->all();
        $billing = AdPlacement::whereIn('id', $placementIds)->pluck('billing', 'id');
        foreach ($campaignRows as $row) {
            // P18: each booked cab takes a slot (slots = cabs carrying cards).
            if (($billing[$row->placement_id] ?? 'day') === AdPlacement::PER_MONTH) {
                self::add($usage[$row->placement_id], $row->start_date, $row->end_date, $from, $to, (int) $row->units);
                continue;
            }
            // P11: a push only occupies its send day.
            if (($billing[$row->placement_id] ?? 'day') === AdPlacement::PER_SEND) {
                self::add($usage[$row->placement_id], $row->start_date, $row->start_date, $from, $to);
                continue;
            }
            if ($pageGroups && in_array($row->placement_id, $websiteIds)) {
                $theirs = (array) (json_decode((string) $row->targeting, true)['page_groups'] ?? []);
                if (!array_intersect($theirs, $pageGroups)) {
                    continue;
                }
            }
            self::add($usage[$row->placement_id], $row->start_date, $row->end_date, $from, $to);
        }

        // Legacy ads booked by an admin (not through a campaign).
        $screens = AdPlacement::whereIn('id', $placementIds)->pluck('id', 'screen_id');
        $legacy = DB::table('advertisements')
            ->whereNull('deleted_at')->whereNull('ad_campaign_id')
            ->where('default', 0)
            ->where('approval_status', 'approved')->where('payment_status', 'paid')
            ->where('start_date', '<=', $to)->where('end_date', '>=', $from)
            ->get(['screens', 'start_date', 'end_date']);
        foreach ($legacy as $ad) {
            foreach (array_map('intval', (array) json_decode((string) $ad->screens, true)) as $screen) {
                if (isset($screens[$screen])) {
                    self::add($usage[$screens[$screen]], $ad->start_date, $ad->end_date, $from, $to);
                }
            }
        }

        return $usage;
    }

    /** Sold-out dates per placement between two dates. @return array<int, string[]> */
    public static function soldOut(iterable $placements, string $from, string $to, ?int $excludeCampaignId = null, array $pageGroups = [], array $units = []): array
    {
        $placements = collect($placements);
        $usage = self::usage($placements->pluck('id')->all(), $from, $to, $excludeCampaignId, $pageGroups);
        $out = [];
        foreach ($placements as $placement) {
            // P18 needs room for all the cabs being booked.
            $need = ($placement->billing ?? 'day') === AdPlacement::PER_MONTH ? max(1, (int) ($units[$placement->id] ?? 1)) : 1;
            $out[$placement->id] = array_keys(array_filter(
                $usage[$placement->id] ?? [],
                fn ($count) => $count + $need > $placement->slots
            ));
            sort($out[$placement->id]);
        }

        return $out;
    }

    /** Placements that are sold out on any day of the range: [code => first sold-out date]. */
    public static function conflicts(iterable $placements, string $from, string $to, ?int $excludeCampaignId = null, array $pageGroups = [], array $units = []): array
    {
        $conflicts = [];
        foreach (self::soldOut($placements, $from, $to, $excludeCampaignId, $pageGroups, $units) as $placementId => $dates) {
            if ($dates) {
                $placement = collect($placements)->firstWhere('id', $placementId);
                $conflicts[$placement->code] = $dates[0];
            }
        }

        return $conflicts;
    }

    /**
     * Category exclusivity (plan §5.3): an exclusive booking can't share a
     * placement-day with another ad of the same category, and nobody of that
     * category can book next to an exclusive one. Returns [code => reason].
     */
    public static function categoryConflicts(iterable $placements, string $from, string $to, int $categoryId, bool $wantsExclusive, ?int $excludeCampaignId = null): array
    {
        $placements = collect($placements);
        $rows = DB::table('ad_campaign_placements as cp')
            ->join('ad_campaigns as c', 'c.id', '=', 'cp.campaign_id')
            ->join('ad_advertisers as a', 'a.id', '=', 'c.advertiser_id')
            ->whereIn('cp.placement_id', $placements->pluck('id'))
            ->where('a.category_id', $categoryId)
            ->where('c.start_date', '<=', $to)->where('c.end_date', '>=', $from)
            ->when($excludeCampaignId, fn ($q) => $q->where('c.id', '!=', $excludeCampaignId))
            ->where(function ($q) {
                $q->whereIn('c.status', AdCampaign::BOOKED)
                    ->orWhere(fn ($q) => $q->where('c.status', AdCampaign::PENDING_PAYMENT)->where('c.hold_expires_at', '>', now()));
            })
            ->when(!$wantsExclusive, fn ($q) => $q->where('c.exclusive_category', true))
            ->get(['cp.placement_id', 'c.exclusive_category']);

        $out = [];
        foreach ($rows as $row) {
            $code = $placements->firstWhere('id', $row->placement_id)->code;
            $out[$code] = $row->exclusive_category
                ? 'another advertiser in your category has these days exclusively'
                : 'another advertiser in your category is already booked on these days';
        }

        return $out;
    }

    private static function add(array &$days, string $start, string $end, string $from, string $to, int $count = 1): void
    {
        $start = max(substr($start, 0, 10), $from);
        $end = min(substr($end, 0, 10), $to);
        if ($start > $end) {
            return;
        }
        foreach (CarbonPeriod::create(Carbon::parse($start), Carbon::parse($end)) as $day) {
            $key = $day->toDateString();
            $days[$key] = ($days[$key] ?? 0) + $count;
        }
    }
}
