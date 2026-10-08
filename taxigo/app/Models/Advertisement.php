<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use Carbon\Carbon;
class Advertisement extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'screens',
        'banner_image',
        'banner_url',
        'gender',
        'country_id',
        'state_id',
        'location',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'billing_company_name',
        'billing_gst',
        'billing_pan',
        'payment_method',
        'default',
        'total_amount',
    ];

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function location()
    {
        return $this->belongsTo(State::class, 'location');
    }


    public function getAddBannerImageAttribute()
    {
        return asset('storage/banner/') . '/' . $this->banner_image;
    }

    public function advertisementUserClicks()
    {
        return $this->hasMany(AdvertisementUserClick::class, 'advertisement_id', 'id');
    }

}
