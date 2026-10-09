<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A scheduled sponsored push (P11). */
class AdPushSend extends Model
{
    public const SCHEDULED = 'scheduled';
    public const SENT = 'sent';
    public const FAILED = 'failed';
    public const CANCELLED = 'cancelled';

    protected $guarded = ['id'];

    protected $casts = ['send_on' => 'date', 'sent_at' => 'datetime'];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }
}
