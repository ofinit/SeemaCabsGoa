<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCampaignPlacement extends Model
{
    protected $guarded = ['id'];

    public function placement()
    {
        return $this->belongsTo(AdPlacement::class, 'placement_id');
    }

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'advertisement_id');
    }
}
