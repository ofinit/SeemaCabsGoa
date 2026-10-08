<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:100',
            'type' => 'required|in:1,2',
            'value' => 'required|numeric|min:1',
            'expiry_date' => 'nullable|date',
            'description' => 'required|string|max:100',
            // 'status' => 'required|in:enable,disable',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Discount code is required.',
            'code.max' => 'Discount code cannot exceed 100 characters.',

            'type.required' => 'Discount type is required.',
            'type.in' => 'Invalid discount type selected.',

            'value.required' => 'Discount value is required.',
            'value.numeric' => 'Discount value must be a number.',
            'value.min' => 'Discount value must be at least 1.',

            'expiry_date.date' => 'Expiry date must be a valid date.',

            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot exceed 100 characters.',

            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selection.',
        ];
    }
}
