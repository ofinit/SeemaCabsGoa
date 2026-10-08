<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class BaseFareStoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'cab_type' => 'required',
            'base_fare' => 'required|integer|max:999999',
            'no_of_kms' => 'required|integer|max:999',
            'additional_km_charges' => 'required|integer|max:999999',
            'waiting_charges' => 'required|integer|max:999999',
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'cab_type.required' => 'Please select cab type.',
            'cab_type.unique' => 'The cab type has already been taken.',
            'base_fare.required' => 'Please enter base fare.',
            'base_fare.numeric' => 'Base fare must be a valid number.',
            'base_fare.max' => 'Base fare must not exceed 999,999.',
            'no_of_kms.required' => 'Please enter the number of kms included.',
            'no_of_kms.numeric' => 'Number of kms must be a valid number.',
            'no_of_kms.max' => 'Number of kms must not exceed 999.',
            'additional_km_charges.required' => 'Please enter additional km charges.',
            'additional_km_charges.numeric' => 'Additional km charges must be a valid number.',
            'additional_km_charges.max' => 'Additional km charges must not exceed 999,999.',
            'waiting_charges.required' => 'Please enter waiting charges.',
            'waiting_charges.numeric' => 'Waiting charges must be a valid number.',
            'waiting_charges.max' => 'Waiting charges must not exceed 999,999.',
        ];
    }
}
