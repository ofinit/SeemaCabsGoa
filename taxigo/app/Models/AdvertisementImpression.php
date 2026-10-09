<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Daily viewable-impression counter per ad, screen and platform. */
class AdvertisementImpression extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];
}
