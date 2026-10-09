<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Peak-season window: every day inside it is priced × multiplier (plan §5.3). */
class AdPricingRule extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'active' => 'boolean', 'multiplier' => 'float'];
}
