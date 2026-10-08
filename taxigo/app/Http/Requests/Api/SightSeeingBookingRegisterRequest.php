<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SightSeeingBookingRegisterRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:users,id',
            'sight_seeing_package_id' => 'required|integer|exists:sight_seeing_packages,id',
            'cab_type' => 'required|integer',
            'pickup_address' => 'nullable|string|max:255',
            'pickup_from' => 'required|integer',
            'pickup_date' => 'required|date_format:d-m-Y',
            'pickup_time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'customer_id.required' => 'The customer ID is required.',
            'customer_id.integer' => 'The customer ID must be a valid integer.',
            'customer_id.exists' => 'The customer not exists.',

            'sight_seeing_package_id.required' => 'The sight seeing package ID is required.',
            'sight_seeing_package_id.integer' => 'The sight seeing package ID must be a valid integer.',
            'sight_seeing_package_id.exists' => 'The sight seeing package not exists.',

            'cab_type.required' => 'The cab type is required.',
            'cab_type.integer' => 'The cab type must be a valid integer.',

            'pickup_address.required' => 'The pickup address is required.',
            'pickup_address.string' => 'The pickup address must be a string.',
            'pickup_address.max' => 'The pickup address must not exceed 255 characters.',

            'pickup_from.required' => 'The pickup from field is required.',
            'pickup_from.string' => 'The pickup from field must be a string.',
            'pickup_from.max' => 'The pickup from field must not exceed 255 characters.',

            'pickup_date.required' => 'The pickup date is required.',
            'pickup_date.date_format' => 'The pickup date must be in the format d-m-Y.',

            'pickup_time.required' => 'The pickup time is required.',
            'pickup_time.date_format' => 'The pickup time must be in the format H:i.',

            'price.required' => 'The base fare is required.',
            'price.numeric' => 'The base fare must be a valid number.',
            'price.min' => 'The base fare must be at least 0.',

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $firstError = $errors[0];

        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => $firstError,
            ], 422)
        );
    }
}
