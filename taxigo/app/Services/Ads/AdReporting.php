<?php

namespace App\Services\Ads;

use App\Mail\AdReportMail;
use App\Models\AdCampaign;
use App\Models\AdPlacement;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Ad results (plan §15): views (viewable impressions), taps (clicks) and
 * tap rate by placement and day — for My ads, CSV export, the weekly and
 * final emails, and the admin dashboard. Counts only; no personal data.
 */
class AdReporting
{
    /** @return array{views: int, clicks: int, ctr: ?float, placements: array, days: array, conversions: int, conversion_value: int, conversion_labels: array, push: array, qr: array} */
    public function stats(AdCampaign $campaign, ?string $from = null, ?string $to = null): array
    {
        $campaign->loadMissing('items.placement');
        $adIds = $campaign->items->pluck('advertisement_id')->filter()->values();
        $extra = $this->extras($campaign, $from, $to);
        if ($adIds->isEmpty()) {
            return ['views' => 0, 'clicks' => 0, 'ctr' => null, 'placements' => [], 'days' => []] + $extra;
        }
        $byAd = $campaign->items->filter(fn ($i) => $i->advertisement_id)->keyBy('advertisement_id');

        $views = DB::table('advertisement_impressions')->whereIn('advertisement_id', $adIds)
            ->when($from, fn ($q) => $q->where('date', '>=', $from))->when($to, fn ($q) => $q->where('date', '<=', $to))
            ->groupBy('advertisement_id', 'date')->selectRaw('advertisement_id, date, SUM(views) as views')->get();
        $clicks = DB::table('advertisement_user_clicks')->whereIn('advertisement_id', $adIds)
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->groupBy('advertisement_id', DB::raw('DATE(created_at)'))->selectRaw('advertisement_id, DATE(created_at) as date, COUNT(*) as clicks')->get();

        $placements = [];
        $days = [];
        foreach ($views as $row) {
            $code = $byAd[$row->advertisement_id]->placement->code ?? '—';
            $placements[$code]['views'] = ($placements[$code]['views'] ?? 0) + $row->views;
            $days[$row->date]['views'] = ($days[$row->date]['views'] ?? 0) + $row->views;
        }
        foreach ($clicks as $row) {
            $code = $byAd[$row->advertisement_id]->placement->code ?? '—';
            $placements[$code]['clicks'] = ($placements[$code]['clicks'] ?? 0) + $row->clicks;
            $days[$row->date]['clicks'] = ($days[$row->date]['clicks'] ?? 0) + $row->clicks;
        }
        krsort($days);
        $totalViews = array_sum(array_column($placements, 'views'));
        $totalClicks = array_sum(array_column($placements, 'clicks'));

        return [
            'views' => $totalViews,
            'clicks' => $totalClicks,
            'ctr' => $totalViews > 0 ? round($totalClicks * 100 / $totalViews, 2) : null,
            'placements' => $placements,
            'days' => $days,
        ] + $extra;
    }

    /** Conversions (advertiser's tag), sponsored-push reach and QR card scans. */
    private function extras(AdCampaign $campaign, ?string $from, ?string $to): array
    {
        $conversions = DB::table('ad_conversions')->where('campaign_id', $campaign->id)
            ->when($from, fn ($q) => $q->where('date', '>=', $from))->when($to, fn ($q) => $q->where('date', '<=', $to))
            ->groupBy('label')->selectRaw('label, COUNT(*) as n, SUM(value) as value')->get();
        $push = DB::table('ad_push_sends')->where('campaign_id', $campaign->id)
            ->selectRaw("SUM(recipients) as recipients, SUM(delivered) as delivered, MAX(sent_at) as sent_at, SUM(status = 'scheduled') as scheduled")->first();
        $qr = DB::table('ad_qr_cards')->where('campaign_id', $campaign->id)
            ->selectRaw('COUNT(*) as cards, SUM(placed_on IS NOT NULL AND removed_on IS NULL) as placed, SUM(scans) as scans')->first();

        return [
            'conversions' => (int) $conversions->sum('n'),
            'conversion_value' => (int) $conversions->sum('value'),
            'conversion_labels' => $conversions->mapWithKeys(fn ($r) => [$r->label => (int) $r->n])->all(),
            'push' => ['recipients' => (int) ($push->recipients ?? 0), 'delivered' => (int) ($push->delivered ?? 0), 'sent_at' => $push->sent_at ?? null, 'scheduled' => (int) ($push->scheduled ?? 0)],
            'qr' => ['cards' => (int) ($qr->cards ?? 0), 'placed' => (int) ($qr->placed ?? 0), 'scans' => (int) ($qr->scans ?? 0)],
        ];
    }

    /** CSV of results by day and placement. */
    public function csv(AdCampaign $campaign): string
    {
        $campaign->loadMissing('items.placement');
        $rows = [];
        $adIds = $campaign->items->pluck('advertisement_id')->filter();
        $codes = $campaign->items->filter(fn ($i) => $i->advertisement_id)->mapWithKeys(fn ($i) => [$i->advertisement_id => $i->placement->code]);
        foreach (DB::table('advertisement_impressions')->whereIn('advertisement_id', $adIds)->groupBy('advertisement_id', 'date')
            ->selectRaw('advertisement_id, date, SUM(views) as views')->get() as $r) {
            $rows[$r->date . '|' . $codes[$r->advertisement_id]]['views'] = (int) $r->views;
        }
        foreach (DB::table('advertisement_user_clicks')->whereIn('advertisement_id', $adIds)->groupBy('advertisement_id', DB::raw('DATE(created_at)'))
            ->selectRaw('advertisement_id, DATE(created_at) as date, COUNT(*) as clicks')->get() as $r) {
            $rows[$r->date . '|' . $codes[$r->advertisement_id]]['clicks'] = (int) $r->clicks;
        }
        ksort($rows);

        $out = fopen('php://temp', 'r+');
        fputcsv($out, ['Ad', 'Date', 'Placement', 'Views', 'Taps', 'Tap rate %']);
        foreach ($rows as $key => $row) {
            [$date, $code] = explode('|', $key);
            $v = $row['views'] ?? 0;
            $c = $row['clicks'] ?? 0;
            fputcsv($out, [$campaign->reference, $date, $code, $v, $c, $v ? round($c * 100 / $v, 2) : '']);
        }
        rewind($out);

        return stream_get_contents($out);
    }

    /** Monday report: last 7 days for every ad that ran in them. */
    public function sendWeeklyReports(): int
    {
        if (AdSettings::get(AdSettings::WEEKLY_REPORTS) !== '1') {
            return 0;
        }
        $to = now('Asia/Kolkata')->subDay()->toDateString();
        $from = now('Asia/Kolkata')->subDays(7)->toDateString();
        $sent = 0;
        AdCampaign::with('advertiser', 'items.placement')
            ->whereIn('status', [AdCampaign::APPROVED, AdCampaign::PAUSED, AdCampaign::EXPIRED])
            ->where('start_date', '<=', $to)->where('end_date', '>=', $from)
            ->where(fn ($q) => $q->whereNull('weekly_report_at')->orWhere('weekly_report_at', '<', now()->subDays(6)))
            ->get()
            ->each(function (AdCampaign $campaign) use ($from, $to, &$sent) {
                if ($this->mail($campaign, 'weekly', $this->stats($campaign, $from, $to), $from, $to)) {
                    $campaign->forceFill(['weekly_report_at' => now()])->save();
                    $sent++;
                }
            });

        return $sent;
    }

    public function sendFinalReport(AdCampaign $campaign): void
    {
        if ($campaign->final_report_at) {
            return;
        }
        if ($this->mail($campaign, 'final', $this->stats($campaign), $campaign->start_date->toDateString(), $campaign->end_date->toDateString())) {
            $campaign->forceFill(['final_report_at' => now()])->save();
        }
    }

    private function mail(AdCampaign $campaign, string $kind, array $stats, string $from, string $to): bool
    {
        $email = $campaign->advertiser->email ?? null;
        if (!$email) {
            return false;
        }
        try {
            Mail::to($email)->send(new AdReportMail($campaign, $kind, $stats, $from, $to));

            return true;
        } catch (\Throwable $e) {
            Log::warning("Ad {$kind} report email failed for {$campaign->reference}: " . $e->getMessage());

            return false;
        }
    }

    /** Admin dashboard figures for a month (amounts in paise). */
    public function dashboard(Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $paid = AdCampaign::whereNotNull('paid_at')->whereBetween('paid_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);

        $money = (clone $paid)->selectRaw('COUNT(*) as ads, SUM(total_amount) as gross, SUM(cgst_amount + sgst_amount + igst_amount) as gst,
            SUM(net_amount) as net, SUM(ofinit_amount) as ofinit, SUM(ofinit_total) as ofinit_total, SUM(refund_amount) as refunds,
            SUM(promo_discount) as promos')->first();

        // Fill rate: booked placement-days ÷ (slots × days in the month).
        $daysInMonth = $start->daysInMonth;
        $placements = AdPlacement::orderBy('sort')->get();
        $usage = AdAvailability::usage($placements->pluck('id')->all(), $start->toDateString(), $end->toDateString());
        $fill = $placements->map(fn ($p) => [
            'code' => $p->code,
            'name' => $p->name,
            'booked' => array_sum($usage[$p->id] ?? []),
            'capacity' => $p->slots * $daysInMonth * ((int) $p->screen_id === AdServer::SCREEN_WEBSITE ? count(AdTargeting::PAGE_GROUPS) : 1),
        ])->map(fn ($r) => $r + ['rate' => $r['capacity'] ? round(min(100, $r['booked'] * 100 / $r['capacity']), 1) : 0]);

        $top = (clone $paid)->join('ad_advertisers as a', 'a.id', '=', 'ad_campaigns.advertiser_id')
            ->groupBy('a.id', 'a.business_name')->selectRaw('a.business_name, COUNT(*) as ads, SUM(ad_campaigns.net_amount) as spend')
            ->orderByDesc('spend')->limit(10)->get();

        $views = (int) DB::table('advertisement_impressions')->whereBetween('date', [$start->toDateString(), $end->toDateString()])->sum('views');
        $clicks = (int) DB::table('advertisement_user_clicks')->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])->count();

        $inReview = AdCampaign::where('status', AdCampaign::IN_REVIEW);

        return [
            'money' => $money,
            'fill' => $fill,
            'top' => $top,
            'views' => $views,
            'clicks' => $clicks,
            'in_review' => (clone $inReview)->count(),
            'late_reviews' => (clone $inReview)->where('submitted_at', '<', now()->subDay())->count(),
            'open_reports' => \App\Models\AdReport::where('status', \App\Models\AdReport::OPEN)->count(),
            'failed_splits' => AdCampaign::whereNotNull('paid_at')->where('transfer_status', 'failed')->count(),
            'live' => AdCampaign::where('status', AdCampaign::APPROVED)->where('start_date', '<=', now('Asia/Kolkata')->toDateString())->where('end_date', '>=', now('Asia/Kolkata')->toDateString())->count(),
        ];
    }
}
