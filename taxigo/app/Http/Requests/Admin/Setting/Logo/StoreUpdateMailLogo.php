<?php

namespace App\Http\Requests\Admin\Setting\Logo;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateMailLogo extends FormRequest
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
            'mailLogo' => [
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
            'mailLogo.required' => 'Please upload a mail logo.',
            'mailLogo.mimes'    => 'Only image files (jpeg, png, jpg, gif) are allowed.',
            'mailLogo.max'      => 'The file size must not exceed 2MB.',
        ];
    }
}
