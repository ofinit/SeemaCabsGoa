<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A phone running the Seema Cabs Fleet app (Firebase registration token). */
class FleetDeviceToken extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['last_seen_at' => 'datetime'];
}
