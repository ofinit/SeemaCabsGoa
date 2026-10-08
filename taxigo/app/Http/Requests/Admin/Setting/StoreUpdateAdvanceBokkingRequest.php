<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateAdvanceBokkingRequest extends FormRequest
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
            'title.*' => 'required|string',
            'value.*' => 'required|numeric|min:0'
        ];
    }

    public function messages()
    {
        return [
            'title.*.required' => 'The title field is required.',
            'title.*.string' => 'The title must be a valid string.',
            'value.*.required' => 'The details is required.',
            'value.*.numeric' => 'The details must be a number.',
            'value.*.min' => 'The details must be at least 0.',
            'value.*.max' => 'The details cannot exceed 1000000.',
        ];
    }
}
