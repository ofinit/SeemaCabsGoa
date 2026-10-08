<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class FleetOperator extends Model
{
    use SoftDeletes;

    protected $with = ['getCountryDetails', 'getStateDetails', 'getBankDetails'];
    protected $fillable = [
        'company_name',
        'person_name',
        'mobile_number',
        'email_id',
        'mobile_number_two',
        'mobile_number_three',
        'country_id',
        'state_id',
        'city_id',
        'zip_code',
        'pan_number',
        'gst_number',
        'aadhar_number',
        'front_side_aadhar',
        'back_side_aadhar',
        'company_license',
        'aggrement',
        'password',
        'razorpay_account',
        'cashfree_vendor_id',
    ];

    public function getCountryDetails()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function getStateDetails()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function getBankDetails()
    {
        return $this->hasMany(FleetOperatorBankDetails::class, 'fleet_perator_id');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setFrontSideAadharAttribute($value)
    {
        $imageName = UploadImage($value, 'aadhar');
        $this->attributes['front_side_aadhar'] = $imageName;
    }

    public function setBackSideAadharAttribute($value)
    {
        $imageName = UploadImage($value, 'aadhar');
        $this->attributes['back_side_aadhar'] = $imageName;
    }

    public function setCompanyLicenseAttribute($value)
    {
        $imageName = UploadImage($value, 'license');
        $this->attributes['company_license'] = $imageName;
    }

    public function setAggrementAttribute($value)
    {
        $imageName = UploadImage($value, 'aggrement');
        $this->attributes['aggrement'] = $imageName;
    }
}
