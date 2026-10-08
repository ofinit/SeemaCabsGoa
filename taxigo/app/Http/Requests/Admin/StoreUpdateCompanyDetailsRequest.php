<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateCompanyDetailsRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'contact_person_name' => 'required|string|max:255',
            'phone_number' => 'required|digits:10',
            'email' => 'required|email|unique:users,email',
            'zip_code' => 'required|regex:/^\d{6}$/',
            'gst_number' => 'required|string|max:15',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            // 'pan_number' => 'required|string|max:10',
            // 'tan_number' => 'required|string|max:10',
            // 'account_name' => 'required|string|max:255',
            // 'account_type' => 'required|in:Fixed,Saving,Salary',
            // 'branch_name' => 'required|string|max:255',
            // 'account_number' => 'required|numeric',
            // 'ifsc_code' => 'required|string|max:11',
            // 'upi_id' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => 'Please enter the company name.',
            'person_name.required' => 'Please enter the contact person name.',
            'mobile_number.required' => 'Please enter the mobile number.',
            'mobile_number.digits' => 'The mobile number must be exactly 10 digits.',
            'email_id.required' => 'Please enter the email address.',
            'email_id.email' => 'Please enter a valid email address.',
            'zip_code.required' => 'Please enter the ZIP code.',
            'zip_code.regex' => 'The ZIP code must be exactly 6 digits.',
            'gst_number.required' => 'Please enter the GST number.',
            'country_id.required' => 'Please select a country.',
            'state_id.required' => 'Please select a state.',
            'city_id.required' => 'Please select a city.',
            'pan_number.required' => 'Please enter the PAN number.',
            'account_name.required' => 'Please enter the account name.',
            'account_type.required' => 'Please select the account type.',
            'branch_name.required' => 'Please enter the branch name.',
            'account_number.required' => 'Please enter the account number.',
            'ifsc_code.required' => 'Please enter the IFSC code.',
            'upi_id.max' => 'The UPI ID is too long.',
        ];
    }
}
