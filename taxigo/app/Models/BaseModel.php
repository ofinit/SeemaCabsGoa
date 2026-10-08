<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BaseModel extends Model
{
    // Format all dates when returned (for API/JSON)
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('d-m-Y');
    }

    // Optional: Accessor to apply when calling $model->created_at directly
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }


}
