<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Carbon\Carbon;

class BookingDetail extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $with = ['getSosDetails', 'user', 'getCabDetails','cabRate', 'getPickupFrom', 'getDropTo'];

    protected $fillable = [
        'customer_id',
        'sight_seeing_package_id',
        'cab_rate_id',
        'assigned_driver_id',
        'cab_id',
        'booking_id',
        'booking_date',
        'pickup_date',
        'pickup_time',
        'trip_type',
        'pickup_from',
        'drop_to',
        'pickup_address',
        'drop_of_address',
        'cab_type',
        'cap_model',
        'trip_otp',
        'part_payment',
        'base_fare',
        'surge_price',
        'air_port_drop',
        'tax_amount',
        'total_payment',
        'cash_with_driver',
        'refund',
        'status',
        'payment_status',
        'call_status',
        // pricing v2 snapshot (see FareBreakdown::bookingAttributes)
        'pricing_version',
        'fare_before_markup',
        'markup_percent',
        'markup_amount',
        'taxable_amount',
        'gst_rate',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'sac_code',
        'platform_fee_percent',
        'platform_fee_amount',
        'customer_gstin',
        'customer_legal_name',
        'customer_billing_address',
        'no_show_at',
        'no_show_reason',
        'no_show_by',
    ];

    /** Bookings priced with GST applied (only possible from pricing v2). */
    public function hasGst(): bool
    {
        return (int) $this->pricing_version >= 2 && (float) $this->gst_rate > 0;
    }

    /** v1 bookings stored the 20% markup in tax_amount; it was never GST. */
    public function gstAmount(): float
    {
        return (int) $this->pricing_version >= 2 ? (float) $this->tax_amount : 0.0;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'booking_id');
    }

    public function getBookingDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function getPickupDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }

    public function getSosDetails()
    {
        return $this->hasMany(SosDetails::class, 'booking_details_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
    public function assignDriver()
    {
        return $this->belongsTo(Driver::class, 'assigned_driver_id', 'id');
    }

    public function getCabDetails()
    {
        return $this->belongsTo(Cab::class, 'cab_id');
    }
    public function cabRate()
    {
        return $this->belongsTo(CabRate::class, 'cab_rate_id');
    }


    public function getPickupFrom()
    {
        return $this->belongsTo(City::class, 'pickup_from');
    }
    public function getDropTo()
    {
        return $this->belongsTo(City::class, 'drop_to');
    }

    public function sightSeeingPackage()
    {
        return $this->belongsTo(SightSeeingPackages::class, 'sight_seeing_package_id');
    }
}
