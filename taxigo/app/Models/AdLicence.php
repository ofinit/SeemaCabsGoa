<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Licence / registration document. Files are on the private `local` disk. */
class AdLicence extends Model
{
    public const PENDING = 'pending';
    public const VERIFIED = 'verified';
    public const REJECTED = 'rejected';

    protected $guarded = ['id'];

    protected $casts = ['valid_until' => 'date', 'verified_at' => 'datetime'];

    public function advertiser()
    {
        return $this->belongsTo(AdAdvertiser::class, 'advertiser_id');
    }
}
