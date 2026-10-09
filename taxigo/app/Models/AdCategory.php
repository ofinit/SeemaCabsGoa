<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdCategory extends Model
{
    public const STANDARD = 'standard';
    public const PREMIUM = 'premium';

    protected $guarded = ['id'];

    protected $casts = ['licence_required' => 'boolean', 'second_approval' => 'boolean', 'active' => 'boolean'];
}
