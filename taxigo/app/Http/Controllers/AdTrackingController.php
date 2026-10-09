<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdvertisementImpression;
use App\Models\AdReport;
use App\Models\AdvertisementUserClick;
use App\Services\Ads\AdCampaignService;
use App\Services\Ads\AdTargeting;
use App\Services\Ads\AdServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Ad click redirect and viewable-impression beacon. No location data is
 * stored; the logged-in customer (if any) is recorded on clicks only.
 */
class AdTrackingController extends Controller
{
    private const PLATFORMS = ['pwa', 'android', 'ios', 'website', 'email', 'qr', 'app'];

    /** GET /ads/c/{advertisement} (signed): count the click, then forward with UTM tags. */
    public function click(Request $request, Advertisement $advertisement)
    {
        $platform = in_array($request->query('p'), self::PLATFORMS, true) ? $request->query('p') : 'pwa';
        $screen = $request->filled('s') ? (int) $request->query('s') : null;

        $target = AdServer::landingUrl($advertisement, $screen, $platform);
        if (!$target) {
            abort(404);
        }

        AdvertisementUserClick::create([
            'advertisement_id' => $advertisement->id,
            'user_id' => auth('customer')->id() ?? auth()->id(),
            'screen_id' => $screen,
            'platform' => $platform,
        ]);

        return redirect()->away($target, 302, ['Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']);
    }

    /** GET /ads/web?page=… — one live website ad (P9) for a marketing page's group. */
    public function web(Request $request)
    {
        $group = AdTargeting::pageGroupOf((string) $request->query('page', 'index'));
        $ad = $group ? AdServer::forScreen(AdServer::SCREEN_WEBSITE, 10, ['page_group' => $group])->shuffle()->first() : null;

        return response()->json(['ad' => $ad ? AdServer::payload($ad, AdServer::SCREEN_WEBSITE, 'website') : null])
            ->header('Cache-Control', 'no-store')->header('X-Robots-Tag', 'noindex');
    }

    /**
     * POST /app/actions/ads/report (PWA) and /ads/report (website): a viewer
     * reports an ad. One report per ad per person; enough of them pause it.
     */
    public function report(Request $request, AdCampaignService $ads)
    {
        $data = $request->validate([
            'id' => 'required|integer|min:1',
            'reason' => 'required|in:' . implode(',', array_keys(AdReport::REASONS)),
            'note' => 'nullable|string|max:500',
            'platform' => 'nullable|string|max:10',
        ]);
        $ad = Advertisement::find($data['id']);
        if (!$ad) {
            return response()->json(['status' => false, 'message' => 'Ad not found.'], 404);
        }
        $userId = auth('customer')->id();
        $reporter = $userId ? 'u:' . $userId : 'ip:' . substr(hash('sha256', $request->ip() . '|' . config('app.key')), 0, 40);
        $platform = in_array($data['platform'] ?? 'pwa', self::PLATFORMS, true) ? ($data['platform'] ?? 'pwa') : 'pwa';
        $ads->report($ad, $userId, $reporter, $data['reason'], $data['note'] ?? null, $platform);

        return response()->json(['status' => true, 'message' => 'Thanks — our team will review this ad.']);
    }

    /**
     * POST /app/actions/ad-impressions — batched viewable impressions
     * (≥ 50% of the ad visible for ≥ 1 s, counted once per page view).
     * Body: {platform: "pwa", items: [{id: 31, screen: 1}, …]}
     */
    public function impressions(Request $request)
    {
        $data = $request->validate([
            'platform' => 'nullable|string|max:10',
            'items' => 'required|array|max:20',
            'items.*.id' => 'required|integer|min:1',
            'items.*.screen' => 'nullable|integer|min:0|max:65535',
        ]);
        $platform = in_array($data['platform'] ?? 'pwa', self::PLATFORMS, true) ? ($data['platform'] ?? 'pwa') : 'pwa';

        $ids = collect($data['items'])->pluck('id')->unique();
        $known = Advertisement::whereIn('id', $ids)->pluck('id')->flip();
        $today = now('Asia/Kolkata')->toDateString();

        foreach ($data['items'] as $item) {
            if (!isset($known[$item['id']])) {
                continue;
            }
            $screen = (int) ($item['screen'] ?? 0);
            AdvertisementImpression::upsert(
                [[
                    'advertisement_id' => $item['id'],
                    'screen_id' => $screen,
                    'platform' => $platform,
                    'date' => $today,
                    'views' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]],
                ['advertisement_id', 'screen_id', 'platform', 'date'],
                ['views' => DB::raw('views + 1'), 'updated_at' => now()]
            );
        }

        return response()->json(['status' => true]);
    }
}
