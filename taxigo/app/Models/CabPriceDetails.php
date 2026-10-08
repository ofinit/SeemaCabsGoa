<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CabPriceDetails extends Model
{
    use SoftDeletes;

    protected $fillable = ['cab_type','base_fare','no_of_kms','additional_km_charges','waiting_charges',];
}
