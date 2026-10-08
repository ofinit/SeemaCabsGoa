<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'value',
        'expiry_date',
        'no_expiry',
        'description',
        'status',
        'show_app'
    ];

    public function setExpiryDateAttribute($value){
        $this->attributes['expiry_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}
