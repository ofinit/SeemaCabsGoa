@extends('layouts.main')

@section('title', 'Company Details')

@section('css')
    <style>
        .parsley-errors-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
            color: red;
            font-size: 14px;
        }

        .parsley-errors-list li {
            display: inline;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Sas Company Details</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Sas Company Details</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Add Company Details</h5>
                </div>
                <div class="card-body">
                    <form method="post" id="companyDetailsFrom" action="{{ route('admin.setting.companyDetails.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" value="{{ @$companyDetails->id ?? '' }}" name="id"
                                        id="">
                                    <label class="form-label" for="companyName">Company Name</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->name ?? old('name') }}" name="name" id="companyName"
                                        placeholder="Enter company name" required data-parsley-required="true"
                                        data-parsley-length="[3, 255]"
                                        data-parsley-error-message="Company Name is required and should be between 3 to 255 characters.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="contactPerson">Contact Person Name</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->contact_person_name ?? old('contact_person_name') }}"
                                        name="contact_person_name" id="contactPerson" placeholder="Enter person name"
                                        required data-parsley-required="true" data-parsley-length="[3, 255]"
                                        data-parsley-error-message="Contact Person Name is required and should be between 3 to 255 characters.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="companyMobile">Mobile Number</label>
                                    <input type="number" class="form-control"
                                        value="{{ @$companyDetails->phone_number ?? old('phone_number') }}"
                                        name="phone_number" id="companyMobile" placeholder="Enter mobile number" required
                                        data-parsley-required="true" data-parsley-type="digits"
                                        data-parsley-length="[10, 10]"
                                        data-parsley-error-message="Mobile Number is required and should be exactly 10 digits.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="companyMail">Email ID</label>
                                    <input type="email" class="form-control"
                                        value="{{ @$companyDetails->email ?? old('email') }}" name="email"
                                        id="companyMail" placeholder="Enter email id" required data-parsley-required="true"
                                        data-parsley-type="email"
                                        data-parsley-error-message="A valid Email ID is required.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="companyMobile2">Address Line 1</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->address_line_one ?? old('address_line_one') }}"
                                        name="address_line_one" id="companyMobile2" placeholder="Enter address" required
                                        data-parsley-error-message="Address line one is required.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="companyMobile3">Address Line 2</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->address_line_two ?? old('address_line_two') }}"
                                        name="address_line_two" id="companyMobile3" placeholder="Enter address">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="countryName">Country</label>
                                    <select class="form-select" name="country_id" id="countryName"
                                        onchange="getState(this)" required
                                        data-parsley-error-message="Please select a country.">
                                        <option value="">Select country</option>
                                        @if (isset($country) && count($country) > 0)
                                            @foreach ($country as $list)
                                                <option value="{{ $list->id ?? '' }}"
                                                    {{ @$companyDetails->country_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="stateName">State</label>
                                    <select class="form-select" name="state_id" id="state" onchange="getCity(this)"
                                        required data-parsley-required="true"
                                        data-parsley-error-message="Please select a state.">
                                        @if (isset($companyDetails->state_id) && $companyDetails->state_id != null)
                                            @if (isset($states) && count($states) > 0)
                                                @foreach ($states as $list)
                                                    <option value="{{ $list->id ?? '' }}"
                                                        {{ $companyDetails->state_id == $list->id ? 'selected' : '' }}>
                                                        {{ $list->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @else
                                            <option value="">Select State</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="cityName">City</label>
                                    <select class="form-select" name="city_id" id="city" required
                                        data-parsley-required="true" data-parsley-error-message="Please select a city.">
                                        @if (isset($companyDetails->city_id) && $companyDetails->city_id != null)
                                            @if (isset($city) && count($city) > 0)
                                                @foreach ($city as $list)
                                                    <option value="{{ $list->id ?? '' }}"
                                                        {{ $companyDetails->city_id == $list->id ? 'selected' : '' }}>
                                                        {{ $list->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @else
                                            <option value="">Select City</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="zipCode">ZIP Code</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->zip_code ?? old('zip_code') }}" name="zip_code"
                                        id="zipCode" placeholder="Enter ZIP number" required
                                        data-parsley-required="true" data-parsley-pattern="^[0-9]{6}$"
                                        data-parsley-error-message="ZIP Code is required and should be 6 digits.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="panCard">PAN</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->pan_number ?? old('pan_number') }}" name="pan_number"
                                        id="panCard" placeholder="Enter PAN number" required
                                        pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$"
                                        data-parsley-error-message="PAN Number is required."
                                        data-parsley-pattern-message="Please enter a valid PAN number.">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tanCard">TAN</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->tan_number ?? old('tan_number') }}" name="tan_number"
                                        id="tanCard" placeholder="Enter TAN number" required
                                        data-parsley-required="true" data-parsley-error-message="TAN Number is required."
                                        pattern="^[A-Z]{4}[0-9]{5}[A-Z]{1}$"
                                        data-parsley-pattern-message="Please enter a valid TAN number.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="gstNumber">GST</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->gst_number ?? old('gst_number') }}" name="gst_number"
                                        id="gstNumber" placeholder="Enter GST number" required
                                        data-parsley-required="true"
                                        data-parsley-error-message="GST Number is required and please follow Example: 12ABCDE1234F1Z5."
                                        data-parsley-pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}[Z]{1}[A-Z0-9]{1}$"
                                        data-parsley-pattern-message="Please follow the GST Number pattern. Example: 12ABCDE1234F1Z5">
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="my-3">
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="bank1Name">Account Name</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->account_name ?? old('account_name') }}"
                                        name="account_name" id="bank1Name" placeholder="Enter bank name" required
                                        data-parsley-required="true"
                                        data-parsley-error-message="Account Name is required.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="bank1Name">Account Type</label>
                                    <select class="form-control" name="account_type" id="" required
                                        data-parsley-required="true"
                                        data-parsley-error-message="Please select an Account Type.">
                                        <option value="" hidden>Select Account Type</option>
                                        @php
                                            $accountTypeList = getAccountTypeList();
                                        @endphp
                                        @foreach ($accountTypeList as $list)
                                            <option value="{{ $list }}"
                                                {{ @$companyDetails ? (@$companyDetails->account_type == $list ? 'selected' : '') : '' }}>
                                                {{ $list }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="branch1Name">Branch Name</label>
                                    <input type="text" class="form-control"
                                        value="{{ @$companyDetails->branch_name ?? old('branch_name') }}"
                                        name="branch_name" id="branch1Name" placeholder="Enter branch name" required
                                        data-parsley-required="true"
                                        data-parsley-error-message="Branch Name is required.">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="account1Number">Account Number</label>
                                    <input type="number" class="form-control" id="account1Number" name="account_number"
                                        placeholder="Enter account number"
                                        value="{{ @$companyDetails->account_number ?? old('account_number') }}" required
                                        pattern="^\d{9,18}$" minlength="9" maxlength="18"
                                        data-parsley-required-message="Please enter account number."
                                        data-parsley-pattern-message="Invalid account number."
                                        data-parsley-minlength-message="Minimum 9 digits required."
                                        data-parsley-maxlength-message="Maximum 18 digits allowed.">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ifsc1Code">IFSC Code</label>
                                    <input type="text" class="form-control" id="ifsc1Code" name="ifsc_code"
                                        placeholder="Enter IFSC code"
                                        value="{{ @$companyDetails->ifsc_code ?? old('ifsc_code') }}" required
                                        pattern="^[A-Z]{4}0[A-Z0-9]{6}$" minlength="11" maxlength="11"
                                        data-parsley-required-message="Please enter IFSC code."
                                        data-parsley-pattern-message="Invalid IFSC code format."
                                        data-parsley-minlength-message="Must be exactly 11 characters."
                                        data-parsley-maxlength-message="Must be exactly 11 characters.">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="upi1Number">UPI ID</label>
                                    <input type="text" class="form-control" id="upi1Number" name="upi_id"
                                        placeholder="Enter UPI ID"
                                        value="{{ @$companyDetails->upi_id ?? old('upi_id') }}" required minlength="6"
                                        maxlength="50" pattern="^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$"
                                        data-parsley-required-message="Please enter UPI ID."
                                        data-parsley-minlength-message="UPI ID must be at least 6 characters."
                                        data-parsley-maxlength-message="UPI ID cannot exceed 50 characters."
                                        data-parsley-pattern-message="Invalid UPI ID format.">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <div class="my-3">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#companyDetailsFrom').parsley();
        });

        function getState(e) {
            var countryId = e.value;
            if (countryId) {
                var url = '{{ route('admin.getStates', ':id') }}'.replace(':id', countryId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#state').empty().append('<option value="">Select State</option>');

                        $.each(data, function(key, state) {
                            $('#state').append('<option value="' + state.id + '">' + state.name +
                                '</option>');
                        });
                    },
                });
            } else {
                $('#state').empty().append('<option value="">Select State</option>');
            }
        }

        function getCity(e) {
            var stateId = e.value;
            if (stateId) {
                var url = '{{ route('admin.getCity', ':id') }}'.replace(':id', stateId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#city').empty().append('<option value="">Select City</option>');

                        $.each(data, function(key, city) {
                            $('#city').append('<option value="' + city.id + '">' + city.name +
                                '</option>');
                        });
                    },
                });
            } else {
                $('#city').empty().append('<option value="">Select City</option>');
            }
        }
    </script>
@endsection
