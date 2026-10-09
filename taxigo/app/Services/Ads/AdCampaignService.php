<?php

namespace App\Services\Ads;

use App\Enums\Type;
use App\Models\AdCampaign;
use App\Models\AdCampaignPlacement;
use App\Models\AdCategory;
use App\Models\AdCreative;
use App\Models\AdPlacement;
use App\Models\AdReview;
use App\Models\Advertisement;
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
     * Create or update a draft: placements, dates and link. Re-prices it.
     *
     * @param  int[]  $placementIds
     */
    public function saveDraft(?AdCampaign $campaign, \App\Models\AdAdvertiser $advertiser, array $placementIds, string $startDate, int $days, string $landingType, string $landingValue): AdCampaign
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
        // Paid campaigns keep their placements and dates while changes are requested.
        $locked = $campaign && $campaign->isPaid();

        return DB::transaction(function () use ($campaign, $advertiser, $placements, $start, $days, $landingType, $landingValue, $locked) {
            $campaign = $campaign ?? new AdCampaign(['status' => AdCampaign::DRAFT]);
            $campaign->fill([
                'advertiser_id' => $advertiser->id,
                'user_id' => $advertiser->user_id,
                'landing_type' => $landingType,
                'landing_url' => trim($landingValue) === '' ? null : self::landingUrl($landingType, $landingValue),
            ]);
            if (!$locked) {
                $campaign->fill([
                    'start_date' => $start->toDateString(),
                    'end_date' => $start->copy()->addDays($days - 1)->toDateString(),
                    'category_name' => optional($advertiser->category)->name,
                ]);
                $campaign->fill(AdPricing::campaignColumns(AdPricing::quote($placements, optional($advertiser->category)->tier ?? AdCategory::STANDARD, $days, $advertiser->gstin)));
            }
            if ($campaign->renewed_from_id && $campaign->isDirty('landing_url')) {
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

    private function syncItems(AdCampaign $campaign, $placements): void
    {
        $tier = $campaign->tier;
        $campaign->items()->whereNotIn('placement_id', $placements->pluck('id'))->delete();
        foreach ($placements as $placement) {
            AdCampaignPlacement::updateOrCreate(
                ['campaign_id' => $campaign->id, 'placement_id' => $placement->id],
                ['price_per_day' => $placement->priceFor($tier), 'days' => $campaign->days, 'subtotal' => $placement->priceFor($tier) * $campaign->days]
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
            $conflicts = AdAvailability::conflicts($placements, $campaign->start_date->toDateString(), $campaign->end_date->toDateString(), $campaign->id);
            if ($conflicts) {
                $list = collect($conflicts)->map(fn ($date, $code) => $code . ' (from ' . Carbon::parse($date)->format('d M') . ')')->implode(', ');
                throw new RuntimeException("Sold out: {$list}. Choose other dates or placements.");
            }
            $advertiser = $campaign->advertiser;
            $campaign->fill(AdPricing::campaignColumns(AdPricing::quote($placements, optional($advertiser->category)->tier ?? AdCategory::STANDARD, (int) $campaign->days, $advertiser->gstin)));
            $this->syncItems($campaign, $placements);
            $flags = $this->autoFlags($campaign);
            $campaign->forceFill([
                'status' => AdCampaign::PENDING_PAYMENT,
                'hold_expires_at' => now()->addMinutes((int) AdSettings::get(AdSettings::HOLD_MINUTES)),
                'auto_flags' => $flags ?: null,
                'needs_second_approval' => (bool) optional($advertiser->category)->second_approval || !empty($flags),
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
            'needs_second_approval' => (bool) optional($campaign->advertiser->category)->second_approval || !empty($flags),
        ])->save();

        return $campaign;
    }

    /** Automated checks (plan §10) — they never block; they require a second admin. */
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
        $this->setServing($campaign, Advertisement::REJECTED, $adminId, 'Campaign cancelled');
        $this->log($campaign, $adminId, 'first', 'cancel', [], [], $note);
        $refunded = $refund > 0 ? $this->refundAndCredit($campaign, $refund, 'Ad cancelled') : 0;
        $this->notify($campaign, 'Your ad was cancelled', "Ad {$campaign->reference} was cancelled." . ($refunded ? ' A refund of ' . AdCampaign::rupees($refunded) . ' has been started.' : ''));

        return $campaign;
    }

    public function pause(AdCampaign $campaign, int $adminId, bool $pause, ?string $note = null): AdCampaign
    {
        $from = $pause ? AdCampaign::APPROVED : AdCampaign::PAUSED;
        if ($campaign->status !== $from) {
            throw new RuntimeException($pause ? 'Only approved ads can be paused.' : 'Only paused ads can be resumed.');
        }
        $campaign->forceFill(['status' => $pause ? AdCampaign::PAUSED : AdCampaign::APPROVED])->save();
        $this->setServing($campaign, $pause ? Advertisement::PAUSED : Advertisement::APPROVED, $adminId, $note);
        $this->log($campaign, $adminId, 'first', $pause ? 'pause' : 'resume', [], [], $note);

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
            $data = [
                'customer_id' => $campaign->user_id,
                'screens' => json_encode([(string) $item->placement->screen_id]),
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

        $new = $this->saveDraft(null, $old->advertiser, $old->items->pluck('placement_id')->all(), $start->toDateString(), $days, $type, $value);
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
            });

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

        return compact('expired', 'reminded', 'recovered', 'stale');
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
