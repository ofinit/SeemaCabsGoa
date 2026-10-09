<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A viewer's report of an ad (plan §9.3, §11). */
class AdReport extends Model
{
    public const OPEN = 'open';
    public const DISMISSED = 'dismissed';
    public const ACTIONED = 'actioned';

    public const REASONS = [
        'misleading' => 'Misleading or a scam',
        'offensive' => 'Offensive or inappropriate',
        'alcohol_gambling' => 'Alcohol, betting or gambling',
        'broken' => "Link doesn't work",
        'irrelevant' => 'Not relevant / annoying',
        'other' => 'Something else',
    ];

    protected $guarded = ['id'];

    protected $casts = ['resolved_at' => 'datetime'];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class)->withTrashed();
    }

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }
}
