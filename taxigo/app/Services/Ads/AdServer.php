<?php

namespace App\Services\Ads;

use App\Models\Advertisement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

/**
 * Decides which ads are shown, everywhere (PWA pages and the mobile API):
 * only ads that are **approved and paid**, inside their start / end **date
 * and time** (IST), on the screen they were booked for. Falls back to
 * approved "default" (house) ads when a screen has nothing booked.
 *
 * Screen ids are `screen_prices.id`, as stored in `advertisements.screens`.
 */
class AdServer
{
    public const SCREEN_HOME = 1;
    public const SCREEN_BOOK_RIDE = 2;
    public const SCREEN_FINDING_TOP = 3;
    public const SCREEN_BOOKING_CONFIRMED = 4;
    public const SCREEN_DRIVER_DETAILS = 5;
    public const SCREEN_RATING = 6;
    public const SCREEN_ACCOUNT = 7;
    public const SCREEN_FINDING_BOTTOM = 8;
    // Added with self-serve ads (PWA only; older app versions never get these shapes).
    public const SCREEN_HOME_INLINE = 9;
    public const SCREEN_FINDING_LARGE = 10;
    public const SCREEN_FINDING_FULL = 11;
    public const PWA_ONLY_SCREENS = [self::SCREEN_HOME_INLINE, self::SCREEN_FINDING_LARGE, self::SCREEN_FINDING_FULL];

    /** Approved, paid ads whose date + time window contains now (IST). */
    public static function live(): Builder
    {
        $now = now('Asia/Kolkata')->format('Y-m-d H:i:s');

        return Advertisement::query()
            ->where('approval_status', Advertisement::APPROVED)
            ->where('payment_status', 'paid')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->whereRaw("TIMESTAMP(start_date, COALESCE(start_time, '00:00:00')) <= ?", [$now])
            ->whereRaw("TIMESTAMP(end_date, COALESCE(end_time, '23:59:59')) >= ?", [$now]);
    }

    /** Restrict a query to ads booked on a screen (screens JSON holds "3" or 3). */
    public static function onScreen(Builder $query, int $screen): Builder
    {
        return $query->whereRaw('screens REGEXP ?', ['(^|[^0-9])' . $screen . '([^0-9]|$)']);
    }

    /** Live ads for a screen, newest first, or approved house ads if none. */
    public static function forScreen(int $screen, int $limit = 5): Collection
    {
        $ads = self::onScreen(self::live(), $screen)->orderByDesc('id')->limit($limit)->get();
        if ($ads->isNotEmpty()) {
            return $ads;
        }

        return self::onScreen(
            Advertisement::where('default', 1)->where('approval_status', Advertisement::APPROVED),
            $screen
        )->orderByDesc('id')->limit($limit)->get();
    }

    /** What the PWA / apps need to render one ad. */
    public static function payload(Advertisement $ad, ?int $screen, string $platform): array
    {
        return [
            'id' => $ad->id,
            'banner_image' => $ad->add_banner_image,
            'banner_url' => $ad->banner_url,
            'click_url' => self::clickUrl($ad, $screen, $platform),
            'screen' => $screen,
            'sponsored' => true,
        ];
    }

    /** Signed tracking link: the click is counted, then forwarded with UTM tags. */
    public static function clickUrl(Advertisement $ad, ?int $screen, string $platform): ?string
    {
        if (blank($ad->banner_url)) {
            return null;
        }

        return URL::signedRoute('ads.click', array_filter([
            'advertisement' => $ad->id,
            'p' => $platform,
            's' => $screen,
        ], fn ($v) => $v !== null && $v !== ''));
    }

    /**
     * The advertiser's landing URL with tracking added:
     *  - web links get utm_source/medium/campaign/content (existing tags kept),
     *  - WhatsApp links get a prefilled "I saw your ad" message,
     *  - tel: / mailto: are returned unchanged.
     * Returns null for anything that isn't a safe, absolute link.
     */
    public static function landingUrl(Advertisement $ad, ?int $screen, string $platform): ?string
    {
        $url = trim((string) $ad->banner_url);
        if ($url === '') {
            return null;
        }
        if (preg_match('/^(tel|mailto):/i', $url)) {
            return $url;
        }
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }

        $parts = parse_url($url);
        if (!$parts || empty($parts['host']) || !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return null;
        }

        parse_str($parts['query'] ?? '', $query);
        $host = strtolower($parts['host']);
        if (in_array($host, ['wa.me', 'api.whatsapp.com', 'whatsapp.com', 'www.whatsapp.com'], true)) {
            $query += ['text' => 'Hi, I saw your ad on Seema Cabs Goa'];
        } else {
            $query += [
                'utm_source' => 'seemacabsgoa',
                'utm_medium' => $platform,
                'utm_campaign' => 'ad-' . $ad->id,
                'utm_content' => $screen ? 'screen-' . $screen : 'advertise-page',
            ];
        }

        return $parts['scheme'] . '://'
            . (isset($parts['user']) ? $parts['user'] . (isset($parts['pass']) ? ':' . $parts['pass'] : '') . '@' : '')
            . $parts['host']
            . (isset($parts['port']) ? ':' . $parts['port'] : '')
            . ($parts['path'] ?? '/')
            . '?' . http_build_query($query)
            . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
    }
}
