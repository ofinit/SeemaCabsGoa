<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A set of placements sold together at a lower per-day price (plan §5.2). */
class AdBundle extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['placement_codes' => 'array', 'active' => 'boolean'];

    public function priceFor(string $tier): int
    {
        return (int) ($tier === AdCategory::PREMIUM ? $this->price_premium : $this->price_standard);
    }
}
