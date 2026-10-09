<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A self-serve ad booking: one advertiser, one date range, one or more
 * placements. Amounts are in paise. Lifecycle (plan §7):
 *
 *   draft → pending_payment → in_review ⇄ changes_requested → approved → expired
 *   approved ⇄ paused · in_review → rejected (refunded) · draft/pending → cancelled
 *
 * "Scheduled" and "Live" are approved campaigns before / inside their dates.
 */
class AdCampaign extends Model
{
    public const DRAFT = 'draft';
    public const PENDING_PAYMENT = 'pending_payment';
    public const IN_REVIEW = 'in_review';
    public const CHANGES_REQUESTED = 'changes_requested';
    public const APPROVED = 'approved';
    public const PAUSED = 'paused';
    public const EXPIRED = 'expired';
    public const REJECTED = 'rejected';
    public const CANCELLED = 'cancelled';

    /** Statuses that take a slot (pending_payment only while its hold is valid). */
    public const BOOKED = [self::IN_REVIEW, self::CHANGES_REQUESTED, self::APPROVED, self::PAUSED];

    public const REASON_CODES = [
        'R01' => 'Image quality',
        'R02' => 'Wrong crop / unreadable',
        'R03' => 'Alcohol content',
        'R04' => 'Gambling / betting claim',
        'R05' => 'Misleading or unproven claim',
        'R06' => 'Link broken / unsafe',
        'R07' => 'Licence missing / expired',
        'R08' => 'Category / tier mismatch',
        'R09' => 'Adult / offensive',
        'R10' => 'Competitor or impersonation',
        'R11' => 'Image or brand rights',
        'R12' => 'Other',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'hold_expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'transferred_at' => 'datetime',
        'refunded_at' => 'datetime',
        'submitted_at' => 'datetime',
        'first_approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'expired_at' => 'datetime',
        'reason_codes' => 'array',
        'auto_flags' => 'array',
        'targeting' => 'array',
        'exclusive_category' => 'boolean',
        'link_checked_at' => 'datetime',
        'licence_warned_at' => 'datetime',
        'weekly_report_at' => 'datetime',
        'final_report_at' => 'datetime',
        'inter_state' => 'boolean',
        'needs_second_approval' => 'boolean',
        'auto_approve' => 'boolean',
    ];

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(AdAdvertiser::class, 'advertiser_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AdCampaignPlacement::class, 'campaign_id');
    }

    public function creatives(): HasMany
    {
        return $this->hasMany(AdCreative::class, 'campaign_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AdReview::class, 'campaign_id')->latest('id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'ad_campaign_id');
    }

    public function qrCards(): HasMany
    {
        return $this->hasMany(AdQrCard::class, 'campaign_id');
    }

    public function pushSends(): HasMany
    {
        return $this->hasMany(AdPushSend::class, 'campaign_id');
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(AdConversion::class, 'campaign_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(AdCoupon::class, 'coupon_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AdReport::class, 'campaign_id');
    }

    public function renewedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'renewed_from_id');
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::DRAFT, self::CHANGES_REQUESTED], true);
    }

    /** Status shown to people: approved campaigns are Scheduled or Live by date. */
    public function displayStatus(): string
    {
        if ($this->status === self::APPROVED) {
            $today = now('Asia/Kolkata')->toDateString();
            if ($this->end_date && $this->end_date->toDateString() < $today) {
                return 'Ended';
            }

            return $this->start_date && $this->start_date->toDateString() > $today ? 'Scheduled' : 'Live';
        }
        if ($this->status === self::IN_REVIEW && $this->needs_second_approval && $this->first_approved_by) {
            return 'Awaiting second approval';
        }

        return match ($this->status) {
            self::DRAFT => 'Draft',
            self::PENDING_PAYMENT => 'Awaiting payment',
            self::IN_REVIEW => 'In review',
            self::CHANGES_REQUESTED => 'Changes requested',
            self::PAUSED => match ($this->paused_reason) {
                'reports' => 'Paused · reported',
                'licence' => 'Paused · licence expired',
                'link' => 'Paused · link broken',
                default => 'Paused',
            },
            self::EXPIRED => 'Expired',
            self::REJECTED => $this->refunded_at ? 'Rejected · refunded' : 'Rejected',
            self::CANCELLED => $this->refunded_at ? 'Cancelled · refunded' : 'Cancelled',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function gstAmount(): int
    {
        return (int) $this->cgst_amount + (int) $this->sgst_amount + (int) $this->igst_amount;
    }

    public static function rupees(int|float|null $paise): string
    {
        return '₹' . number_format(((int) $paise) / 100, 2);
    }
}
