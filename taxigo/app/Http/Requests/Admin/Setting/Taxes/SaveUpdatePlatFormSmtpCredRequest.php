<?php

namespace App\Http\Requests\Admin\Setting\Taxes;

use Illuminate\Foundation\Http\FormRequest;

class SaveUpdatePlatFormSmtpCredRequest extends FormRequest
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
            'value.0' => 'required|string', // Host
            'value.1' => 'required|integer|min:1|max:65535', // Port
            'value.2' => 'required|string', // Username
            'value.3' => 'required|string', // Password
            'value.4' => 'required|string' // Authentication
        ];
    }

    public function messages()
    {
        return [
            'title.*.required' => 'The title field is required.',
            'title.*.string' => 'The title must be a valid string.',
            'value.0.required' => 'SMTP host is required.',
            'value.0.string' => 'SMTP host must be a valid string.',
            'value.1.required' => 'SMTP port is required.',
            'value.1.integer' => 'SMTP port must be a valid number.',
            'value.1.min' => 'Port must be at least 1.',
            'value.1.max' => 'Port cannot exceed 65535.',
            'value.2.required' => 'SMTP username is required.',
            'value.2.string' => 'SMTP username must be a valid string.',
            'value.3.required' => 'SMTP password is required.',
            'value.3.string' => 'SMTP password must be a valid string.',
            'value.4.required' => 'Authentication method is required.',
            'value.4.in' => 'Invalid authentication method selected.',
        ];
    }
}
