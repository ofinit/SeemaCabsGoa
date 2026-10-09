<?php

namespace App\Http\Controllers\Api;

use App\Enums\Type;
use App\Http\Resources\Api\AdvertisementListResource;
use App\Models\Advertisement;
use App\Models\AdvertisementUserClick;
use App\Services\Ads\AdServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AdvertisementController extends ResponseController
{
    /**
     * Ads for the mobile apps and the PWA. Same response shape as before
     * (`data` = top ads, `second_data` = bottom ads), but only approved, paid
     * ads inside their date + time window are returned (AdServer::live()).
     * Optional `screen` (screen_prices.id) returns that screen's ads in `data`.
     * Gender targeting is no longer applied; area (state) targeting is kept.
     */
    public function index(Request $request)
    {
        try {
            $stateId = (int) ($request->state ?? 0);
            $all = Type::AllValue;
            $platform = in_array($request->platform, ['android', 'ios', 'pwa'], true) ? $request->platform : 'app';

            $query = AdServer::live();
            $token = $request->bearerToken();
            $loggedIn = $token && optional(PersonalAccessToken::findToken($token))->tokenable;
            if ($loggedIn) {
                if ($stateId > 0) {
                    $query->where(function ($q) use ($stateId, $all) {
                        $q->where('location', $stateId)
                            ->orWhere('location', $all)
                            ->orWhere('country_id', $all)
                            ->orWhere('state_id', $all);
                    });
                }
            } else {
                $query->where(function ($q) use ($stateId, $all) {
                    $q->where('location', $stateId)->orWhere('location', $all);
                });
            }

            if ($request->filled('screen')) {
                $screen = (int) $request->screen;
                $ads = AdServer::onScreen($query, $screen)->orderByDesc('id')->get();
                if ($ads->isEmpty()) {
                    $ads = AdServer::forScreen($screen);
                }

                return $this->success([
                    'data' => $this->rows($ads, $screen, $platform),
                    'second_data' => [],
                ], 'Advertisement list fetched successfully.');
            }

            // Legacy split used by existing app versions: ads booked on the
            // finding-a-taxi bottom slot go to second_data, the rest to data.
            $isBottom = fn ($ad) => in_array(
                AdServer::SCREEN_FINDING_BOTTOM,
                array_map('intval', (array) json_decode((string) $ad->screens, true)),
                true
            );
            $allAds = $query->orderByDesc('id')->get();
            $bottomAds = $allAds->filter($isBottom)->values();
            $topAds = $allAds->reject($isBottom)->values();

            if ($topAds->isEmpty()) {
                $topAds = Advertisement::where('default', 1)->where('approval_status', Advertisement::APPROVED)
                    ->orderByDesc('id')->get()->reject($isBottom)->values();
            }
            if ($bottomAds->isEmpty()) {
                $bottomAds = AdServer::forScreen(AdServer::SCREEN_FINDING_BOTTOM);
            }

            return $this->success([
                'data' => $this->rows($topAds->unique('id'), null, $platform),
                'second_data' => $this->rows($bottomAds->unique('id'), AdServer::SCREEN_FINDING_BOTTOM, $platform),
            ], 'Advertisement list fetched successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of fetched advertisement list :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again later.');
        }
    }

    /** Resource rows plus a tracked click link and the "Sponsored" flag. */
    private function rows($ads, ?int $screen, string $platform): array
    {
        return $ads->values()->map(function (Advertisement $ad) use ($screen, $platform) {
            $row = (new AdvertisementListResource($ad))->resolve();
            $row['click_url'] = AdServer::clickUrl($ad, $screen, $platform);
            $row['sponsored'] = true;

            return $row;
        })->all();
    }

    public function manageClick(Request $request)
    {
        try {
            $auth_user = Auth::user();

            // Location is no longer stored with ad clicks (privacy).
            AdvertisementUserClick::create([
                'advertisement_id' => $request->id,
                'user_id' => $auth_user->id ?? null,
                'screen_id' => $request->filled('screen') ? (int) $request->screen : null,
                'platform' => in_array($request->platform, ['android', 'ios', 'pwa'], true) ? $request->platform : 'app',
            ]);

            return $this->success([], ' successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of manage advertisement clicks :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
