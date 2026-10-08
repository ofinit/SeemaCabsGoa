<?php

namespace App\Http\Requests\Admin\Setting\Logo;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateInvoiceLogo extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules(): array
    {
        return [
            'invoiceLogo' => [
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
            'invoiceLogo.required' => 'Please upload a invoice logo.',
            'invoiceLogo.mimes'    => 'Only image files (jpeg, png, jpg, gif) are allowed.',
            'invoiceLogo.max'      => 'The file size must not exceed 2MB.',
        ];
    }
}
