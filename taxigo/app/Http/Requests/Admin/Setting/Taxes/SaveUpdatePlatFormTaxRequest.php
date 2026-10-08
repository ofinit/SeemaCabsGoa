<?php

namespace App\Http\Requests\Admin\Setting\Taxes;

use Illuminate\Foundation\Http\FormRequest;

class SaveUpdatePlatFormTaxRequest extends FormRequest
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
            'value.*' => 'required|numeric|min:0|max:100'
        ];
    }

    public function messages()
    {
        return [
            'title.*.required' => 'The title field is required.',
            'title.*.string' => 'The title must be a valid string.',
            'value.*.required' => 'The tax percentage is required.',
            'value.*.numeric' => 'The tax percentage must be a number.',
            'value.*.min' => 'The tax percentage must be at least 0%.',
            'value.*.max' => 'The tax percentage cannot exceed 100%.',
        ];
    }
}
