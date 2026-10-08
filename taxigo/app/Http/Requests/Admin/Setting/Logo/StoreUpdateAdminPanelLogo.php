<?php

namespace App\Http\Requests\Admin\Setting\Logo;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateAdminPanelLogo extends FormRequest
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
            'adminPanelLogo' => [
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
            'adminPanelLogo.required' => 'Please upload a admin panel logo.',
            'adminPanelLogo.mimes'    => 'Only image files (jpeg, png, jpg, gif) are allowed.',
            'adminPanelLogo.max'      => 'The file size must not exceed 2MB.',
        ];
    }
}
