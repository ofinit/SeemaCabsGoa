<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCabRateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'edit_id' => ['array'],
            'edit_id.*' => ['nullable', 'integer'],

            'edit_from' => ['nullable', 'array'],
            'edit_from.*' => ['nullable', 'integer'],

            'edit_to' => ['nullable', 'array'],
            'edit_to.*' => ['nullable', 'integer'],

            'edit_cab_id' => ['nullable', 'array'],
            'edit_cab_id.*' => ['nullable', 'integer'],

            'edit_base_fare' => ['nullable', 'array'],
            'edit_base_fare.*' => ['nullable', 'numeric', 'digits_between:1,8'],

            'from' => ['nullable', 'array'],
            'from.*' => ['nullable', 'integer'],

            'to' => ['nullable', 'array'],
            'to.*' => ['nullable', 'integer'],

            'cab_id' => ['nullable', 'array'],
            'cab_id.*' => ['nullable', 'integer'],

            'base_fare' => ['nullable', 'array'],
            'base_fare.*' => ['nullable', 'numeric', 'digits_between:1,8'],
        ];
    }

    public function messages()
    {
        return [
            'edit_from.*.required' => 'Please select airport.',
            'edit_from.*.exists' => 'Selected airport does not exist.',

            'edit_to.*.required' => 'Please select city.',
            'edit_to.*.exists' => 'Selected city does not exist.',

            'edit_cab_id.*.required' => 'Please select cab type.',

            'edit_base_fare.*.required' => 'Please enter base fare price.',
            'edit_base_fare.*.numeric' => 'Base fare must be a valid number.',
            'edit_base_fare.*.min' => 'Base fare must be at least 0.',
            'edit_base_fare.*.digits_between' => 'Each base fare value must be between 1 and 8 digits.',

            'from.*.required' => 'Please select airport.',
            'from.*.exists' => 'Selected airport does not exist.',

            'to.*.required' => 'Please select city.',
            'to.*.exists' => 'Selected city does not exist.',

            'cab_id.*.required' => 'Please select cab type.',

            'base_fare.*.required' => 'Please enter base fare price.',
            'base_fare.*.numeric' => 'Base fare must be a valid number.',
            'base_fare.*.min' => 'Base fare must be at least 0.',
            'base_fare.*.digits_between' => 'Each base fare value must be between 1 and 8 digits.',
        ];
    }

    // Optional: Custom JSON validation error response
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $firstError = $errors[0];
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $firstError,
            ])
        );
    }
}