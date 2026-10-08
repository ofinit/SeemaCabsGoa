<?php

namespace App\Http\Requests\Admin\Cabs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCabRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Change to authorization logic if needed
    }

    public function rules(): array
    {
        return [
            // 'fleet_operator_id'      => 'required|exists:fleet_operators,id',
            'cab_zone'               => 'required|array|min:1',
            'cab_zone.*'             => 'exists:cities,id',
            'cab_number'             => 'required|string|max:20',
            'cab_type'               => 'required|integer',
            'cab_model'              => 'required|integer',
            'model_id'               => 'required|integer',
            'color_id'               => 'required|integer',
            'no_of_seats'            => 'required|integer',
            'fuel_type'              => 'required|integer',
            // 'base_fare'              => 'required|numeric',
            // 'no_of_kms'              => 'required|numeric',
            // 'additional_km_charges'  => 'required|numeric',
            // 'waiting_charges'        => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'fleet_operator_id.required'      => 'Please select a fleet operator.',
            'fleet_operator_id.exists'        => 'Selected fleet operator is invalid.',
            'cab_zone.required'               => 'Please select at least one cab zone.',
            'cab_zone.*.exists'               => 'Invalid cab zone selected.',
            'cab_number.required'             => 'Please enter a cab number.',
            'cab_number.unique'               => 'This cab number is already taken.',
            'cab_type.required'               => 'Please select a cab type.',
            'cab_type.in'                     => 'Invalid cab type selected.',
            'cab_model.required'              => 'Please select a cab model.',
            'cab_model.in'                    => 'Invalid cab model selected.',
            'model_id.required'               => 'Please select a model name.',
            'model_id.exists'                 => 'Invalid model selected.',
            'color_id.required'               => 'Please select a color.',
            'color_id.exists'                 => 'Invalid color selected.',
            'no_of_seats.required'            => 'Please select the number of seats.',
            'no_of_seats.in'                  => 'Invalid number of seats selected.',
            'fuel_type.required'              => 'Please select a fuel type.',
            'fuel_type.in'                    => 'Invalid fuel type selected.',
            'base_fare.required'              => 'Please select a base fare.',
            'base_fare.in'                    => 'Invalid base fare selected.',
            'no_of_kms.required'              => 'Please select the number of kilometers.',
            'no_of_kms.in'                    => 'Invalid number of kilometers selected.',
            'additional_km_charges.required'  => 'Please select additional km charges.',
            'additional_km_charges.in'        => 'Invalid additional km charge selected.',
            'waiting_charges.required'        => 'Please select waiting charges.',
            'waiting_charges.in'              => 'Invalid waiting charge selected.',
        ];
    }
}
