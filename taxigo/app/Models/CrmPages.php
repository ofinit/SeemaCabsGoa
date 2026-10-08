<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmPages extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'content'
    ];


}
