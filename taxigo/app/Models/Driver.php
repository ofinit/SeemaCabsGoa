<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cab_id',
        'driver_name',
        'driver_mobile',
        'bank_name',
        'branch_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'upi_id',
        'driving_license_number',
        'aadhar_card_number',
        'driver_profile_picture',
        'front_driver_license',
        'back_driver_license',
        'front_aadhar_card',
        'back_aadhar_card',
        'assignDriver'
    ];

    /**
     * Get the getCabDetails that owns the Driver
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCabDetails()
    {
        return $this->belongsTo(Cab::class, 'cab_id');
    }


}
