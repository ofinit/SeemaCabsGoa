<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyDetails extends Model
{
    use SoftDeletes;

    protected $fillable =
        [
            'name',
            'contact_person_name',
            'pan_number',
            'tan_number',
            'gst_number',
            'email',
            'phone_number',
            'address_line_one',
            'address_line_two',
            'country_id',
            'state_id',
            'city_id',
            'zip_code',
            'account_name',
            'account_type',
            'branch_name',
            'account_number',
            'ifsc_code',
            'upi_id',
        ];
}
