<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementUserClick extends Model
{
    protected $fillable = [
        'advertisement_id',
        'user_id',
        'latitude',
        'longitude',
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


