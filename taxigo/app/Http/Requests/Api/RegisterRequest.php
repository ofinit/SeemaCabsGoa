<?php

namespace App\Http\Requests\Api;

use App\Enums\Type;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->whereNull('deleted_at'),
            ],

            'gender' => [
                'nullable',
                'in:0,1,2'
            ],
            'country_id' => [
                Rule::requiredIf($this->social_login !== Type::GOOGLE),
                'integer'
            ],
            'state_id' => [
                Rule::requiredIf($this->social_login !== Type::GOOGLE),
                'integer'
            ],
            'phone_number' => [
                Rule::requiredIf($this->social_login !== Type::GOOGLE),
                'digits:10'
            ],

            'password' => [
                Rule::requiredIf($this->social_login !== Type::GOOGLE),
                'string',
                'min:8'
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'full name',
            'phone_number' => 'phone number',
            'email' => 'email address',
            'gender' => 'gender',
            'password' => 'password',
            'country_id' => 'country',
            'state_id' => 'state',
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The full name is required.',
            'name.string' => 'The full name must be a valid string.',
            'name.max' => 'The full name should not exceed 255 characters.',

            'gender.required' => 'The gender field is required.',
            'gender.in' => 'The gender must be either male (1), female (0), or other (2).',

            'country_id.required' => 'The country field is required.',
            'country_id.integer' => 'The country must be a valid integer.',

            'state_id.required' => 'The state field is required.',
            'state_id.integer' => 'The state must be a valid integer.',

            'phone_number.required' => 'The phone number is required.',
            'phone_number.digits' => 'The phone number must be exactly 10 digits.',
            'phone_number.unique' => 'The phone number has already been taken.',

            'email.required' => 'The email address is required.',
            'email.email' => 'The email address must be a valid email format.',
            'email.unique' => 'The email address has already been taken.',

            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters long.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $firstError = $errors[0];

        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => $firstError,
            ], 422)
        );
    }
}
