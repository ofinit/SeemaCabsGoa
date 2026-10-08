<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class PlateFormFeeRequest extends FormRequest
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
            'plateFormFee' => [
                'required',
                'max:200',
            ],
            'title' => 'required',
        ];
    }
    
    public function messages(): array
    {
        return [
            'plateFormFee.required' => 'The platform fee is required.',
            'plateFormFee.max' => 'The platform fee cannot be greater than 200.',
            'title.required' => 'The title is required.',
        ];
    }    
}
