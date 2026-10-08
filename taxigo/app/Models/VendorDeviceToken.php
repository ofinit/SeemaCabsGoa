<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDeviceToken extends Model
{
    //

    public $fillable = ['fcm_token', 'device_id'];

}
