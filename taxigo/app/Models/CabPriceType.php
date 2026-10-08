<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class CabPriceType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cab_rate_id',
        'cab_type',
        'base_fare',
    ];

    public function cabRate()
    {
        return $this->belongsTo(CabRate::class, 'cab_rate_id');
    }

    public static function store($cab_rate_id, $cab_type, $base_fare)
    {
        try {
            $cabPriceType = new self();
            $cabPriceType->cab_rate_id = $cab_rate_id;
            $cabPriceType->cab_type = $cab_type;
            $cabPriceType->base_fare = $base_fare;
            $cabPriceType->save();
            return true;
        } catch (\Throwable $th) {
            Log::error('Getting error of store cabPriceType Data ' . $th->getMessage());
            return false;
        }
    }
}
