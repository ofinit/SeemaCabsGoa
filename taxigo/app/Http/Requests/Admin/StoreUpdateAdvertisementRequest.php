<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateAdvertisementRequest extends FormRequest
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
            'customer_id' => 'required|exists:users,id',
            // 'screens' => 'required|array|min:1',
            // 'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_url' => 'required|url',
            // 'gender' => 'required|in:0,1,2,3', // Valid gender values
            // 'country_id' => 'required|exists:countries,id', // Assuming countries table
            // 'state_id' => 'required|exists:states,id', // Assuming states table
            // 'location' => 'required|exists:locations,id', // Assuming locations table
            // 'start_date' => 'required|date',
            // 'start_time' => 'required|date_format:H:i',
            // 'end_date' => 'required|date|after_or_equal:start_date',
            // 'end_time' => 'required|date_format:H:i|after:start_time',
            // 'payment_method' => 'required|in:cash,online',
            // 'billing_company_name' => 'nullable|string|max:255',
            // 'billing_gst' => 'nullable|string|max:15',
            // 'billing_pan' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get the custom error messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'screens.required' => 'Please select at least one screen.',
            'screens.array' => 'Please select at least one screen.',
            'screens.min' => 'Please select at least one screen.',
            'banner_image.required' => 'Please choose a banner image.',
            'banner_image.image' => 'The file must be an image.',
            'banner_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'banner_image.max' => 'The image size should not exceed 2MB.',
            'banner_url.required' => 'Please enter a valid URL.',
            'banner_url.url' => 'Please enter a valid URL.',
            'gender.required' => 'Please select gender.',
            'gender.in' => 'Invalid gender selected.',
            'country_id.required' => 'Please select a country.',
            'country_id.exists' => 'Selected country does not exist.',
            'state_id.required' => 'Please select a state.',
            'state_id.exists' => 'Selected state does not exist.',
            'location.required' => 'Please select a live location.',
            'location.exists' => 'Selected live location does not exist.',
            'start_date.required' => 'Please select a start date.',
            'start_date.date' => 'Please select a valid date for start date.',
            'start_time.required' => 'Please select a start time.',
            'start_time.date_format' => 'Start time must be in the format HH:mm.',
            'end_date.required' => 'Please select an end date.',
            'end_date.date' => 'Please select a valid date for end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'end_time.required' => 'Please select an end time.',
            'end_time.date_format' => 'End time must be in the format HH:mm.',
            'end_time.after' => 'End time must be after start time.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'billing_company_name.max' => 'Company name should not exceed 255 characters.',
            'billing_gst.max' => 'GST number should not exceed 15 characters.',
            'billing_pan.max' => 'PAN number should not exceed 10 characters.',
        ];
    }
}
