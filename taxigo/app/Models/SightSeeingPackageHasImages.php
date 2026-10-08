<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SightSeeingPackageHasImages extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sight_seeing_package_id',
        'image'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
