<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBookingRequest extends FormRequest
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
            'cab_id' => 'nullable|integer|exists:cab_rates,id',
            'customer_id' => 'required|integer|exists:users,id',
            'trip_type' => 'required|string',
            'cab_model' => 'required|integer',
            'cab_type' => 'required|integer',
            'pickup_address' => 'nullable|string|max:255',
            'drop_of_address' => 'nullable|string|max:255',
            'air_port_drop' => 'nullable',
            'pickup_from' => 'required|integer',
            'drop_to' => 'required|integer',
            'pickup_date' => 'required|date_format:d-m-Y',
            'pickup_time' => 'required|date_format:H:i',
            'base_fare' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'total_payment' => 'required|numeric|min:0',
            'part_payment' => 'required|numeric|min:0',
            'remain_payment' => 'required|numeric|min:0',
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

            'trip_type.required' => 'The trip type is required.',
            'trip_type.string' => 'The trip type must be a string.',

            'cab_model.required' => 'The cab model is required.',
            'cab_model.integer' => 'The cab model must be a valid integer.',

            'cab_type.required' => 'The cab type is required.',
            'cab_type.integer' => 'The cab type must be a valid integer.',
            'cab_type.in' => 'The cab type must be either 1 or 2.',

            'pickup_address.required' => 'The pickup address is required.',
            'pickup_address.string' => 'The pickup address must be a string.',
            'pickup_address.max' => 'The pickup address must not exceed 255 characters.',

            'drop_of_address.required' => 'The drop-off address is required.',
            'drop_of_address.string' => 'The drop-off address must be a string.',
            'drop_of_address.max' => 'The drop-off address must not exceed 255 characters.',

            'pickup_from.required' => 'The pickup from field is required.',
            'pickup_from.string' => 'The pickup from field must be a string.',
            'pickup_from.max' => 'The pickup from field must not exceed 255 characters.',

            'drop_to.required' => 'The drop-to field is required.',
            'drop_to.string' => 'The drop-to field must be a string.',
            'drop_to.max' => 'The drop-to field must not exceed 255 characters.',

            'pickup_date.required' => 'The pickup date is required.',
            'pickup_date.date_format' => 'The pickup date must be in the format d-m-Y.',

            'pickup_time.required' => 'The pickup time is required.',
            'pickup_time.date_format' => 'The pickup time must be in the format H:i.',

            'base_fare.required' => 'The base fare is required.',
            'base_fare.numeric' => 'The base fare must be a valid number.',
            'base_fare.min' => 'The base fare must be at least 0.',

            'tax_amount.required' => 'The tax amount is required.',
            'tax_amount.numeric' => 'The tax amount must be a valid number.',
            'tax_amount.min' => 'The tax amount must be at least 0.',

            'total_payment.required' => 'The total amount is required.',
            'total_payment.numeric' => 'The total amount must be a valid number.',
            'total_payment.min' => 'The total amount must be at least 0.',
            'total_payment.gte' => 'The total amount must be greater than or equal to the sum of the base fare and tax amount.',

            'part_payment.required' => 'The part payment is required.',
            'part_payment.numeric' => 'The part payment must be a valid number.',
            'part_payment.min' => 'The part payment must be at least 0.',
            'part_payment.lte' => 'The part payment cannot be greater than the total amount.',

            'remain_payment.required' => 'The remain amount is required.',
            'remain_payment.numeric' => 'The remain amount must be a valid number.',
            'remain_payment.min' => 'The remain amount must be at least 0.',
            'remain_payment.lte' => 'The remain amount cannot be greater than the total amount.',
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
