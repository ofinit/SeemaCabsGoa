<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SosDetails extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_details_id',
        'customer_id',
        'driver_id',
        'cab_id'
    ];
}
