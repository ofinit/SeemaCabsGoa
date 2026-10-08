<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalKmCharges extends Model
{
    use SoftDeletes;

    protected $fillable = ['value'];
}
