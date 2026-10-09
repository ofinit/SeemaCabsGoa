<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A sellable ad position (plan §4). `screen_id` is what advertisements.screens stores. */
class AdPlacement extends Model
{
    public const PER_DAY = 'day';
    public const PER_MONTH = 'month';
    public const PER_SEND = 'send';

    protected $guarded = ['id'];

    protected $casts = ['exclusive' => 'boolean', 'active' => 'boolean'];

    /** Price per day in paise for a tier. */
    public function priceFor(string $tier): int
    {
        return (int) ($tier === AdCategory::PREMIUM ? $this->price_premium : $this->price_standard);
    }

    public function isDaily(): bool
    {
        return ($this->billing ?? self::PER_DAY) === self::PER_DAY;
    }

    public function priceUnit(): string
    {
        return match ($this->billing) {
            self::PER_MONTH => 'cab / month',
            self::PER_SEND => 'send',
            default => 'day',
        };
    }

    /** Creatives are cropped once per shape (same ratio and master size). */
    public function shapeKey(): string
    {
        return $this->width . 'x' . $this->height;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('sort');
    }
}
