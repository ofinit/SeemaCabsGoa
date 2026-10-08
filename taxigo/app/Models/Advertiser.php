<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertiser extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'add_id',
        'date',
        'company_name',
        'name',
        'phone_number',
        'email'
    ];

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'add_id');
    }
}
