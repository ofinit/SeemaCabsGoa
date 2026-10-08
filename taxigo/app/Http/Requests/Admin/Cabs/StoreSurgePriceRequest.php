<?php

namespace App\Http\Requests\Admin\Cabs;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurgePriceRequest extends FormRequest
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
            'surge_enable' => 'nullable',
            // 'surge_price' => 'required|numeric|min:0',
            'surge_start_date.*' => 'required|date',
            'surge_end_date.*' => 'required|date',
            'surge_end_time.*' => 'required|date_format:H:i',
            'surge_start_time.*' => 'required|date_format:H:i',
            'surge_percentage.*' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'surge_enable.boolean' => 'The surge enable field must be true or false.',
            // 'surge_price.required' => 'The surge price field is required.',
            // 'surge_price.numeric' => 'The surge price must be a numeric value.',
            // 'surge_price.min' => 'The surge price must be at least 0.',
            'surge_start_date.required' => 'The surge start date field is required.',
            'surge_start_date.date' => 'The surge start date must be a valid date.',
            'surge_end_date.required' => 'The surge end date field is required.',
            'surge_end_date.date' => 'The surge end date must be a valid date.',
            'surge_end_date.after_or_equal' => 'The surge end date must be the same or after the start date.',
            'surge_end_time.required' => 'The surge end time field is required.',
            'surge_end_time.date_format' => 'The surge end time must be in the format HH:MM.',
            'surge_start_time.required' => 'The surge start time field is required.',
            'surge_start_time.date_format' => 'The surge start time must be in the format HH:MM.',
            'surge_percentage.required' => 'The surge percentage field is required',
        ];
    }

}
