<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdvertisementImpression;
use App\Models\AdCampaign;
use App\Models\AdConversion;
use App\Models\AdQrCard;
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
    private const PLATFORMS = ['pwa', 'android', 'ios', 'website', 'email', 'qr', 'push', 'app'];

    /** GET /ads/c/{advertisement} (signed): count the click, then forward with UTM tags. */
    public function click(Request $request, Advertisement $advertisement)
    {
        $platform = in_array($request->query('p'), self::PLATFORMS, true) ? $request->query('p') : 'pwa';
        $screen = $request->filled('s') ? (int) $request->query('s') : null;

        // Signed links live on in pushes, emails and cached pages: only forward while the ad
        // is approved (not paused, rejected or cancelled).
        if ($advertisement->approval_status !== Advertisement::APPROVED) {
            return redirect('/')->header('Cache-Control', 'no-store');
        }
        if (!AdServer::landingUrl($advertisement, $screen, $platform)) {
            abort(404);
        }

        $click = AdvertisementUserClick::create([
            'advertisement_id' => $advertisement->id,
            'user_id' => auth('customer')->id() ?? auth()->id(),
            'screen_id' => $screen,
            'platform' => $platform,
        ]);
        $target = AdServer::landingUrl($advertisement, $screen, $platform, $click->id);

        return redirect()->away($target, 302, ['Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']);
    }

    /**
     * Sponsored-push consent (P11), for the PWA (customer session) and the
     * apps (Sanctum). GET returns the setting; POST {on: bool} changes it.
     */
    public function pushOptIn(Request $request)
    {
        $user = auth('customer')->user() ?? $request->user('sanctum');
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Please log in.'], 401);
        }
        if ($request->isMethod('post')) {
            $on = $request->boolean('on');
            $user->forceFill(['ad_push_opt_in' => $on, 'ad_push_opt_in_at' => now()])->save();
        }

        return response()->json(['status' => true, 'data' => ['on' => (bool) $user->ad_push_opt_in]]);
    }

    /** GET /q/{code} — in-cab QR card (P18) scan: counted per card, then on to the advertiser. */
    public function qr(string $code)
    {
        $card = AdQrCard::with('campaign')->where('code', strtoupper($code))->first();
        $ad = $card && $card->campaign
            ? AdServer::onScreen(AdServer::live()->where('ad_campaign_id', $card->campaign_id), AdServer::SCREEN_QR)->first()
            : null;
        if (!$ad || $card->removed_on) {
            return redirect('/')->header('Cache-Control', 'no-store');
        }
        $card->increment('scans');
        $click = AdvertisementUserClick::create([
            'advertisement_id' => $ad->id,
            'user_id' => auth('customer')->id(),
            'screen_id' => AdServer::SCREEN_QR,
            'platform' => 'qr',
        ]);
        $target = AdServer::landingUrl($ad, AdServer::SCREEN_QR, 'qr', $click->id) ?: '/';

        return redirect()->away($target, 302, ['Cache-Control' => 'no-store', 'X-Robots-Tag' => 'noindex, nofollow']);
    }

    /**
     * GET /ads/conv/{token}.gif?click=…&label=…&value=… — conversion tag on
     * the advertiser's website (lead, booking, sale). `click` is the
     * `sc_click` id we added to their landing URL; value is in rupees.
     * One conversion per visitor, label and day. Always returns a 1×1 GIF.
     */
    public function conversion(Request $request, string $token)
    {
        $campaign = AdCampaign::where('conversion_token', $token)->first();
        if ($campaign) {
            $adIds = $campaign->items()->pluck('advertisement_id')->filter();
            $clickId = (int) $request->query('click', 0);
            $click = $clickId ? AdvertisementUserClick::whereKey($clickId)->whereIn('advertisement_id', $adIds)->first() : null;
            $label = substr(preg_replace('/[^a-z0-9_-]/', '', strtolower((string) $request->query('label', 'lead'))), 0, 40) ?: 'lead';
            $value = (int) round(min(10000000, max(0, (float) $request->query('value', 0))) * 100);
            AdConversion::insertOrIgnore([
                'campaign_id' => $campaign->id,
                'click_id' => $click?->id,
                'label' => $label,
                'value' => $value,
                'visitor' => $click ? 'c:' . $click->id : 'ip:' . substr(hash('sha256', $request->ip() . '|' . config('app.key')), 0, 36),
                'date' => now('Asia/Kolkata')->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'), 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, max-age=0',
            'Access-Control-Allow-Origin' => '*',
        ]);
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
        $userId = auth('customer')->id() ?? optional($request->user('sanctum'))->id;
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
