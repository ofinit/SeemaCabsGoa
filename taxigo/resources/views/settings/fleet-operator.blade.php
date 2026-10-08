@extends('layouts.main')

@section('title', 'Fleet Operator')

@section('css')
    <style>
        .imagePreview {
            height: 100px;
            margin-top: 10px;
            border: none;
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
                        <li class="breadcrumb-item" aria-current="page">Fleet Operator</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Fleet Operator</h2>
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
                    <h5>Add Fleet Operator</h5>
                </div>
                <div class="card-body">
                    @if (isset($flletOperator) && $flletOperator != null)
                        <form method="post" id="fleetOperatorUpdateForm"
                            action="{{ route('admin.setting.fleetOperators.update') }}" enctype="multipart/form-data">
                        @else
                            <form method="post" id="fleetOperatorForm"
                                action="{{ route('admin.setting.fleetOperators.store') }}" enctype="multipart/form-data">
                    @endif
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" value="{{ $flletOperator->id ?? '' }}" name="id" id="">
                                <label class="form-label" for="companyName">Company Name</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->company_name ?? old('company_name') }}" name="company_name"
                                    id="companyName" placeholder="Enter company name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="contactPerson">Contact Person Name</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->person_name ?? old('person_name') }}" name="person_name"
                                    id="contactPerson" placeholder="Enter person name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="companyMobile">Mobile Number</label>
                                <input type="number" class="form-control"
                                    value="{{ @$flletOperator->mobile_number ?? old('mobile_number') }}"
                                    name="mobile_number" id="companyMobile" placeholder="Enter mobile number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="companyMail">Email ID</label>
                                <input type="email" class="form-control"
                                    value="{{ @$flletOperator->email_id ?? old('email_id') }}" name="email_id"
                                    id="companyMail" placeholder="Enter email id">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="companyMobile2">Mobile Number-2</label>
                                <input type="number" class="form-control"
                                    value="{{ @$flletOperator->mobile_number_two ?? old('mobile_number_two') }}"
                                    name="mobile_number_two" id="companyMobile2" placeholder="Enter mobile number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="companyMobile3">Mobile Number-3</label>
                                <input type="number" class="form-control"
                                    value="{{ @$flletOperator->mobile_number_three ?? old('mobile_number_three') }}"
                                    name="mobile_number_three" id="companyMobile3" placeholder="Enter mobile number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="countryName">Country</label>
                                <select class="form-select" name="country_id" id="countryName" onchange="getState(this)">
                                    <option value="">Select country</option>
                                    @if (isset($country) && count($country) > 0)
                                        @foreach ($country as $list)
                                            <option value="{{ $list->id ?? '' }}"
                                                {{ @$flletOperator->country_id == $list->id ? 'selected' : '' }}>
                                                {{ $list->name ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="stateName">State</label>
                                <select class="form-select" name="state_id" id="state" onchange="getCity(this)">
                                    @if (isset($flletOperator->state_id) && $flletOperator->state_id != null)
                                        @if (isset($states) && count($states) > 0)
                                            @foreach ($states as $list)
                                                <option value="{{ $list->id ?? '' }}"
                                                    {{ $flletOperator->state_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name ?? '' }}</option>
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
                                <select class="form-select" name="city_id" id="city">
                                    @if (isset($flletOperator->city_id) && $flletOperator->city_id != null)
                                        @if (isset($city) && count($city) > 0)
                                            @foreach ($city as $list)
                                                <option value="{{ $list->id ?? '' }}"
                                                    {{ $flletOperator->city_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name ?? '' }}</option>
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
                                    value="{{ @$flletOperator->zip_code ?? old('zip_code') }}" name="zip_code"
                                    id="zipCode" placeholder="Enter ZIP number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="panCard">PAN</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->pan_number ?? old('pan_number') }}" name="pan_number"
                                    id="panCard" placeholder="Enter PAN number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="gstNumber">GST</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->gst_number ?? old('gst_number') }}" name="gst_number"
                                    id="gstNumber" placeholder="Enter GST number">
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="my-3">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bank1Name">Bank Name</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->getBankDetails[0]['bank_name'] ?? old('bank_name') }}"
                                    name="bank_name" id="bank1Name" placeholder="Enter bank name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="branch1Name">Branch Name</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->getBankDetails[0]['branch_name'] ?? old('branch_name') }}"
                                    name="branch_name" id="branch1Name" placeholder="Enter branch name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="account1Name">Account Holder Name</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->getBankDetails[0]['holder_name'] ?? old('holder_name') }}"
                                    name="holder_name" id="account1Name" placeholder="Enter account holder name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="account1Number">Account Number</label>
                                <input type="number" class="form-control"
                                    value="{{ @$flletOperator->getBankDetails[0]['account_number'] ?? old('account_number') }}"
                                    name="account_number" id="account1Number" placeholder="Enter account number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="ifsc1Code">IFSC Code</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->getBankDetails[0]['ifsc_code'] ?? old('ifsc_code') }}"
                                    name="ifsc_code" id="ifsc1Code" placeholder="Enter IFSC code">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="upi1Number">UPI ID</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->getBankDetails[0]['upi_id'] ?? old('upi_id') }}"
                                    name="upi_id" id="upi1Number" placeholder="Enter upi id">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="razorpayAccount">Razorpay Route Account</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->razorpay_account ?? old('razorpay_account') }}"
                                    name="razorpay_account" id="razorpayAccount" placeholder="Ex: acc_vendor123">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="cashfreeVendorId">Cashfree Easy Split Vendor ID</label>
                                <input type="text" class="form-control"
                                    value="{{ @$flletOperator->cashfree_vendor_id ?? old('cashfree_vendor_id') }}"
                                    name="cashfree_vendor_id" id="cashfreeVendorId" placeholder="Ex: vendor_123">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="aadhar1Number">Aadhar Card Number</label>
                                <input type="number" class="form-control"
                                    value="{{ @$flletOperator->aadhar_number ?? old('aadhar_number') }}"
                                    name="aadhar_number" id="aadhar1Number" placeholder="Enter aadhar card number">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="aadhar-1-front">Frontside of Aadhar Card</label>
                                <input type="file" class="form-control"
                                    value="{{ @$flletOperator->front_side_aadhar ?? old('front_side_aadhar') }}"
                                    name="front_side_aadhar" id="front_side_aadhar" accept="image/*">
                                <img class="" id="imagePreviewFrontSide" />
                                @if (isset($flletOperator->front_side_aadhar) && $flletOperator->front_side_aadhar != null)
                                    <img class="imagePreview"
                                        src="{{ asset('storage/aadhar/') . '/' . $flletOperator->front_side_aadhar }}"
                                        alt="">
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="aadhar-1-back">Backside of Aadhar Card</label>
                                <input type="file" class="form-control"
                                    value="{{ @$flletOperator->back_side_aadhar ?? old('back_side_aadhar') }}"
                                    name="back_side_aadhar" id="back_side_aadhar" accept="image/*">
                                <img class="" id="imagePreviewBackSide" />
                                @if (isset($flletOperator->back_side_aadhar) && $flletOperator->back_side_aadhar != null)
                                    <img class="imagePreview"
                                        src="{{ asset('storage/aadhar/') . '/' . $flletOperator->back_side_aadhar }}"
                                        alt="">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="companyLicense">Company License</label>
                                <input type="file" class="form-control"
                                    value="{{ @$flletOperator->company_license ?? old('company_license') }}"
                                    name="company_license" id="company_license" accept="image/*">
                                <img class="" id="imagePreviewCompanyLicense" />
                                @if (isset($flletOperator->company_license) && $flletOperator->company_license != null)
                                    <img class="imagePreview"
                                        src="{{ asset('storage/license/') . '/' . $flletOperator->company_license }}"
                                        alt="">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="Agreement">Agreement</label>
                                <input type="file" class="form-control"
                                    value="{{ @$flletOperator->aggrement ?? old('aggrement') }}" name="aggrement"
                                    id="aggrement" accept="image/*">
                                <img class="" id="imagePreviewAggrement" />
                                @if (isset($flletOperator->aggrement) && $flletOperator->aggrement != null)
                                    <img class="imagePreview"
                                        src="{{ asset('storage/aggrement/') . '/' . $flletOperator->aggrement }}"
                                        alt="">
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="enterPassword">Password</label>
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Enter password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="enterConfirmPassword">Confirm Password</label>
                                <input type="password" class="form-control" name="cpassword" id="enterConfirmPassword"
                                    placeholder="Confirm password">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <div class="my-3">
                            <button type="submit"
                                class="btn btn-primary">{{ @$flletOperator ? 'Update' : 'Add' }}</button>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>View Fleet Operator</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="addAdvertiserTable" class="table table-striped fleeterListTable table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact Person Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>PAN</th>
                                <th>GST</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>


@endsection

@section('scripts')
    <script>
        $(function() {
            // get datatable data
            $('.fleeterListTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true, // Ensure responsive design with Bootstrap 5

                ajax: {
                    url: "{{ route('admin.setting.fleetOperators.list') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                language: {
                    emptyTable: "No records found", // ✅ Message when no data exists
                    zeroRecords: "No matching records found", // ✅ Message when search returns nothing
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'person_name',
                        name: 'person_name'
                    },
                    {
                        data: 'mobile_number',
                        name: 'mobile_number'
                    },
                    {
                        data: 'email_id',
                        name: 'email_id'
                    },
                    {
                        data: 'country_id',
                        name: 'country_id',
                        render: function(data, type, row) {
                            return row.get_country_details.name;
                        }
                    },
                    {
                        data: 'state_id',
                        name: 'state_id',
                        render: function(data, type, row) {
                            return row.get_state_details.name;
                        }
                    },
                    {
                        data: 'pan_number',
                        name: 'pan_number'
                    },
                    {
                        data: 'gst_number',
                        name: 'gst_number'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row) {
                            var editUrl = '{{ route('admin.setting.fleetOperators.edit', ':id') }}'
                                .replace(':id', row.id);
                            var deleteUrl =
                                '{{ route('admin.setting.fleetOperators.delete', ':id') }}'
                                .replace(':id', row.id);
                            var html = ` <a href="${editUrl}" class="btn btn-sm btn-light-success me-1"><i
                                        class="feather icon-edit"></i></a>
                                <span  class="btn btn-sm btn-light-danger sa-bs-error-ico deleteFleeter" data-url="${deleteUrl}"><i
                                        class="feather icon-trash-2"></i></span>`;
                            return html;
                        }
                    }
                ]
            });

            // preview front_side_aadhar
            $('#front_side_aadhar').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewFrontSide');
                $('#imagePreviewFrontSide').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });

            // preview back_side_aadhar
            $('#back_side_aadhar').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewBackSide');
                $('#imagePreviewBackSide').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });

            // preview company_license
            $('#company_license').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewCompanyLicense');
                $('#imagePreviewCompanyLicense').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });

            // preview aggrement
            $('#aggrement').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewAggrement');
                $('#imagePreviewAggrement').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });
            // get State

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
                $('#state').empty().append('<option value="">Select State</option>'); // Reset state dropdown
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
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            $.validator.addMethod("regex", function(value, element, regexpr) {
                return this.optional(element) || regexpr.test(value);
            }, "Invalid format.");

            $.validator.addMethod("fileType", function(value, element, param) {
                return element.files[0] && param.includes(element.files[0].type);
            }, "Upload a valid image file.");

            $("#fleetOperatorForm").validate({
                rules: {
                    company_name: {
                        required: true,
                        maxlength: 200
                    },
                    person_name: {
                        required: true
                    },
                    mobile_number: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    email_id: {
                        required: true,
                        email: true
                    },
                    zip_code: {
                        required: true,
                        digits: true,
                        minlength: 6,
                        maxlength: 6
                    },
                    pan_number: {
                        required: true,
                        regex: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/
                    },
                    gst_number: {
                        required: true,
                        regex: /^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}$/
                    },
                    bank_name: {
                        required: true
                    },
                    country_id: {
                        required: true
                    },
                    state_id: {
                        required: true
                    },
                    city_id: {
                        required: true
                    },
                    account_number: {
                        required: true,
                        digits: true,
                        minlength: 9,
                        maxlength: 18
                    },
                    ifsc_code: {
                        required: true,
                        regex: /^[A-Z]{4}0[A-Z0-9]{6}$/
                    },
                    upi_id: {
                        regex: /^[\w.-]+@[\w.-]+$/
                    },
                    aadhar_number: {
                        required: true,
                        digits: true,
                        minlength: 12,
                        maxlength: 12
                    },
                    front_side_aadhar: {
                        required: true,
                        fileType: ["image/jpeg", "image/png", "image/gif"]
                    },
                    back_side_aadhar: {
                        required: true,
                        fileType: ["image/jpeg", "image/png", "image/gif"]
                    },
                    company_license: {
                        required: true,
                        fileType: ["image/jpeg", "image/png", "image/gif"]
                    },
                    aggrement: {
                        required: true,
                        fileType: ["image/jpeg", "image/png", "image/gif"]
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    cpassword: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    company_name: {
                        required: "Company Name is required.",
                        maxlength: "Company Name cannot exceed 200 characters."
                    },
                    person_name: {
                        required: "Contact Person Name is required."
                    },
                    mobile_number: {
                        required: "Mobile Number is required.",
                        digits: "Mobile Number must contain only digits.",
                        minlength: "Mobile Number must be exactly 10 digits.",
                        maxlength: "Mobile Number must be exactly 10 digits."
                    },
                    email_id: {
                        required: "Email ID is required.",
                        email: "Please enter a valid Email ID."
                    },
                    zip_code: {
                        required: "ZIP Code is required.",
                        digits: "ZIP Code must contain only digits.",
                        minlength: "ZIP Code must be exactly 6 digits.",
                        maxlength: "ZIP Code must be exactly 6 digits."
                    },
                    pan_number: {
                        required: "PAN Number is required.",
                        regex: "Enter a valid PAN number (e.g., AAAAA1234A)."
                    },
                    gst_number: {
                        required: "GST Number is required.",
                        regex: "Enter a valid GST number."
                    },
                    bank_name: {
                        required: "Bank Name is required."
                    },
                    country_id: {
                        required: "Country selection is required."
                    },
                    state_id: {
                        required: "State selection is required."
                    },
                    city_id: {
                        required: "City selection is required."
                    },
                    account_number: {
                        required: "Account Number is required.",
                        digits: "Account Number must contain only digits.",
                        minlength: "Account Number must be between 9 and 18 digits.",
                        maxlength: "Account Number must be between 9 and 18 digits."
                    },
                    ifsc_code: {
                        required: "IFSC Code is required.",
                        regex: "Enter a valid IFSC code."
                    },
                    upi_id: {
                        regex: "Please enter a valid UPI ID."
                    },
                    aadhar_number: {
                        required: "Aadhar Number is required.",
                        digits: "Aadhar Number must contain only digits.",
                        minlength: "Aadhar Number must be exactly 12 digits.",
                        maxlength: "Aadhar Number must be exactly 12 digits."
                    },
                    front_side_aadhar: {
                        required: "Upload the Front Side of Aadhar image.",
                        fileType: "Please upload a valid image file (JPEG, PNG, or GIF)."
                    },
                    back_side_aadhar: {
                        required: "Upload the Back Side of Aadhar image.",
                        fileType: "Please upload a valid image file (JPEG, PNG, or GIF)."
                    },
                    company_license: {
                        required: "Upload the Company License image.",
                        fileType: "Please upload a valid image file (JPEG, PNG, or GIF)."
                    },
                    aggrement: {
                        required: "Upload the Agreement document.",
                        fileType: "Please upload a valid image file (JPEG, PNG, or GIF)."
                    },
                    password: {
                        required: "Password is required.",
                        minlength: "Password must be at least 8 characters."
                    },
                    cpassword: {
                        required: "Confirm Password is required.",
                        equalTo: "Passwords do not match."
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $("#fleetOperatorUpdateForm").validate({
                rules: {
                    company_name: {
                        required: true,
                        maxlength: 200
                    },
                    person_name: {
                        required: true
                    },
                    mobile_number: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    email_id: {
                        required: true,
                        email: true
                    },
                    zip_code: {
                        required: true,
                        digits: true,
                        minlength: 6,
                        maxlength: 6
                    },
                    pan_number: {
                        required: true,
                        regex: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/
                    },
                    gst_number: {
                        required: true,
                        regex: /^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z\d]{1}[Z]{1}[A-Z\d]{1}$/
                    },
                    bank_name: {
                        required: true
                    },
                    country_id: {
                        required: true
                    },
                    state_id: {
                        required: true
                    },
                    city_id: {
                        required: true
                    },
                    account_number: {
                        required: true,
                        digits: true,
                        minlength: 9,
                        maxlength: 18
                    },
                    ifsc_code: {
                        required: true,
                        regex: /^[A-Z]{4}0[A-Z0-9]{6}$/
                    },
                    upi_id: {
                        regex: /^[\w.-]+@[\w.-]+$/
                    },
                    aadhar_number: {
                        required: true,
                        digits: true,
                        minlength: 12,
                        maxlength: 12
                    },
                },
                messages: {
                    company_name: "Company Name is required.",
                    person_name: "Contact Person Name is required.",
                    zip_code: "ZIP Code must be 6 digits.",
                    pan_number: "Enter a valid PAN number.",
                    gst_number: "Enter a valid GST number.",
                    ifsc_code: "Enter a valid IFSC code.",
                    aadhar_number: "Aadhar Number must be 12 digits.",
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // delete
            $(document).on('click', '.deleteFleeter', function(e) {
                e.preventDefault();
                var url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "GET",
                            success: function(response) {
                                Swal.fire("Deleted!",
                                    "Fleet Operator has been deleted.", "success");
                                location.reload();
                            },
                            error: function() {
                                Swal.fire("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
    <!-- [Page Specific JS] end -->
@endsection
