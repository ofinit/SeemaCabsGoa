<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdAdvertiser extends Model
{
    public const ACTIVE = 'active';
    public const BLOCKED = 'blocked';

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(AdAgency::class, 'agency_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AdCategory::class, 'category_id');
    }

    public function licences(): HasMany
    {
        return $this->hasMany(AdLicence::class, 'advertiser_id')->latest('id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(AdCampaign::class, 'advertiser_id');
    }

    public function isBlocked(): bool
    {
        return $this->status === self::BLOCKED;
    }

    /** A licence (not rejected) that is valid on the given date. */
    public function hasLicenceValidUntil(?string $date): bool
    {
        return $this->licences->contains(function (AdLicence $licence) use ($date) {
            return $licence->status !== AdLicence::REJECTED
                && (!$date || !$licence->valid_until || $licence->valid_until->toDateString() >= $date);
        });
    }
}
