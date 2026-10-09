<?php

namespace App\Services\Ads;

use App\Enums\Type;
use App\Models\AdAdvertiser;
use App\Models\AdCampaign;
use App\Models\AdCampaignPlacement;
use App\Models\AdCategory;
use App\Models\AdCoupon;
use App\Models\AdCreative;
use App\Models\AdPlacement;
use App\Models\AdPushSend;
use App\Models\AdQrCard;
use App\Models\AdReview;
use App\Models\Advertisement;
use App\Models\AdReport;
use App\Models\SightSeeingPackages;
use App\Models\UserFcmToken;
use App\Services\Invoicing\InvoiceService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Self-serve ad workflow (plan §7, §11, §14). Approved campaigns are served
 * by creating one `advertisements` row per placement, so AdServer, the mobile
 * API and tracking need no changes.
 */
class AdCampaignService
{
    /** Words that flag an ad for a second review (plan §10, automated checks). */
    private const FLAG_WORDS = ['beer', 'whisky', 'whiskey', 'vodka', 'rum', 'wine', 'liquor', 'happy hour', 'free drink', 'free pint',
        'bet', 'betting', 'odds', 'jackpot', 'win money', 'win cash', 'gamble', 'gambling', 'lottery', 'teen patti', 'rummy'];

    private const SHORTENERS = ['bit.ly', 'tinyurl.com', 't.co', 'goo.gl', 'cutt.ly', 'rb.gy', 'is.gd', 'ow.ly', 'shorturl.at', 'tiny.cc', 'rebrand.ly', 'bl.ink', 'shorte.st'];

    public function __construct(
        private readonly AdPaymentService $payments,
        private readonly InvoiceService $invoices,
    ) {
    }

    // ---------------------------------------------------------------- drafts

    /**
     * Create or update a draft: placements, dates, link, targeting and
     * options (headline for P16, exclusivity, coupon). Re-prices it.
     *
     * @param  int[]  $placementIds
     * @param  array{targeting?: array, headline?: ?string, exclusive_category?: bool, coupon_code?: ?string}  $options
     */
    public function saveDraft(?AdCampaign $campaign, AdAdvertiser $advertiser, array $placementIds, string $startDate, int $days, string $landingType, string $landingValue, array $options = []): AdCampaign
    {
        if ($campaign && !$campaign->isEditable()) {
            throw new RuntimeException('This ad can no longer be edited.');
        }
        $settings = AdSettings::load();
        $min = (int) $settings[AdSettings::MIN_DAYS];
        $max = (int) $settings[AdSettings::MAX_DAYS];
        if ($days < $min || $days > $max) {
            throw new RuntimeException("Book between {$min} and {$max} days.");
        }
        $start = Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay();
        if ($start->lt(now('Asia/Kolkata')->startOfDay())) {
            throw new RuntimeException('The start date is in the past.');
        }
        $placements = AdPlacement::active()->whereIn('id', $placementIds)->get();
        if ($placements->isEmpty()) {
            throw new RuntimeException('Choose at least one placement.');
        }
        // Paid campaigns keep their placements, dates and targeting while changes are requested.
        $locked = $campaign && $campaign->isPaid();
        $targeting = AdTargeting::normalize((array) ($options['targeting'] ?? []));
        $headline = trim((string) ($options['headline'] ?? '')) ?: null;
        if ($headline !== null && mb_strlen($headline) > 40) {
            throw new RuntimeException('Keep the one-line message to 40 characters.');
        }
        $pushBody = trim((string) ($options['push_body'] ?? '')) ?: null;
        if ($pushBody !== null && mb_strlen($pushBody) > 120) {
            throw new RuntimeException('Keep the push message to 120 characters.');
        }
        if (!$locked && $placements->contains(fn ($p) => $p->billing === AdPlacement::PER_MONTH) && $days % 30 !== 0) {
            throw new RuntimeException('In-cab QR cards (P18) are booked by the month: choose 30, 60 or 90 days.');
        }
        $couponCode = strtoupper(trim((string) ($options['coupon_code'] ?? ''))) ?: null;
        if ($couponCode && !$locked) {
            $coupon = AdCoupon::findCode($couponCode);
            if (!$coupon) {
                throw new RuntimeException('That coupon code was not found.');
            }
        }

        return DB::transaction(function () use ($campaign, $advertiser, $placements, $start, $days, $landingType, $landingValue, $locked, $targeting, $headline, $pushBody, $couponCode, $options) {
            $campaign = $campaign ?? new AdCampaign(['status' => AdCampaign::DRAFT]);
            $campaign->fill([
                'advertiser_id' => $advertiser->id,
                'user_id' => $advertiser->user_id,
                'landing_type' => $landingType,
                'landing_url' => trim($landingValue) === '' ? null : self::landingUrl($landingType, $landingValue),
                'headline' => $headline,
                'push_body' => $pushBody,
            ]);
            if (!$locked) {
                $campaign->fill([
                    'start_date' => $start->toDateString(),
                    'end_date' => $start->copy()->addDays($days - 1)->toDateString(),
                    'days' => $days,
                    'category_name' => optional($advertiser->category)->name,
                    'targeting' => $targeting,
                    'exclusive_category' => !empty($options['exclusive_category']),
                    'coupon_code' => $couponCode,
                ]);
                $campaign->fill(AdPricing::campaignColumns($this->quoteFor($campaign, $advertiser, $placements)));
            }
            if ($campaign->renewed_from_id && $campaign->isDirty(['landing_url', 'headline', 'push_body'])) {
                $campaign->auto_approve = false;
            }
            $campaign->save();
            if (!$campaign->reference) {
                $campaign->forceFill(['reference' => 'AD' . str_pad((string) $campaign->id, 6, '0', STR_PAD_LEFT)])->save();
            }
            if (!$locked) {
                $this->syncItems($campaign, $placements);
            }

            return $campaign->fresh(['items.placement', 'creatives', 'advertiser.category']);
        });
    }

    /** Price a campaign with its own options (peak days, units, exclusivity, launch offer / coupon). */
    public function quoteFor(AdCampaign $campaign, AdAdvertiser $advertiser, $placements): array
    {
        return AdPricing::quote(
            $placements,
            optional($advertiser->category)->tier ?? AdCategory::STANDARD,
            (int) $campaign->days,
            $advertiser->gstin,
            null,
            [
                'start_date' => $campaign->start_date instanceof Carbon ? $campaign->start_date->toDateString() : (string) $campaign->start_date,
                'units' => self::unitsFor($placements, $campaign->targeting),
                'exclusive_category' => (bool) $campaign->exclusive_category,
                'coupon' => AdCoupon::findCode($campaign->coupon_code),
                'first_booking' => !AdCampaign::where('advertiser_id', $advertiser->id)->whereNotNull('paid_at')
                    ->when($campaign->id, fn ($q) => $q->where('id', '!=', $campaign->id))->exists(),
            ]
        );
    }

    /** Website ads (P9) are priced per page group. */
    public static function unitsFor($placements, ?array $targeting): array
    {
        $units = [];
        foreach ($placements as $placement) {
            $units[$placement->id] = match (true) {
                (int) $placement->screen_id === AdServer::SCREEN_WEBSITE => max(1, count($targeting['page_groups'] ?? [])),
                $placement->billing === AdPlacement::PER_MONTH => max(1, (int) ($targeting['cabs'] ?? 1)),
                default => 1,
            };
        }

        return $units;
    }

    private function syncItems(AdCampaign $campaign, $placements, ?array $quote = null): void
    {
        $quote = $quote ?? $this->quoteFor($campaign, $campaign->advertiser, $placements);
        $byPlacement = collect($quote['items'])->keyBy('placement_id');
        $campaign->items()->whereNotIn('placement_id', $placements->pluck('id'))->delete();
        foreach ($placements as $placement) {
            $item = $byPlacement[$placement->id];
            AdCampaignPlacement::updateOrCreate(
                ['campaign_id' => $campaign->id, 'placement_id' => $placement->id],
                ['price_per_day' => $item['price_per_day'], 'days' => $campaign->days, 'units' => $item['units'], 'subtotal' => $item['subtotal']]
            );
        }
        // Creatives for shapes no longer booked are dropped.
        $shapes = $placements->map->shapeKey()->unique()->all();
        $campaign->creatives()->whereNotIn('shape', $shapes)->delete();
    }

    /** Website → https URL; WhatsApp / call → wa.me / tel: link from a 10-digit number. */
    public static function landingUrl(string $type, string $value): string
    {
        $value = trim($value);
        if (in_array($type, ['whatsapp', 'call'], true)) {
            $digits = preg_replace('/\D/', '', $value);
            if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
                $digits = substr($digits, 2);
            }
            if (!preg_match('/^[6-9]\d{9}$/', $digits)) {
                throw new RuntimeException('Enter a valid 10-digit Indian mobile number.');
            }

            return $type === 'whatsapp' ? 'https://wa.me/91' . $digits : 'tel:+91' . $digits;
        }

        if (!preg_match('#^https?://#i', $value)) {
            $value = 'https://' . $value;
        }
        $parts = parse_url($value);
        if (!$parts || empty($parts['host']) || !str_contains($parts['host'], '.') || strlen($value) > 500) {
            throw new RuntimeException('Enter a valid website address.');
        }
        if (strtolower($parts['scheme']) !== 'https') {
            throw new RuntimeException('The website must use https://.');
        }
        if (in_array(strtolower(preg_replace('/^www\./', '', $parts['host'])), self::SHORTENERS, true)) {
            throw new RuntimeException('Link shorteners are not allowed. Use the full website address.');
        }

        return $value;
    }

    /** Store a creative for one shape of the campaign (new upload or re-crop). */
    public function saveCreative(AdCampaign $campaign, string $shape, array $processed): AdCreative
    {
        if (!$campaign->isEditable()) {
            throw new RuntimeException('This ad can no longer be edited.');
        }
        $creative = AdCreative::updateOrCreate(
            ['campaign_id' => $campaign->id, 'shape' => $shape],
            $processed
        );
        if ($campaign->renewed_from_id) {
            $campaign->forceFill(['auto_approve' => false])->save();
        }

        return $creative;
    }

    // -------------------------------------------------------------- checkout

    /** Everything that must be true before payment / resubmission. Returns problems. */
    public function problems(AdCampaign $campaign): array
    {
        $campaign->loadMissing('items.placement', 'creatives', 'advertiser.category', 'advertiser.licences');
        $problems = [];
        $advertiser = $campaign->advertiser;
        if ($advertiser->isBlocked()) {
            $problems[] = 'Your advertiser account is on hold. Please contact us.';
        }
        if ($campaign->items->isEmpty()) {
            $problems[] = 'Choose at least one placement.';
        }
        $shapes = $campaign->items->map(fn ($i) => $i->placement->shapeKey())->unique();
        $have = $campaign->creatives->pluck('shape')->all();
        foreach ($shapes as $shape) {
            if (!in_array($shape, $have, true)) {
                $problems[] = "Add and crop an image for the {$shape} placements.";
            }
        }
        if (blank($campaign->landing_url)) {
            $problems[] = 'Add the link people open when they tap the ad.';
        }
        $screens = $campaign->items->map(fn ($i) => (int) $i->placement->screen_id)->all();
        if (in_array(AdServer::SCREEN_APP_OPEN, $screens, true) && blank($campaign->headline)) {
            $problems[] = 'Add the one-line message for the app-open sponsor (P16).';
        }
        if (in_array(AdServer::SCREEN_SIGHTSEEING_STOP, $screens, true)
            && !SightSeeingPackages::whereKey((int) ($campaign->targeting['package_id'] ?? 0))->exists()) {
            $problems[] = 'Choose the sightseeing package for the sponsored stop (P14).';
        }
        if (in_array(AdServer::SCREEN_WEBSITE, $screens, true) && empty($campaign->targeting['page_groups'])) {
            $problems[] = 'Choose at least one website page group (P9).';
        }
        if (in_array(AdServer::SCREEN_PUSH, $screens, true) && (blank($campaign->headline) || blank($campaign->push_body))) {
            $problems[] = 'Add the push title and message (P11).';
        }
        $category = $advertiser->category;
        if ($category && $category->licence_required && !$advertiser->hasLicenceValidUntil(optional($campaign->end_date)->toDateString())) {
            $problems[] = "Upload your {$category->licence_label}, valid until the end date of the ad.";
        }
        if (!$campaign->isPaid() && $campaign->start_date && $campaign->start_date->lt(now('Asia/Kolkata')->startOfDay())) {
            $problems[] = 'The start date has passed. Choose new dates.';
        }

        return $problems;
    }

    /** Hold the slots and get the campaign ready to pay. */
    public function startCheckout(AdCampaign $campaign): AdCampaign
    {
        if (!in_array($campaign->status, [AdCampaign::DRAFT, AdCampaign::PENDING_PAYMENT], true) || $campaign->isPaid()) {
            throw new RuntimeException('This ad is not awaiting payment.');
        }
        if ($problems = $this->problems($campaign)) {
            throw new RuntimeException(implode(' ', $problems));
        }

        return DB::transaction(function () use ($campaign) {
            // Lock the placements so two advertisers can't take the last slot together.
            $placements = AdPlacement::whereIn('id', $campaign->items->pluck('placement_id'))->lockForUpdate()->get();
            [$from, $to] = [$campaign->start_date->toDateString(), $campaign->end_date->toDateString()];
            $conflicts = AdAvailability::conflicts($placements, $from, $to, $campaign->id, (array) ($campaign->targeting['page_groups'] ?? []), self::unitsFor($placements, $campaign->targeting));
            if ($conflicts) {
                $list = collect($conflicts)->map(fn ($date, $code) => $code . ' (from ' . Carbon::parse($date)->format('d M') . ')')->implode(', ');
                throw new RuntimeException("Sold out: {$list}. Choose other dates or placements.");
            }
            $advertiser = $campaign->advertiser;
            $category = $advertiser->category_id
                ? AdAvailability::categoryConflicts($placements, $from, $to, (int) $advertiser->category_id, (bool) $campaign->exclusive_category, $campaign->id)
                : [];
            if ($category) {
                $list = collect($category)->map(fn ($why, $code) => "{$code}: {$why}")->implode('; ');
                throw new RuntimeException("Not available — {$list}. Choose other dates or placements" . ($campaign->exclusive_category ? ', or book without exclusivity.' : '.'));
            }
            $coupon = AdCoupon::findCode($campaign->coupon_code);
            $quote = $this->quoteFor($campaign, $advertiser, $placements);
            if ($coupon && ($problem = $coupon->problemFor($advertiser, (int) $quote['list_amount'], $campaign->id))) {
                throw new RuntimeException($problem . ' Remove the coupon to continue.');
            }
            $campaign->fill(AdPricing::campaignColumns($quote));
            $this->syncItems($campaign, $placements, $quote);
            $flags = $this->autoFlags($campaign);
            $campaign->forceFill([
                'status' => AdCampaign::PENDING_PAYMENT,
                'hold_expires_at' => now()->addMinutes((int) AdSettings::get(AdSettings::HOLD_MINUTES)),
                'auto_flags' => $flags ?: null,
                'needs_second_approval' => self::needsSecondApproval($advertiser->category, $flags),
            ])->save();

            return $campaign->fresh(['items.placement', 'advertiser']);
        });
    }

    /** Called once the gateway confirms payment (browser confirm, return URL or webhook). Idempotent. */
    public function paymentReceived(AdCampaign $campaign, string $gateway, string $transactionId): AdCampaign
    {
        $justPaid = false;
        $campaign = DB::transaction(function () use ($campaign, $gateway, $transactionId, &$justPaid) {
            $campaign = AdCampaign::whereKey($campaign->id)->lockForUpdate()->first();
            if ($campaign->isPaid()) {
                return $campaign;
            }
            $campaign->forceFill([
                'payment_gateway' => $gateway,
                'transaction_id' => $transactionId,
                'paid_at' => now(),
                'hold_expires_at' => null,
                'status' => AdCampaign::IN_REVIEW,
                'submitted_at' => now(),
            ])->save();
            $justPaid = true;
            if ($campaign->coupon_id) {
                AdCoupon::whereKey($campaign->coupon_id)->increment('used');
            }

            return $campaign;
        });
        if (!$justPaid) {
            return $campaign;
        }

        try {
            $this->invoices->adInvoice($campaign);
        } catch (\Throwable $e) {
            Log::error("Ad invoice failed for {$campaign->reference}: " . $e->getMessage());
        }
        try {
            $this->payments->transfer($campaign);
        } catch (\Throwable $e) {
            Log::error("OfinIT ad share transfer failed for {$campaign->reference}: " . $e->getMessage());
        }

        // Renewals with an unchanged creative and link go live without another review.
        if ($campaign->auto_approve && $campaign->renewedFrom && in_array($campaign->renewedFrom->status, [AdCampaign::APPROVED, AdCampaign::EXPIRED, AdCampaign::PAUSED], true)) {
            $this->finalApproval($campaign, null, 'Renewal with the same creative and link');
        } else {
            $this->notify($campaign, 'Ad submitted for review', "Thanks! Your ad {$campaign->reference} is paid and in review. We'll get back to you within 24 hours.");
        }

        return $campaign->fresh();
    }

    /** Advertiser resubmits after "changes requested" (no new payment). */
    public function resubmit(AdCampaign $campaign): AdCampaign
    {
        if ($campaign->status !== AdCampaign::CHANGES_REQUESTED) {
            throw new RuntimeException('This ad is not waiting for changes.');
        }
        if ($problems = $this->problems($campaign)) {
            throw new RuntimeException(implode(' ', $problems));
        }
        $flags = $this->autoFlags($campaign);
        $campaign->forceFill([
            'status' => AdCampaign::IN_REVIEW,
            'submitted_at' => now(),
            'first_approved_by' => null,
            'first_approved_at' => null,
            'auto_flags' => $flags ?: null,
            'needs_second_approval' => self::needsSecondApproval($campaign->advertiser->category, $flags),
        ])->save();

        return $campaign;
    }

    /**
     * Two-admin rule (plan §11) — only when switched on in the ad settings.
     * Off by default: one admin approves, and high-risk ads are highlighted.
     */
    public static function needsSecondApproval(?AdCategory $category, array $flags): bool
    {
        return AdSettings::get(AdSettings::SECOND_APPROVAL) === '1'
            && ((bool) optional($category)->second_approval || !empty($flags));
    }

    /** Automated checks (plan §10) — they never block; they are shown to the reviewer. */
    public function autoFlags(AdCampaign $campaign): array
    {
        $flags = [];
        $text = mb_strtolower($campaign->advertiser->business_name . ' ' . $campaign->landing_url);
        foreach (self::FLAG_WORDS as $word) {
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/u', $text)) {
                $flags[] = "Contains \"{$word}\"";
            }
        }
        $shas = $campaign->creatives->pluck('sha1')->all();
        if ($shas && AdCreative::whereIn('sha1', $shas)->whereHas('campaign', fn ($q) => $q->where('advertiser_id', '!=', $campaign->advertiser_id))->exists()) {
            $flags[] = 'Same image used by another advertiser';
        }
        if ($campaign->landing_type === 'website') {
            try {
                $response = Http::timeout(6)->withOptions(['allow_redirects' => ['max' => 5]])->get($campaign->landing_url);
                if ($response->failed()) {
                    $flags[] = 'Link returned HTTP ' . $response->status();
                }
            } catch (\Throwable $e) {
                $flags[] = 'Link could not be opened';
            }
        }

        return $flags;
    }

    // ---------------------------------------------------------------- review

    /**
     * Approve. Campaigns needing two admins stay in review after the first
     * approval; the second approval must come from a different admin.
     */
    public function approve(AdCampaign $campaign, int $adminId, array $checklist, ?string $note): AdCampaign
    {
        if ($campaign->status !== AdCampaign::IN_REVIEW || !$campaign->isPaid()) {
            throw new RuntimeException('Only paid ads in review can be approved.');
        }
        if ($campaign->needs_second_approval && !$campaign->first_approved_by) {
            $campaign->forceFill(['first_approved_by' => $adminId, 'first_approved_at' => now()])->save();
            $this->log($campaign, $adminId, 'first', 'approve', $checklist, [], $note);

            return $campaign;
        }
        if ($campaign->needs_second_approval && (int) $campaign->first_approved_by === $adminId) {
            throw new RuntimeException('A different admin must give the second approval.');
        }
        $this->log($campaign, $adminId, $campaign->needs_second_approval ? 'second' : 'first', 'approve', $checklist, [], $note);

        return $this->finalApproval($campaign, $adminId, $note);
    }

    private function finalApproval(AdCampaign $campaign, ?int $adminId, ?string $note): AdCampaign
    {
        DB::transaction(function () use ($campaign, $adminId, $note) {
            // Approved after the start date: the end date moves forward by the delay.
            $today = now('Asia/Kolkata')->startOfDay();
            if ($campaign->start_date->lt($today)) {
                $delay = (int) round(abs($campaign->start_date->diffInDays($today)));
                $campaign->start_date = $today->toDateString();
                $campaign->end_date = $campaign->end_date->copy()->addDays($delay)->toDateString();
            }
            $campaign->forceFill([
                'status' => AdCampaign::APPROVED,
                'reviewed_at' => now(),
                'reviewed_by' => $adminId,
                'review_note' => $note,
                'reason_codes' => null,
            ])->save();
            $this->materialize($campaign);
        });

        $when = $campaign->start_date->isFuture() ? 'It goes live on ' . $campaign->start_date->format('d M Y') . '.' : 'It is live now.';
        $this->notify($campaign, 'Your ad is approved', "Ad {$campaign->reference} is approved. {$when}");

        return $campaign;
    }

    public function requestChanges(AdCampaign $campaign, int $adminId, array $checklist, array $codes, ?string $note): AdCampaign
    {
        if ($campaign->status !== AdCampaign::IN_REVIEW) {
            throw new RuntimeException('Only ads in review can be sent back for changes.');
        }
        $campaign->forceFill([
            'status' => AdCampaign::CHANGES_REQUESTED,
            'reason_codes' => $codes,
            'review_note' => $note,
            'reviewed_at' => now(),
            'reviewed_by' => $adminId,
            'first_approved_by' => null,
            'first_approved_at' => null,
        ])->save();
        $this->log($campaign, $adminId, 'first', 'changes', $checklist, $codes, $note);
        $this->notify($campaign, 'Changes needed on your ad', "Ad {$campaign->reference}: " . $this->reasonText($codes, $note) . ' Edit it and resubmit — no new payment needed.');

        return $campaign;
    }

    /** Reject: full automatic refund, OfinIT's split reversed, credit note. */
    public function reject(AdCampaign $campaign, int $adminId, array $checklist, array $codes, ?string $note): AdCampaign
    {
        if (!in_array($campaign->status, [AdCampaign::IN_REVIEW, AdCampaign::CHANGES_REQUESTED], true)) {
            throw new RuntimeException('Only ads in review can be rejected.');
        }
        $campaign->forceFill([
            'status' => AdCampaign::REJECTED,
            'reason_codes' => $codes,
            'review_note' => $note,
            'reviewed_at' => now(),
            'reviewed_by' => $adminId,
        ])->save();
        $this->log($campaign, $adminId, 'first', 'reject', $checklist, $codes, $note);
        $refunded = $this->refundAndCredit($campaign, (int) $campaign->total_amount - (int) $campaign->refund_amount, 'Ad rejected');
        $this->notify($campaign, 'Your ad was not approved', "Ad {$campaign->reference}: " . $this->reasonText($codes, $note)
            . ($refunded ? ' A full refund of ' . AdCampaign::rupees($refunded) . ' has been started (5–7 working days).' : ''));

        return $campaign;
    }

    /** Admin cancels a paid ad, refunding the given amount (paise). */
    public function cancel(AdCampaign $campaign, int $adminId, int $refund, ?string $note): AdCampaign
    {
        if (!in_array($campaign->status, [AdCampaign::IN_REVIEW, AdCampaign::CHANGES_REQUESTED, AdCampaign::APPROVED, AdCampaign::PAUSED], true)) {
            throw new RuntimeException('This ad cannot be cancelled.');
        }
        $campaign->forceFill(['status' => AdCampaign::CANCELLED, 'review_note' => $note, 'reviewed_at' => now(), 'reviewed_by' => $adminId])->save();
        $campaign->pushSends()->where('status', AdPushSend::SCHEDULED)->update(['status' => AdPushSend::CANCELLED]);
        $campaign->qrCards()->whereNull('removed_on')->update(['removed_on' => now('Asia/Kolkata')->toDateString()]);
        $this->setServing($campaign, Advertisement::REJECTED, $adminId, 'Campaign cancelled');
        $this->log($campaign, $adminId, 'first', 'cancel', [], [], $note);
        $refunded = $refund > 0 ? $this->refundAndCredit($campaign, $refund, 'Ad cancelled') : 0;
        $this->notify($campaign, 'Your ad was cancelled', "Ad {$campaign->reference} was cancelled." . ($refunded ? ' A refund of ' . AdCampaign::rupees($refunded) . ' has been started.' : ''));

        return $campaign;
    }

    public function pause(AdCampaign $campaign, ?int $adminId, bool $pause, ?string $note = null, ?string $reason = null): AdCampaign
    {
        $from = $pause ? AdCampaign::APPROVED : AdCampaign::PAUSED;
        if ($campaign->status !== $from) {
            throw new RuntimeException($pause ? 'Only approved ads can be paused.' : 'Only paused ads can be resumed.');
        }
        $campaign->forceFill([
            'status' => $pause ? AdCampaign::PAUSED : AdCampaign::APPROVED,
            'paused_reason' => $pause ? ($reason ?? 'admin') : null,
        ])->save();
        $this->setServing($campaign, $pause ? Advertisement::PAUSED : Advertisement::APPROVED, $adminId, $note);
        $this->log($campaign, $adminId ?? 0, $adminId ? 'first' : 'system', $pause ? 'pause' : 'resume', [], [], $note);
        if ($pause && $reason) {
            $this->notify($campaign, 'Your ad is paused', "Ad {$campaign->reference} is paused: {$note} Contact us or fix it in the app to resume.");
        }

        return $campaign;
    }

    /** Advertiser cancels an unpaid draft. */
    public function discard(AdCampaign $campaign): void
    {
        if ($campaign->isPaid() || !in_array($campaign->status, [AdCampaign::DRAFT, AdCampaign::PENDING_PAYMENT], true)) {
            throw new RuntimeException('Paid ads can only be cancelled by our team. Please contact us.');
        }
        $campaign->forceFill(['status' => AdCampaign::CANCELLED, 'hold_expires_at' => null])->save();
    }

    private function refundAndCredit(AdCampaign $campaign, int $amount, string $reason): int
    {
        if (!$campaign->isPaid() || $amount <= 0) {
            return 0;
        }
        try {
            $refunded = $this->payments->refund($campaign, $amount, $reason . ' — ' . $campaign->reference);
        } catch (\Throwable $e) {
            Log::error("Ad refund failed for {$campaign->reference}: " . $e->getMessage());

            return 0;
        }
        try {
            $this->invoices->adCreditNote($campaign, $refunded, $reason);
        } catch (\Throwable $e) {
            Log::error("Ad credit note failed for {$campaign->reference}: " . $e->getMessage());
        }

        return $refunded;
    }

    // --------------------------------------------------------------- serving

    /** One advertisements row per placement, using that shape's creative. */
    public function materialize(AdCampaign $campaign): void
    {
        $campaign->load('items.placement', 'creatives', 'advertiser');
        $creatives = $campaign->creatives->keyBy('shape');
        foreach ($campaign->items as $item) {
            $creative = $creatives[$item->placement->shapeKey()] ?? null;
            if (!$creative) {
                continue;
            }
            $targeting = (array) ($campaign->targeting ?? []);
            if ((int) $item->placement->screen_id !== AdServer::SCREEN_SIGHTSEEING_STOP) {
                unset($targeting['package_id']);
            }
            if ((int) $item->placement->screen_id !== AdServer::SCREEN_WEBSITE) {
                unset($targeting['page_groups']);
            }
            $data = [
                'customer_id' => $campaign->user_id,
                'screens' => json_encode([(string) $item->placement->screen_id]),
                'targeting' => $targeting ?: null,
                'banner_image' => $creative->file,
                'banner_url' => $campaign->landing_url,
                'location' => Type::AllValue,
                'start_date' => $campaign->start_date->toDateString(),
                'start_time' => '00:00:00',
                'end_date' => $campaign->end_date->toDateString(),
                'end_time' => '23:59:59',
                'billing_company_name' => $campaign->advertiser->legal_name ?: $campaign->advertiser->business_name,
                'billing_gst' => $campaign->advertiser->gstin,
                'payment_method' => 'online',
                'default' => 0,
                'total_amount' => $item->subtotal / 100,
                'approval_status' => Advertisement::APPROVED,
                'payment_status' => 'paid',
                'status_changed_at' => now(),
            ];
            $ad = $item->advertisement_id ? Advertisement::withTrashed()->find($item->advertisement_id) : null;
            if ($ad) {
                $ad->restore();
                $ad->fill($data)->save();
            } else {
                $ad = new Advertisement($data);
                $ad->ad_campaign_id = $campaign->id;
                $ad->save();
                $item->forceFill(['advertisement_id' => $ad->id])->save();
            }
        }
        $this->prepareOffline($campaign);
    }

    /** P18: one card (own QR code) per booked cab. P11: schedule the push. */
    private function prepareOffline(AdCampaign $campaign): void
    {
        foreach ($campaign->items as $item) {
            if ($item->placement->billing === AdPlacement::PER_MONTH) {
                for ($n = $campaign->qrCards()->count(); $n < (int) $item->units; $n++) {
                    AdQrCard::create(['campaign_id' => $campaign->id, 'code' => AdQrCard::newCode()]);
                }
            }
            if ((int) $item->placement->screen_id === AdServer::SCREEN_PUSH && !$campaign->pushSends()->exists()) {
                $sendOn = $campaign->start_date->lt(now('Asia/Kolkata')->startOfDay()) ? now('Asia/Kolkata')->toDateString() : $campaign->start_date->toDateString();
                AdPushSend::create(['campaign_id' => $campaign->id, 'send_on' => $sendOn]);
            }
        }
    }

    private function setServing(AdCampaign $campaign, string $status, ?int $adminId, ?string $note): void
    {
        Advertisement::where('ad_campaign_id', $campaign->id)->update([
            'approval_status' => $status,
            'status_changed_by' => $adminId,
            'status_changed_at' => now(),
            'status_note' => $note,
        ]);
    }

    // ------------------------------------------------------------- lifecycle

    /** Draft for the next period with the same placements, image and link. */
    public function renew(AdCampaign $old, ?int $days = null): AdCampaign
    {
        if (!in_array($old->status, [AdCampaign::APPROVED, AdCampaign::PAUSED, AdCampaign::EXPIRED], true)) {
            throw new RuntimeException('Only approved or expired ads can be renewed.');
        }
        $old->load('items', 'creatives', 'advertiser.category');
        $days = max($days ?? (int) $old->days, (int) AdSettings::get(AdSettings::MIN_DAYS));
        $start = $old->end_date->copy()->addDay();
        $today = now('Asia/Kolkata')->startOfDay();
        if ($start->lt($today)) {
            $start = $today;
        }
        $type = $old->landing_type;
        $value = match ($type) {
            'whatsapp' => substr(preg_replace('/\D/', '', $old->landing_url), -10),
            'call' => substr(preg_replace('/\D/', '', $old->landing_url), -10),
            default => $old->landing_url,
        };

        $new = $this->saveDraft(null, $old->advertiser, $old->items->pluck('placement_id')->all(), $start->toDateString(), $days, $type, $value, [
            'targeting' => (array) $old->targeting,
            'headline' => $old->headline,
            'push_body' => $old->push_body,
            'exclusive_category' => (bool) $old->exclusive_category,
        ]);
        foreach ($old->creatives as $creative) {
            AdCreative::updateOrCreate(
                ['campaign_id' => $new->id, 'shape' => $creative->shape],
                $creative->only(['original_path', 'crop', 'file', 'width', 'height', 'bytes', 'sha1'])
            );
        }
        $new->forceFill(['renewed_from_id' => $old->id, 'auto_approve' => true])->save();

        return $new->fresh(['items.placement', 'creatives']);
    }

    /** Hourly: expire ended ads, send 3-day reminders, drop stale drafts. */
    public function maintain(): array
    {
        $today = now('Asia/Kolkata')->toDateString();
        $expired = 0;
        AdCampaign::whereIn('status', [AdCampaign::APPROVED, AdCampaign::PAUSED])->where('end_date', '<', $today)->get()
            ->each(function (AdCampaign $campaign) use (&$expired) {
                $campaign->forceFill(['status' => AdCampaign::EXPIRED, 'expired_at' => now()])->save();
                $expired++;
                $this->notify($campaign, 'Your ad has ended', "Ad {$campaign->reference} finished on {$campaign->end_date->format('d M Y')}. See the results and renew in the app.");
                app(AdReporting::class)->sendFinalReport($campaign);
            });

        $licences = $this->checkLicences();
        $links = $this->checkLinks();
        $pushes = app(AdPushService::class)->sendDue();

        $reminded = 0;
        AdCampaign::where('status', AdCampaign::APPROVED)->whereNull('reminder_sent_at')
            ->where('end_date', '<=', now('Asia/Kolkata')->addDays(3)->toDateString())->where('end_date', '>=', $today)
            ->where('days', '>', 3)->get()
            ->each(function (AdCampaign $campaign) use (&$reminded) {
                $campaign->forceFill(['reminder_sent_at' => now()])->save();
                $reminded++;
                $this->notify($campaign, 'Your ad ends soon', "Ad {$campaign->reference} ends on {$campaign->end_date->format('d M Y')}. Renew now to keep your slot.");
            });

        // Payments the browser never confirmed (closed tab, network drop).
        $recovered = 0;
        AdCampaign::where('status', AdCampaign::PENDING_PAYMENT)->whereNull('paid_at')->whereNotNull('pg_order_id')
            ->where('updated_at', '>', now()->subDays(3))->get()
            ->each(function (AdCampaign $campaign) use (&$recovered) {
                if ($transactionId = $this->payments->reconcile($campaign)) {
                    $this->paymentReceived($campaign, $campaign->payment_gateway, $transactionId);
                    $recovered++;
                }
            });

        $stale = AdCampaign::whereIn('status', [AdCampaign::DRAFT, AdCampaign::PENDING_PAYMENT])->whereNull('paid_at')
            ->where('updated_at', '<', now()->subDays(14))->update(['status' => AdCampaign::CANCELLED, 'hold_expires_at' => null]);

        return compact('expired', 'reminded', 'recovered', 'stale', 'licences', 'links', 'pushes');
    }

    /**
     * Licence expiry (plan §11): pause live ads of categories that need a
     * licence once no valid licence is on file; warn 7 days ahead.
     */
    private function checkLicences(): int
    {
        $today = now('Asia/Kolkata')->toDateString();
        $paused = 0;
        AdCampaign::with('advertiser.category', 'advertiser.licences')->where('status', AdCampaign::APPROVED)->get()
            ->filter(fn ($c) => optional($c->advertiser->category)->licence_required)
            ->each(function (AdCampaign $campaign) use ($today, &$paused) {
                $advertiser = $campaign->advertiser;
                if (!$advertiser->hasLicenceValidUntil($today)) {
                    $this->pause($campaign, null, true, 'your ' . $advertiser->category->licence_label . ' has expired. Upload the renewed licence.', 'licence');
                    $paused++;

                    return;
                }
                $lastsTo = $advertiser->licences->where('status', '!=', \App\Models\AdLicence::REJECTED)->max(fn ($l) => optional($l->valid_until)->toDateString());
                if ($lastsTo && $lastsTo < $campaign->end_date->toDateString() && $lastsTo <= now('Asia/Kolkata')->addDays(7)->toDateString() && !$campaign->licence_warned_at) {
                    $campaign->forceFill(['licence_warned_at' => now()])->save();
                    $this->notify($campaign, 'Licence expiring soon', "Your {$advertiser->category->licence_label} expires on " . Carbon::parse($lastsTo)->format('d M Y') . ". Upload the renewed licence so ad {$campaign->reference} isn't paused.");
                }
            });

        return $paused;
    }

    /** Daily link check (plan §11): two failures in a row pause the ad. */
    private function checkLinks(): int
    {
        $paused = 0;
        AdCampaign::where('status', AdCampaign::APPROVED)->where('landing_type', 'website')
            ->where(fn ($q) => $q->whereNull('link_checked_at')->orWhere('link_checked_at', '<', now()->subHours(23)))
            ->limit(50)->get()
            ->each(function (AdCampaign $campaign) use (&$paused) {
                $ok = self::linkWorks($campaign->landing_url);
                $failures = $ok ? 0 : (int) $campaign->link_failures + 1;
                $campaign->forceFill(['link_checked_at' => now(), 'link_failures' => $failures])->save();
                if ($failures >= 2) {
                    $this->pause($campaign, null, true, "the link {$campaign->landing_url} isn't opening. Fix your website or change the link.", 'link');
                    $paused++;
                }
            });

        return $paused;
    }

    public static function linkWorks(?string $url): bool
    {
        if (!$url) {
            return false;
        }
        try {
            $response = Http::timeout(10)->withOptions(['allow_redirects' => ['max' => 5]])
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (SeemaCabsGoa link check)'])->get($url);

            return $response->status() < 400 || in_array($response->status(), [401, 403, 405, 429], true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * A viewer reports an ad. Reaching the threshold of distinct reporters
     * pauses it until an admin reviews the reports.
     */
    public function report(Advertisement $ad, ?int $userId, string $reporterKey, string $reason, ?string $note, string $platform): AdReport
    {
        $report = AdReport::firstOrCreate(
            ['advertisement_id' => $ad->id, 'reporter' => $reporterKey],
            ['campaign_id' => $ad->ad_campaign_id, 'user_id' => $userId, 'reason' => $reason, 'note' => $note ? mb_substr($note, 0, 500) : null, 'platform' => $platform]
        );
        $threshold = (int) AdSettings::get(AdSettings::REPORT_THRESHOLD);
        $open = AdReport::where('status', AdReport::OPEN)
            ->where(fn ($q) => $ad->ad_campaign_id ? $q->where('campaign_id', $ad->ad_campaign_id) : $q->where('advertisement_id', $ad->id))
            ->distinct('reporter')->count('reporter');
        if ($open >= $threshold) {
            $campaign = $ad->ad_campaign_id ? AdCampaign::find($ad->ad_campaign_id) : null;
            if ($campaign && $campaign->status === AdCampaign::APPROVED) {
                $this->pause($campaign, null, true, 'several people reported it. Our team is reviewing it.', 'reports');
            } elseif (!$campaign && $ad->approval_status === Advertisement::APPROVED) {
                $ad->forceFill(['approval_status' => Advertisement::PAUSED, 'status_changed_at' => now(), 'status_note' => 'Auto-paused: user reports'])->save();
            }
        }

        return $report;
    }

    // ----------------------------------------------------------------- misc

    private function log(AdCampaign $campaign, int $adminId, string $stage, string $decision, array $checklist, array $codes, ?string $note): void
    {
        AdReview::create([
            'campaign_id' => $campaign->id,
            'reviewer_id' => $adminId,
            'stage' => $stage,
            'decision' => $decision,
            'checklist' => $checklist ?: null,
            'reason_codes' => $codes ?: null,
            'note' => $note,
        ]);
    }

    private function reasonText(array $codes, ?string $note): string
    {
        $reasons = collect($codes)->map(fn ($c) => AdCampaign::REASON_CODES[$c] ?? $c)->implode(', ');

        return trim(($reasons ? $reasons . '.' : '') . ($note ? ' ' . $note : '')) ?: 'Please review the content guidelines.';
    }

    public function notify(AdCampaign $campaign, string $title, string $body): void
    {
        try {
            $data = ['type' => 'ad_campaign', 'campaign' => $campaign->reference];
            $link = route('customer.ads.show', $campaign);
            $service = new NotificationService();
            $service->storeUserNotification((string) $campaign->user_id, $title, $body, $data, $link);
            $token = UserFcmToken::where('user_id', $campaign->user_id)->value('token');
            if ($token) {
                $service->sendUserNotification($token, $title, $body, $data, $campaign->user_id);
            }
        } catch (\Throwable $e) {
            Log::warning("Ad notification failed for {$campaign->reference}: " . $e->getMessage());
        }
    }
}
