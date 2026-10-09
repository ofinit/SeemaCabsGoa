<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One printed in-cab card (P18) with its own QR code; scans are counted per card. */
class AdQrCard extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['placed_on' => 'date', 'removed_on' => 'date'];

    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function cab()
    {
        return $this->belongsTo(Cab::class, 'cab_id');
    }

    public function url(): string
    {
        return url('/q/' . $this->code);
    }

    public static function newCode(): string
    {
        do {
            $code = strtoupper(substr(str_replace(['0', 'O', '1', 'I', 'L'], '', base_convert(bin2hex(random_bytes(8)), 16, 36)), 0, 8));
        } while (strlen($code) < 8 || self::where('code', $code)->exists());

        return $code;
    }
}
