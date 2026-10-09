<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdvertisementImpression;
use App\Models\AdvertisementUserClick;
use App\Services\Ads\AdServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Ad click redirect and viewable-impression beacon. No location data is
 * stored; the logged-in customer (if any) is recorded on clicks only.
 */
class AdTrackingController extends Controller
{
    private const PLATFORMS = ['pwa', 'android', 'ios', 'website', 'qr', 'app'];

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
