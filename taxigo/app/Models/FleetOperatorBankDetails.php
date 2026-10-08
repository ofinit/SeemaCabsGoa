<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FleetOperatorBankDetails extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fleet_perator_id',
        'bank_name',
        'branch_name',
        'holder_name',
        'account_number',
        'ifsc_code',
        'upi_id',
    ];
}
