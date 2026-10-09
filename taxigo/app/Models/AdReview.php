<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Audit log of every review decision (plan §11). */
class AdReview extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['checklist' => 'array', 'reason_codes' => 'array'];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
