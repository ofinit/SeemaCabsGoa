<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** An agency login that manages ads for several client businesses. */
class AdAgency extends Model
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const BLOCKED = 'blocked';

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->hasMany(AdAdvertiser::class, 'user_id', 'user_id');
    }

    public function isApproved(): bool
    {
        return $this->status === self::APPROVED;
    }
}
