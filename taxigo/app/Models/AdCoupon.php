<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Admin-created discount code: percent or flat (paise) off the ad price. */
class AdCoupon extends Model
{
    public const PERCENT = 'percent';
    public const FLAT = 'flat';

    protected $guarded = ['id'];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'active' => 'boolean',
        'first_booking_only' => 'boolean',
    ];

    public static function findCode(?string $code): ?self
    {
        $code = strtoupper(trim((string) $code));

        return $code === '' ? null : self::where('code', $code)->first();
    }

    /** Why the coupon can't be used, or null if it can. */
    public function problemFor(AdAdvertiser $advertiser, int $amount, ?int $exceptCampaignId = null): ?string
    {
        $today = now('Asia/Kolkata')->toDateString();
        if (!$this->active || ($this->valid_from && $this->valid_from->toDateString() > $today) || ($this->valid_until && $this->valid_until->toDateString() < $today)) {
            return 'This coupon is not valid now.';
        }
        if ($this->max_uses !== null && $this->used >= $this->max_uses) {
            return 'This coupon has been fully used.';
        }
        $paid = AdCampaign::where('advertiser_id', $advertiser->id)->whereNotNull('paid_at')
            ->when($exceptCampaignId, fn ($q) => $q->where('id', '!=', $exceptCampaignId));
        if ($this->first_booking_only && (clone $paid)->exists()) {
            return 'This coupon is for your first ad only.';
        }
        if ((clone $paid)->where('coupon_id', $this->id)->count() >= max(1, (int) $this->per_advertiser)) {
            return 'You have already used this coupon.';
        }
        if ($amount < (int) $this->min_amount) {
            return 'This coupon needs a booking of at least ' . AdCampaign::rupees($this->min_amount) . '.';
        }

        return null;
    }

    /** Discount (paise) on an amount. */
    public function discountOn(int $amount): int
    {
        $discount = $this->type === self::FLAT ? (int) $this->value : (int) round($amount * min(100, (int) $this->value) / 100);

        return max(0, min($discount, $amount));
    }

    public function label(): string
    {
        return $this->type === self::FLAT ? AdCampaign::rupees($this->value) . ' off' : $this->value . '% off';
    }
}
