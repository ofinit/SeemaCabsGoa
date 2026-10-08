<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SightSeeingPackages extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'start_time',
        'end_time',
        'location',
        'description',
        'terms_and_condition',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function packageImages()
    {
        return $this->hasMany(SightSeeingPackageHasImages::class, 'sight_seeing_package_id');
    }

    public function packageCabPrices()
    {
        return $this->hasMany(SightSeeingPackageCabPrice::class, 'sight_seeing_package_id');
    }
}
