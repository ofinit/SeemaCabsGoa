<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A sellable ad position (plan §4). `screen_id` is what advertisements.screens stores. */
class AdPlacement extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['exclusive' => 'boolean', 'active' => 'boolean'];

    /** Price per day in paise for a tier. */
    public function priceFor(string $tier): int
    {
        return (int) ($tier === AdCategory::PREMIUM ? $this->price_premium : $this->price_standard);
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
