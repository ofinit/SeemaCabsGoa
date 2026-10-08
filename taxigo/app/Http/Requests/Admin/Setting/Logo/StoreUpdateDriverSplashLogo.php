<?php

namespace App\Http\Requests\Admin\Setting\Logo;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateDriverSplashLogo extends FormRequest
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
            'driverSplashLogo' => [
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
            'driverSplashLogo.required' => 'Please upload a driver splash logo.',
            'driverSplashLogo.mimes'    => 'Only image files (jpeg, png, jpg, gif) are allowed.',
            'driverSplashLogo.max'      => 'The file size must not exceed 2MB.',
        ];
    }
}
