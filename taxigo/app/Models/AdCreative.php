<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One cropped WebP per campaign and shape; the original upload stays private. */
class AdCreative extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['crop' => 'array'];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function url(): string
    {
        return asset('storage/banner/' . $this->file);
    }
}
