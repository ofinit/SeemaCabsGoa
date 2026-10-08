<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tab',
        'from',
        'to',
        'cab_id',
        'base_fare',
    ];

    protected $casts = [
        'base_fare' => 'float',
    ];


    public function cityFrom()
    {
        return $this->belongsTo(City::class, 'from');
    }
    public function cityTo()
    {
        return $this->belongsTo(City::class, 'to');
    }

    public function cabPriceType()
    {
        return $this->hasMany(CabPriceType::class);
    }
    public function cabPriceTypeSingle()
    {
        return $this->hasOne(CabPriceType::class);
    }

    public function priceType()
    {
        return $this->hasMany(CabPriceType::class, 'cab_rate_id', 'id');
    }
}
