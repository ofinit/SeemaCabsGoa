<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Cab extends Model
{
    use HasFactory, SoftDeletes;
    protected $with = [
        'getCabModelDetails',
        'getDriverDetails',
        'getColorDetails',
        'getCityDetails',
        'getFleetOperatorDetails',
        'getWaitingChargeDetails',
        'getNoOfKMsDetails',
        'getBaseFareDetails',
        'getAdditionalChargeDetails',
        'getAssignedDriverDetails'
    ];

    protected $fillable = [
        'fleet_operator_id',
        'zone',
        'number',
        'type',
        'model',
        'model_id',
        'color_id',
        'no_of_seats',
        'fuel_type',
        'base_fare',
        'no_of_kms',
        'additional_km_charges',
        'waiting_charges',
        'front_registration_certificate',
        'back_registration_certificate',
        'insurance',
        'status',
        'insurance_expiry_date'
    ];

    // Define relationships if needed
    public function getCabModelDetails()
    {
        return $this->belongsTo(CabModel::class, 'model_id');
    }
    public function getDriverDetails()
    {
        return $this->hasMany(Driver::class, 'cab_id', 'id');
    }

    public function getAssignedDriverDetails()
    {
        return $this->hasOne(Driver::class, 'cab_id', 'id')->where('assignDriver', 1);
    }

    public function getCityDetails()
    {
        return $this->belongsTo(City::class, 'zone');
    }

    public function getZoneNamesAttribute()
    {
        if (!$this->zone) {
            return 'N/A';
        }

        $zoneIds = explode(',', $this->zone);
        $zones = City::whereIn('id', $zoneIds)->pluck('name')->toArray();

        return implode(', ', $zones);
    }

    public function getColorDetails()
    {
        return $this->belongsTo(CabColor::class, 'color_id');
    }

    public function getFleetOperatorDetails()
    {
        return $this->belongsTo(User::class, 'fleet_operator_id', 'id');
    }

    public function getBaseFareDetails()
    {
        return $this->belongsTo(BaseFare::class, 'base_fare');
    }

    public function getNoOfKMsDetails()
    {
        return $this->belongsTo(NoOfKm::class, 'no_of_kms');
    }

    public function getAdditionalChargeDetails()
    {
        return $this->belongsTo(AdditionalKmCharges::class, 'additional_km_charges');
    }

    public function getWaitingChargeDetails()
    {
        return $this->belongsTo(WaitingCharges::class, 'waiting_charges');
    }


    public function getFrontRegistrationImageAttribute()
    {
        return asset('storage/certificate/') . '/' . $this->front_registration_certificate;
    }

    public function getBackRegistrationImageAttribute()
    {
        return asset('storage/certificate/') . '/' . $this->back_registration_certificate;
    }

    public function getInsuranceImageAttribute()
    {
        return asset('storage/insurance/') . '/' . $this->insurance;
    }

    public static function store($data)
    {
        try {
            $cab = new self();
            $cab->fleet_operator_id = Auth::id();
            $cab->zone = implode(',', $data['cab_zone']);
            $cab->number = $data['cab_number'];
            $cab->type = $data['cab_type'];
            $cab->model = $data['cab_model'];
            $cab->model_id = $data['model_id'];
            $cab->color_id = $data['color_id'];
            $cab->no_of_seats = $data['no_of_seats'];
            $cab->fuel_type = $data['fuel_type'];
            // $cab->base_fare = $data['base_fare'];
            // $cab->no_of_kms = $data['no_of_kms'];
            // $cab->additional_km_charges = $data['additional_km_charges'];
            // $cab->waiting_charges = $data['waiting_charges'];
            if (isset($data['front_registration_certificate']) && $data['front_registration_certificate']) {
                $cab->front_registration_certificate = UploadImage($data['front_registration_certificate'], 'certificate');
            }
            if (isset($data['back_registration_certificate']) && $data['back_registration_certificate']) {
                $cab->back_registration_certificate = UploadImage($data['back_registration_certificate'], 'certificate');
            }
            if (isset($data['insurance']) && $data['insurance']) {
                $cab->insurance = UploadImage($data['insurance'], 'insurance');
            }
            $cab->insurance_expiry_date = $data['insurance_expiry_date'] ?? null;
            $cab->status = 1;
            $cab->save();
            return $cab;
        } catch (\Throwable $th) {
            Log::error('Getting error of store cab details :' . $th->getMessage());
            return false;
        }
    }

    public static function updateDetails($data)
    {
        try {
            $cab = self::findOrFail($data['id']);
            // $cab->fleet_operator_id = $data['fleet_operator_id'];
            $cab->zone = implode(',', $data['cab_zone']);
            $cab->number = $data['cab_number'];
            $cab->type = $data['cab_type'];
            $cab->model = $data['cab_model'];
            $cab->model_id = $data['model_id'];
            $cab->color_id = $data['color_id'];
            $cab->no_of_seats = $data['no_of_seats'];
            $cab->fuel_type = $data['fuel_type'];
            // $cab->base_fare = $data['base_fare'];
            // $cab->no_of_kms = $data['no_of_kms'];
            // $cab->additional_km_charges = $data['additional_km_charges'];
            // $cab->waiting_charges = $data['waiting_charges'];
            if (isset($data['front_registration_certificate']) && $data['front_registration_certificate'] != null) {
                $cab->front_registration_certificate = UploadImage($data['front_registration_certificate'], 'certificate');
            }
            if (isset($data['back_registration_certificate']) && $data['back_registration_certificate'] != null) {
                $cab->back_registration_certificate = UploadImage($data['back_registration_certificate'], 'certificate');
            }
            if (isset($data['insurance']) && $data['insurance'] != null) {
                $cab->insurance = UploadImage($data['insurance'], 'insurance');
            }
            if (isset($data['insurance']) && $data['insurance'] != null) {
                $cab->insurance = UploadImage($data['insurance'], 'insurance');
            }
            $cab->insurance_expiry_date = $data['insurance_expiry_date'] ?? null;
            $cab->save();
            return $cab;
        } catch (\Throwable $th) {
            Log::error('Getting error of update cab details :' . $th->getMessage());
            return false;
        }
    }

}
