<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SightSeeingAddEditRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'terms_and_condition' => 'nullable|string',
            'city.*' => 'required',
            'hatch_back_price.*' => 'required|numeric',
            'sedan_price.*' => 'required|numeric',
            'suv_price.*' => 'required|numeric',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if 'city' key is missing or empty
            if (!$this->has('city') || empty($this->city)) {
                $validator->errors()->add('city', 'Please add price details.');
            }
        });
    }
}
