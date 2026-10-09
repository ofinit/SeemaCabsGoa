<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A lead / sale reported by the advertiser's website (conversion tag). */
class AdConversion extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['date' => 'date'];
}
