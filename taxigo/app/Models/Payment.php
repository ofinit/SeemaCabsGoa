<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'transaction_id',
        'payment_gateway',
        'pg_order_id',
        'date',
        'amount',
        'amount_settlement',
        'status',
        'transfer_amount_status',
    ];


    public function bookingDetails()
    {
        return $this->belongsTo(BookingDetail::class, 'booking_id');
    }
}
