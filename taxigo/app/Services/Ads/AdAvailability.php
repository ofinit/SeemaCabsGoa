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
    /** @return array<int, array<string, int>> placement_id => [Y-m-d => ads] */
    public static function usage(array $placementIds, string $from, string $to, ?int $excludeCampaignId = null): array
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
            ->get(['cp.placement_id', 'c.start_date', 'c.end_date']);
        foreach ($campaignRows as $row) {
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
    public static function soldOut(iterable $placements, string $from, string $to, ?int $excludeCampaignId = null): array
    {
        $placements = collect($placements);
        $usage = self::usage($placements->pluck('id')->all(), $from, $to, $excludeCampaignId);
        $out = [];
        foreach ($placements as $placement) {
            $out[$placement->id] = array_keys(array_filter(
                $usage[$placement->id] ?? [],
                fn ($count) => $count >= $placement->slots
            ));
            sort($out[$placement->id]);
        }

        return $out;
    }

    /** Placements that are sold out on any day of the range: [code => first sold-out date]. */
    public static function conflicts(iterable $placements, string $from, string $to, ?int $excludeCampaignId = null): array
    {
        $conflicts = [];
        foreach (self::soldOut($placements, $from, $to, $excludeCampaignId) as $placementId => $dates) {
            if ($dates) {
                $placement = collect($placements)->firstWhere('id', $placementId);
                $conflicts[$placement->code] = $dates[0];
            }
        }

        return $conflicts;
    }

    private static function add(array &$days, string $start, string $end, string $from, string $to): void
    {
        $start = max(substr($start, 0, 10), $from);
        $end = min(substr($end, 0, 10), $to);
        if ($start > $end) {
            return;
        }
        foreach (CarbonPeriod::create(Carbon::parse($start), Carbon::parse($end)) as $day) {
            $key = $day->toDateString();
            $days[$key] = ($days[$key] ?? 0) + 1;
        }
    }
}
