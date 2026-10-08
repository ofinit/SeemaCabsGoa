<?php

namespace App\Http\Requests\Admin\Setting\Logo;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateAppLogo extends FormRequest
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
            'appLogo' => [
                'required',               
                'mimes:jpeg,png,jpg,gif',
                'max:2048',
            ],
            'title'=>'required'
        ];
    }

    public function messages(): array
    {
        return [
            'appLogo.required' => 'Please upload a app logo.',
            'appLogo.mimes'    => 'Only image files (jpeg, png, jpg, gif) are allowed.',
            'appLogo.max'      => 'The file size must not exceed 2MB.',
        ];
    }
}
