<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SightSeeingPackageCabPrice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sight_seeing_package_id',
        'city_id',
        'sedan_price',
        'suv_price',
        'hatchback_price',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
