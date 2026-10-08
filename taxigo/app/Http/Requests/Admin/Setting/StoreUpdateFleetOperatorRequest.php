<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateFleetOperatorRequest extends FormRequest
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
            'company_name' => 'required|string|max:255',
            'person_name' => 'required|string|max:255',
            'mobile_number' => 'required|digits:10',
            'email_id' => 'required|email|unique:fleet_operators,email_id',
            // 'mobile_number_two' => 'nullable|digits:10',
            // 'mobile_number_three' => 'nullable|digits:10',
            'country_id' => 'required|string',
            'state_id' => 'required|string',
            'city_id' => 'required|string',
            'zip_code' => 'required|digits:6',
            'pan_number' => 'required|regex:/[A-Z]{5}[0-9]{4}[A-Z]{1}/',
            'gst_number' => 'required|regex:/\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}/',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'holder_name' => 'required|string|max:255',
            'account_number' => 'required|numeric|digits_between:9,18',
            'ifsc_code' => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            'upi_id' => 'nullable|regex:/^[\w.-]+@[\w.-]+$/',
            'aadhar_number' => 'required|digits:12',
            'front_side_aadhar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'back_side_aadhar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'company_license' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'aggrement' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            // 'password' => 'required|min:8',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => 'Company Name is required.',
            'person_name.required' => 'Contact Person Name is required.',
            'mobile_number.required' => 'Mobile Number is required.',
            'mobile_number.digits' => 'Mobile Number must be 10 digits.',
            'email_id.required' => 'Email ID is required.',
            'email_id.email' => 'Enter a valid Email ID.',
            'email_id.unique' => 'This Email ID is already taken.',
            'zip_code.required' => 'ZIP Code is required.',
            'zip_code.digits' => 'ZIP Code must be 6 digits.',
            'pan_number.regex' => 'Enter a valid PAN number.',
            'gst_number.regex' => 'Enter a valid GST number.',
            'bank_name.required' => 'Bank Name is required.',
            'account_number.required' => 'Account Number is required.',
            'account_number.digits_between' => 'Account Number must be between 9-18 digits.',
            'ifsc_code.regex' => 'Enter a valid IFSC code.',
            'aadhar_number.required' => 'Aadhar Number is required.',
            'aadhar_number.digits' => 'Aadhar Number must be 12 digits.',
            // 'password.required' => 'Password is required.',
            // 'password.min' => 'Password must be at least 8 characters.',
            // 'password.confirmed' => 'Passwords do not match.',
            'front_side_aadhar.required' => 'Frontside of Aadhar Card is required.',
            'front_side_aadhar.image' => 'Frontside of Aadhar must be an image (JPG, PNG, JPEG).',
            'company_license.required' => 'Company License is required.',
            'aggrement.required' => 'Agreement is required.',
        ];
    }
}
