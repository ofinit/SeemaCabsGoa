<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScreenPrice extends Model
{
    use SoftDeletes;

    protected $fillable =
        [
            'title',
            'screen',
            'price_per_day',
            'image',
        ];
}
