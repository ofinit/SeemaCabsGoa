<?php

namespace App\Services\Ads;

use App\Enums\Type;
use App\Models\AdCampaign;
use App\Models\AdPushSend;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Sponsored push (P11): only to customers who opted in, at most one per
 * person every 7 days, sent between 10:00 and 20:00 IST.
 */
class AdPushService
{
    public const GAP_DAYS = 7;

    /** Customers a push could reach today (opted in, not pushed in the last 7 days, with a device). */
    public static function audience()
    {
        return User::where('type', Type::CUSTOMER)
            ->where('ad_push_opt_in', true)
            ->where(fn ($q) => $q->whereNull('last_ad_push_at')->orWhere('last_ad_push_at', '<', now()->subDays(self::GAP_DAYS)))
            ->whereIn('id', UserFcmToken::whereNotNull('token')->select('user_id'));
    }

    public static function audienceSize(): int
    {
        return self::audience()->count();
    }

    /** Send every scheduled push that is due. Returns how many were sent. */
    public function sendDue(): int
    {
        $hour = (int) now('Asia/Kolkata')->format('G');
        if ($hour < 10 || $hour >= 20) {
            return 0;
        }
        $sent = 0;
        AdPushSend::with('campaign')->where('status', AdPushSend::SCHEDULED)
            ->where('send_on', '<=', now('Asia/Kolkata')->toDateString())->orderBy('send_on')->get()
            ->each(function (AdPushSend $push) use (&$sent) {
                if ($push->campaign?->status !== AdCampaign::APPROVED) {
                    return; // paused: wait; cancelled campaigns cancel their pushes
                }
                $sent += $this->send($push) ? 1 : 0;
            });

        return $sent;
    }

    public function send(AdPushSend $push): bool
    {
        $campaign = $push->campaign;
        $ad = AdServer::onScreen(Advertisement::where('ad_campaign_id', $campaign->id), AdServer::SCREEN_PUSH)->first();
        if (!$ad) {
            $push->forceFill(['status' => AdPushSend::FAILED, 'error' => 'No approved push creative.'])->save();

            return false;
        }

        $userIds = self::audience()->pluck('id');
        $tokens = UserFcmToken::whereIn('user_id', $userIds)->whereNotNull('token')->pluck('token')->all();
        try {
            $result = $tokens
                ? app(NotificationService::class)->sendAdPush(
                    $tokens,
                    'Sponsored · ' . $campaign->headline,
                    (string) $campaign->push_body,
                    $ad->add_banner_image,
                    ['type' => 'sponsored', 'link' => (string) AdServer::clickUrl($ad, AdServer::SCREEN_PUSH, 'push'), 'ad_id' => $ad->id]
                )
                : ['success' => 0, 'failure' => 0];
        } catch (\Throwable $e) {
            Log::error("Sponsored push failed for {$campaign->reference}: " . $e->getMessage());
            $push->forceFill(['status' => AdPushSend::FAILED, 'error' => mb_substr($e->getMessage(), 0, 500)])->save();

            return false;
        }

        User::whereIn('id', $userIds)->update(['last_ad_push_at' => now()]);
        $push->forceFill([
            'status' => AdPushSend::SENT,
            'recipients' => $userIds->count(),
            'delivered' => $result['success'],
            'failed' => $result['failure'],
            'sent_at' => now(),
        ])->save();

        return true;
    }
}
