<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => 'Please enter your current password.',
            'new_password.required' => 'Please choose a new password.',
            'new_password.min' => 'Password must be at least 8 characters.',
            'new_password.confirmed' => 'The new password and confirmation password do not match.',
            'confirm_password.required' => 'Please confirm your new password.',
            'confirm_password.same' => 'Passwords must match.',
        ];
    }
}
