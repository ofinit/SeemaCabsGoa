@extends('layouts.main')

@section('title', 'Edit Cab Details')

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

        .imagePreview {
            display: none;
            width: 200px;
            height: 200px;
            object-fit: contain;
            border-radius: 12px;
        }

        .logoImage {
            width: 200px;
            height: 200px;
            object-fit: contain;
            border-radius: 12px;
            margin-top: 10px;
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.cabs.index') }}">Cab Management</a></li>
                        <li class="breadcrumb-item" aria-current="page">Edit Cab Details</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Edit Cab Details</h2>
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
            <form class="cabForm" id="add-cab" action="{{ route('admin.cabs.update') }}" method="post"
                enctype=multipart/form-data>
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Cab Details</h5>
                    </div>
                    <input type="hidden" name="id" value="{{ $cab->id ?? '' }}">
                    <div class="card-body">
                        <div class="row">
                            {{-- <div class="col-12">
                            <div class="mb-3">
                                <label class="col-form-label">Fleet Operator</label>
                                <select
                                    class="form-control"
                                    data-trigger
                                    name="fleet_operator_id"
                                    id="searchFleetOperator"
                                    required data-parsley-required-message="Please select fleet operator."
                                    data-parsley-errors-container="#fleetOperator-error-container">
                                    <option value="" hidden>Select fleet operator</option>
                                    @if (isset($fleetOperator) && count($fleetOperator) > 0)
                                        @foreach ($fleetOperator as $list)
                                            <option value="{{$list->id}}" {{($cab->fleet_operator_id==$list->id)?'selected':''}}>{{$list->person_name??''}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="fleetOperator-error-container" class="text-danger"></div>
                            </div>
                        </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="cab-zone">Cab Zone</label>
                                    <select class="form-control" name="cab_zone[]" id="cab-zone" multiple required
                                        data-parsley-required-message="Please select cab zone."
                                        data-parsley-errors-container="#cabZon-error-container">
                                        <option value="" hidden>Select cab zone</option>
                                        @if (isset($citys) && count($citys) > 0)
                                            @php
                                                $zoneIds = explode(',', $cab->zone);
                                            @endphp
                                            @foreach ($citys as $city)
                                                <option value="{{ $city->id ?? '' }}"
                                                    {{ in_array($city->id, $zoneIds) ? 'selected' : '' }}>
                                                    {{ $city->name ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="cabZon-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="cabName">Cab Number</label>
                                    <input type="text" class="form-control" name="cab_number"
                                        value="{{ $cab->number ?? '' }}" id="cabName" placeholder="Enter cab number"
                                        required minlength="6" maxlength="12"
                                        data-parsley-required-message="Please enter cab number."
                                        data-parsley-minlength-message="Cab number must be at least 6 characters."
                                        data-parsley-maxlength-message="Cab number must be at most 12 characters.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="cabType">Cab Type</label>
                                    <select class="form-select" id="cabType" name="cab_type" required
                                        data-parsley-required-message="Please select cab type."
                                        data-parsley-errors-container="#cabType-error-container">
                                        <option value="" hidden>Select cab type</option>
                                        @php
                                            $typeList = getCabType();
                                        @endphp
                                        @if (count($typeList) > 0)
                                            @foreach ($typeList as $key => $list)
                                                <option value="{{ $key + 1 }}"
                                                    {{ $cab->type == $key + 1 ? 'selected' : '' }}>
                                                    {{ $list ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="cabType-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="cabModel">Cab Model</label>
                                    <select class="form-select" id="cabModel" name="cab_model" required
                                        data-parsley-required-message="Please select cab model."
                                        data-parsley-errors-container="#cabModel-error-container">
                                        <option value="" hidden>Select cab model</option>
                                        @php
                                            $modelList = getModal();
                                        @endphp
                                        @if (count($modelList) > 0)
                                            @foreach ($modelList as $key => $list)
                                                <option value="{{ $key + 1 }}"
                                                    {{ $cab->model == $key + 1 ? 'selected' : '' }}>
                                                    {{ $list ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="cabModel-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="modelName">Model Name</label>
                                    <select class="form-select" id="modelName" name="model_id" required
                                        data-parsley-required-message="Please select model name."
                                        data-parsley-errors-container="#cabName-error-container">
                                        <option value="" hidden>Select cab model name</option>
                                        @if (isset($cabModels) && count($cabModels) > 0)
                                            @foreach ($cabModels as $model)
                                                <option value="{{ $model->id ?? '' }}"
                                                    {{ $cab->model_id == $model->id ? 'selected' : '' }}>
                                                    {{ $model->name ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="cabName-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="modelColor">Color</label>
                                    <select class="form-select" id="modelColor" name="color_id" required
                                        data-parsley-required-message="Please select color."
                                        data-parsley-errors-container="#colorNew-error-container">
                                        <option value="" hidden>Select cab color</option>
                                        @if (isset($cabColors) && count($cabColors) > 0)
                                            @foreach ($cabColors as $model)
                                                <option value="{{ $model->id ?? '' }}"
                                                    {{ $cab->color_id == $model->id ? 'selected' : '' }}>
                                                    {{ $model->name ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="colorNew-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="seatNumber">No. Of Seats</label>
                                    <select class="form-select" id="seatNumber" name="no_of_seats" required
                                        data-parsley-required-message="Please select no. of seats."
                                        data-parsley-errors-container="#npOfSeat-error-container">
                                        <option value="" hidden>Select no. of seats</option>
                                        <option value="3" {{ $cab->no_of_seats == 3 ? 'selected' : '' }}>3</option>
                                        <option value="4" {{ $cab->no_of_seats == 4 ? 'selected' : '' }}>4</option>
                                        <option value="5" {{ $cab->no_of_seats == 5 ? 'selected' : '' }}>5</option>
                                        <option value="6" {{ $cab->no_of_seats == 6 ? 'selected' : '' }}>6</option>
                                    </select>
                                    <div id="npOfSeat-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="fuelType">Fuel Type</label>
                                    <select class="form-select" id="fuelType" name="fuel_type" required
                                        data-parsley-required-message="Please select fuel type."
                                        data-parsley-errors-container="#fuleType-error-container">
                                        <option value="" hidden>Select fuel type</option>
                                        @php
                                            $fuelType = getFuelType();
                                        @endphp
                                        @if (count($fuelType) > 0)
                                            @foreach ($fuelType as $key => $list)
                                                <option value="{{ $key + 1 }}"
                                                    {{ $cab->fuel_type == $key + 1 ? 'selected' : '' }}>
                                                    {{ $list ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="fuleType-error-container" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="card">
                    <div class="card-header">
                        <h5>Price Details <span class="cancellation-notice">( Cancellation: Free till 1 hours of departure
                                )</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="baseFare">Base Fare</label>
                                    <select class="form-select" id="baseFare" name="base_fare" required
                                        data-parsley-required-message="Please select base fare."
                                        data-parsley-errors-container="#baseFare-error-container">
                                        <option value="" hidden>Select base fare</option>
                                        @if (isset($baseFare) && count($baseFare))
                                            @foreach ($baseFare as $list)
                                                <option value="{{ $list->id ?? '' }}"
                                                    {{ $cab->base_fare == $list->id ? 'selected' : '' }}>Rs.
                                                    {{ $list->value ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="baseFare-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kmNumber">No. of Kms Included</label>
                                    <select class="form-select" id="kmNumber" name="no_of_kms" required
                                        data-parsley-required-message="Please select fare no. of Kms."
                                        data-parsley-errors-container="#noKms-error-container">
                                        <option value="" hidden>Select No. of Kms</option>
                                        @if (isset($noOfKms) && count($noOfKms))
                                            @foreach ($noOfKms as $list)
                                                <option value="{{ $list->id ?? '' }}"
                                                    {{ $cab->no_of_kms == $list->id ? 'selected' : '' }}>
                                                    {{ $list->value ?? '' }} kms</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="noKms-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="addKm">Additional KM Charges</label>
                                    <select class="form-select" id="addKm" name="additional_km_charges" required
                                        data-parsley-required-message="Please select additional km charges."
                                        data-parsley-errors-container="#KmCharges-error-container">
                                        <option value="" hidden>Select additional km charges</option>
                                        @if (isset($additional) && count($additional))
                                            @foreach ($additional as $list)
                                                <option value="{{ $list->id ?? '' }}"
                                                    {{ $cab->additional_km_charges == $list->id ? 'selected' : '' }}>Rs.
                                                    {{ $list->value ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="KmCharges-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="waitingCharge">Waiting Charges ( After 30 mins
                                        )</label>
                                    <select class="form-select" id="waitingCharge" name="waiting_charges" required
                                        data-parsley-required-message="Please select waiting charges."
                                        data-parsley-errors-container="#waitingCharges-error-container">
                                        <option value="" hidden>Select Waiting Charges</option>
                                        @if (isset($waitingChar) && count($waitingChar))
                                            @foreach ($waitingChar as $list)
                                                <option value="{{ $list->id ?? '' }}"
                                                    {{ $cab->waiting_charges == $list->id ? 'selected' : '' }}>Rs.
                                                    {{ $list->value ?? '' }} mins</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div id="waitingCharges-error-container" class="text-danger"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="card">
                    <div class="card-header">
                        <h5>Cab Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label" for="registerCertificate">Registration Certificate</label>
                            </div>
                            <div class="col-md-6">
                                <label class="card-upload-box" for="register-front">
                                    <p>(Frontside of Registration Certificate)</p>
                                    <p>Drag & Drop file here or click to browse</p>
                                </label>
                                <input type="file" class="d-none" id="register-front" accept="image/*"
                                    name="front_registration_certificate"
                                    data-parsley-errors-container="#frontRegisteration-error-container">
                                <div id="frontRegisteration-error-container" class="text-danger"></div>
                                <img class="imagePreview" id="registerFrontPreview" />
                                <span id="registerFrontError" class="text-danger"></span>
                                @if (isset($cab->front_registration_certificate) && $cab->front_registration_certificate != null)
                                    <img class="logoImage" src="{{ $cab->front_registration_image }}" alt="Image">
                                @endif

                            </div>
                            <div class="col-md-6">
                                <label class="card-upload-box" for="register-back">
                                    <p>(Backside of Registration Certificate)</p>
                                    <p>Drag & Drop file here or click to browse</p>
                                </label>
                                <input type="file" class="d-none" id="register-back" accept="image/*"
                                    name="back_registration_certificate"
                                    data-parsley-errors-container="#backRegisteration-error-container">
                                <div id="backRegisteration-error-container" class="text-danger"></div>
                                <img class="imagePreview" id="registerBackPreview" />
                                <span id="registerBackError" class="text-danger"></span>
                                @if (isset($cab->back_registration_certificate) && $cab->back_registration_certificate != null)
                                    <img class="logoImage" src="{{ $cab->back_registration_image }}" alt="Image">
                                @endif
                            </div>
                            <div class="col-6">
                                <div class="my-3">
                                    <label class="form-label" for="insurance">Insurance</label>
                                    <input type="file" class="form-control mb-2" id="insurance" accept="image/*"
                                        name="insurance">
                                    <img class="imagePreview" id="insurancePreview" />
                                    <span id="insuranceError" class="text-danger"></span>
                                    @if (isset($cab->insurance) && $cab->insurance != null)
                                        <img class="logoImage" src="{{ $cab->insurance_image }}" alt="Image">
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="my-3">
                                    <label class="form-label" for="insurance">Insurance Expiry Date</label>
                                    <input type="text" class="form-control" id="expiryDate"
                                        {{ $cab->insurance_expiry_date ?? '' }} name="insurance_expiry_date"
                                        placeholder="Please select expiry date.">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card_ }}">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5>Driver Details</h5>
                        <a id="addNewDriver" class="btn btn-sm btn-light-secondary fw-bold"> <i data-feather="plus"></i>
                            Add New
                            Driver</a>
                    </div>
                </div>
                @if (isset($cab->getDriverDetails) && count($cab->getDriverDetails) > 0)
                    @foreach ($cab->getDriverDetails as $key => $list)
                        <div class="card card_{{ $list->id }}">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5>Driver Details {{ $key + 1 }}</h5>
                                {{-- @if ($key == 0)
                             <a id="addNewDriver" class="btn btn-sm btn-light-secondary fw-bold"> <i data-feather="plus"></i>
                              Add New
                              Driver</a>
                          @endif --}}
                                <button type="button" class="btn btn-sm btn-light-danger deleteDriver"
                                    data-id="{{ $list->id }}"> <i data-feather="minus"></i> Remove Driver</button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <input type="hidden" name="edit_driver_id[]" value="{{ $list->id }}"
                                                id="">
                                            <input type="hidden" class="deletedFleg_{{ $list->id }}"
                                                name="deletedFleg[]" value="">
                                            <label class="form-label" for="driver1Name">Driver Name</label>
                                            <input type="text" class="form-control" id="driver1Name"
                                                name="edit_driver_name[]" value="{{ $list->name ?? '' }}"
                                                placeholder="Enter Driver name" required minlength="3" maxlength="50"
                                                data-parsley-required-message="Please enter driver name."
                                                data-parsley-minlength-message="Minimum 3 characters required."
                                                data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="driver1Number">Driver Mobile</label>
                                            <input type="number" class="form-control" id="driver1Number"
                                                name="edit_driver_mobile[]" placeholder="Enter driver number"
                                                value="{{ $list->mobile ?? '' }}" required minlength="10" maxlength="10"
                                                data-parsley-required-message="Please enter driver mobile."
                                                data-parsley-pattern-message="Invalid mobile number."
                                                data-parsley-minlength-message="Must be 10 digits."
                                                data-parsley-maxlength-message="Must be 10 digits.">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="bank1Name">Bank Name</label>
                                            <input type="text" class="form-control" id="bank1Name"
                                                name="edit_bank_name[]" placeholder="Enter bank name"
                                                value="{{ $list->bank_name ?? '' }}" minlength="3" maxlength="50"
                                                data-parsley-required-message="Please enter bank name."
                                                data-parsley-minlength-message="Minimum 3 characters required."
                                                data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="branch1Name">Branch Name</label>
                                            <input type="text" class="form-control" id="branch1Name"
                                                name="edit_branch_name[]" placeholder="Enter branch name"
                                                value="{{ $list->branch_name ?? '' }}" minlength="3" maxlength="50"
                                                data-parsley-required-message="Please enter branch name."
                                                data-parsley-minlength-message="Minimum 3 characters required."
                                                data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="account1Name">Account Holder Name</label>
                                            <input type="text" class="form-control" id="account1Name"
                                                value="{{ $list->account_holder_name ?? '' }}"
                                                name="edit_account_holder_name[]" placeholder="Enter account holder name"
                                                minlength="3" maxlength="50"
                                                data-parsley-required-message="Please enter account holder name."
                                                data-parsley-minlength-message="Minimum 3 characters required."
                                                data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="account1Number">Account Number</label>
                                            <input type="number" class="form-control" id="account1Number"
                                                name="edit_account_number[]" placeholder="Enter account number"
                                                value="{{ $list->account_number ?? '' }}" pattern="^\d{9,18}$"
                                                minlength="9" maxlength="18"
                                                data-parsley-required-message="Please enter account number."
                                                data-parsley-pattern-message="Invalid account number."
                                                data-parsley-minlength-message="Minimum 9 digits required."
                                                data-parsley-maxlength-message="Maximum 18 digits allowed.">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="ifsc1Code">IFSC Code</label>
                                            <input type="text" class="form-control" id="ifsc1Code"
                                                name="edit_ifsc_code[]" placeholder="Enter IFSC code"
                                                value="{{ $list->ifsc_code ?? '' }}" {{-- pattern="^[A-Z]{4}0[A-Z0-9]{6}$"  --}} minlength="11"
                                                maxlength="11" data-parsley-required-message="Please enter IFSC code."
                                                data-parsley-pattern-message="Invalid IFSC code format."
                                                data-parsley-minlength-message="Must be exactly 11 characters."
                                                {{-- data-parsley-maxlength-message="Must be exactly 11 characters." --}}>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="upi1Number">UPI ID</label>
                                            <input type="text" class="form-control" id="upi1Number"
                                                name="edit_upi_id[]" placeholder="Enter UPI ID"
                                                value="{{ $list->upi_id ?? '' }}" minlength="6" maxlength="50"
                                                pattern="^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$"
                                                data-parsley-required-message="Please enter UPI ID."
                                                data-parsley-minlength-message="UPI ID must be at least 6 characters."
                                                data-parsley-maxlength-message="UPI ID cannot exceed 50 characters."
                                                data-parsley-pattern-message="Invalid UPI ID format.">

                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="license1Number">Driving License Number</label>
                                            <input type="text" class="form-control" id="license1Number"
                                                name="edit_driving_license_number[]"
                                                placeholder="Enter driving license number"
                                                value="{{ $list->driving_license_number ?? '' }}" minlength="10"
                                                maxlength="20" pattern="^[A-Z0-9-]+$"
                                                data-parsley-required-message="Please enter driving license number."
                                                data-parsley-minlength-message="Driving license number must be at least 10 characters."
                                                data-parsley-maxlength-message="Driving license number cannot exceed 20 characters."
                                                data-parsley-pattern-message="Invalid format. Use only uppercase letters, numbers, and hyphens.">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="aadhar1Number">Aadhar Card Number</label>
                                            <input type="number" class="form-control" id="aadhar1Number"
                                                name="edit_aadhar_card_number[]" placeholder="Enter aadhar card number"
                                                value="{{ $list->aadhar_card_number ?? '' }}" pattern="^\d{12}$"
                                                minlength="12" maxlength="12"
                                                data-parsley-required-message="Please enter Aadhar card number."
                                                data-parsley-pattern-message="Invalid Aadhar number."
                                                data-parsley-minlength-message="Must be 12 digits."
                                                data-parsley-maxlength-message="Must be 12 digits.">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="driver1Image">Driver Profile Picture</label>
                                            <input type="file" class="form-control driver1Image mb-2"
                                                id="driver1Image" data-id="{{ $list->id }}" accept="image/*"
                                                name="edit_driver_profile_picture[]"
                                                data-parsley-required-message="Please upload diver profile picture.">
                                            <img class="imagePreview driver1ImagePreview"
                                                id="driver1ImagePreview_{{ $list->id }}" />
                                            <span id="driver1ImageError_{{ $list->id }}" class="text-danger"></span>
                                            @if (isset($list->profile_picture) && $list->profile_picture != null)
                                                <img class="logoImage"
                                                    src="{{ asset('storage/profile/') . '/' . $list->profile_picture }}"
                                                    alt="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="my-3">
                                            <h5>Driving License</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="aadhar-1-back">Front side of Driving
                                            License</label>
                                        <input type="file" class="form-control license-1-front" id="license-1-front"
                                            data-id="{{ $list->id }}" accept="image/*"
                                            name="edit_front_driver_license[]"
                                            data-parsley-required-message="Please upload driving license front image."
                                            data-parsley-errors-container="#drivingLicence{{ $list->id }}-error-container">
                                        <div id="drivingLicence{{ $list->id }}-error-container" class="text-danger">
                                        </div>
                                        <img class="imagePreview license-1-frontPreview"
                                            id="license-1-frontPreview_{{ $list->id }}" />
                                        <span id="license-1-frontError_{{ $list->id }}" class="text-danger"></span>
                                        @if (isset($list->front_license) && $list->front_license != null)
                                            <img class="logoImage"
                                                src="{{ asset('storage/license/') . '/' . $list->front_license }}"
                                                alt="">
                                        @endif
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <label class="card-upload-box" for="license-1-front">
                                            <p>(Frontside of Driving License)</p>
                                            <p>Drag & Drop file here or click to browse</p>
                                        </label>
                                        <input type="file" class="d-none license-1-front" id="license-1-front"
                                            data-id="{{ $list->id }}" accept="image/*"
                                            name="edit_front_driver_license[]"
                                            data-parsley-required-message="Please upload driving license front image."
                                            data-parsley-errors-container="#drivingLicence{{ $list->id }}-error-container">
                                        <div id="drivingLicence{{ $list->id }}-error-container" class="text-danger">
                                        </div>
                                        <img class="imagePreview license-1-frontPreview"
                                            id="license-1-frontPreview_{{ $list->id }}" />
                                        <span id="license-1-frontError_{{ $list->id }}" class="text-danger"></span>
                                        @if (isset($list->front_license) && $list->front_license != null)
                                            <img class="logoImage"
                                                src="{{ asset('storage/license/') . '/' . $list->front_license }}"
                                                alt="">
                                        @endif
                                    </div> --}}
                                    <div class="col-md-6">
                                        <label class="form-label" for="aadhar-1-back">Back side of Driving
                                            License</label>
                                        <input type="file" class="form-control license-1-back" id="license-1-back"
                                            data-id="{{ $list->id }}" accept="image/*"
                                            name="edit_back_driver_license[]"
                                            data-parsley-required-message="Please upload driving license back image."
                                            data-parsley-errors-container="#drivingLicenceBack{{ $list->id }}-error-container">
                                        <div id="drivingLicenceBack{{ $list->id }}-error-container"
                                            class="text-danger"></div>
                                        <img class="imagePreview" id="license-1-backPreview_{{ $list->id }}" />
                                        <span id="license-1-backError_{{ $list->id }}" class="text-danger"></span>
                                        @if (isset($list->back_license) && $list->back_license != null)
                                            <img class="logoImage"
                                                src="{{ asset('storage/license/') . '/' . $list->back_license }}"
                                                alt="">
                                        @endif
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <label class="card-upload-box" for="license-1-back">
                                            <p>(Backside of Driving License)</p>
                                            <p>Drag & Drop file here or click to browse</p>
                                        </label>
                                        <input type="file" class="d-none license-1-back" id="license-1-back"
                                            data-id="{{ $list->id }}" accept="image/*"
                                            name="edit_back_driver_license[]"
                                            data-parsley-required-message="Please upload driving license back image."
                                            data-parsley-errors-container="#drivingLicenceBack{{ $list->id }}-error-container">
                                        <div id="drivingLicenceBack{{ $list->id }}-error-container"
                                            class="text-danger"></div>
                                        <img class="imagePreview" id="license-1-backPreview_{{ $list->id }}" />
                                        <span id="license-1-backError_{{ $list->id }}" class="text-danger"></span>
                                        @if (isset($list->back_license) && $list->back_license != null)
                                            <img class="logoImage"
                                                src="{{ asset('storage/license/') . '/' . $list->back_license }}"
                                                alt="">
                                        @endif
                                    </div> --}}
                                    <div class="col-md-6">
                                        <div class="my-3">
                                            <label class="form-label" for="aadhar-1-front">Frontside of Aadhar
                                                Card</label>
                                            <input type="file" class="form-control aadhar-1-front" id="aadhar-1-front"
                                                data-id="{{ $list->id }}" accept="image/*"
                                                name="edit_front_aadhar_card[]"
                                                data-parsley-required-message="Please upload aadhar card front image.">
                                            <img class="imagePreview" id="aadhar-1-frontPreview_{{ $list->id }}" />
                                            <span id="aadhar-1-frontError_{{ $list->id }}"
                                                class="text-danger"></span>
                                            @if (isset($list->front_aadhar_card) && $list->front_aadhar_card != null)
                                                <img class="logoImage"
                                                    src="{{ asset('storage/aadhar/') . '/' . $list->front_aadhar_card }}"
                                                    alt="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-3 mb-5">
                                            <label class="form-label" for="aadhar-1-back">Backside of Aadhar Card</label>
                                            <input type="file" class="form-control aadhar-1-back" id="aadhar-1-back"
                                                data-id="{{ $list->id }}" accept="image/*"
                                                name="edit_back_aadhar_card[]"
                                                data-parsley-required-message="Please upload aadhar card back image.">
                                            <img class="imagePreview" id="aadhar-1-backPreview_{{ $list->id }}" />
                                            <span id="aadhar-1-backError_{{ $list->id }}" class="text-danger"></span>
                                            @if (isset($list->back_aadhar_card) && $list->back_aadhar_card != null)
                                                <img class="logoImage"
                                                    src="{{ asset('storage/aadhar/') . '/' . $list->back_aadhar_card }}"
                                                    alt="">
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="form-check">
                                                <input type="hidden" class="assign_{{ $list->id }}"
                                                    name="assign_{{ $list->id }}" id="">
                                                <input class="form-check-input assignDriverButton" type="radio"
                                                    name="assignDriver[]" value="1"
                                                    {{ $list->assignDriver == 1 ? 'Checked' : '' }}
                                                    data-id="{{ $list->id }}" id="assignDriver1">
                                                <label class="form-label mb-0" for="assignDriver1">
                                                    <h5 class="mb-0">Assign Driver to Cab </h5>
                                                </label>
                                                <br><label id="assignDriver[]-error" class="error"
                                                    for="assignDriver[]"></label>
                                            </div>
                                        </div>
                                        <div id="assignDe-error-container" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div id="driverDetailsContainer">
                </div>
                <div class="card">
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.cabForm').parsley();


            let driverCount = 1;

            $('#addNewDriver').on('click', function() {
                driverCount++;
                const newDriverCard = `
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5>Driver Details</h5>
                            <button class="btn btn-sm btn-light-danger removeDriver"> <i data-feather="minus"></i> Remove Driver</button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="driverName-${driverCount}">Driver Name</label>
                                        <input type="text" class="form-control" id="driverName-${driverCount}" name="driver_name[]" placeholder="Enter Driver name"
                                        required minlength="3" maxlength="50"
                                        data-parsley-required-message="Please enter driver name."
                                        data-parsley-minlength-message="Minimum 3 characters required."
                                        data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="driverNumber-${driverCount}">Driver Mobile</label>
                                        <input type="number" class="form-control" id="driverNumber-${driverCount}" name="driver_mobile[]" placeholder="Enter driver number"
                                        required  minlength="10" maxlength="10"
                                        data-parsley-required-message="Please enter driver mobile."
                                        data-parsley-minlength-message="Must be 10 digits."
                                        data-parsley-maxlength-message="Must be 10 digits.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="bankName-${driverCount}">Bank Name</label>
                                        <input type="text" class="form-control" id="bankName-${driverCount}" name="bank_name[]" placeholder="Enter bank name"
                                        minlength="3" maxlength="50"
                                        data-parsley-required-message="Please enter bank name."
                                        data-parsley-minlength-message="Minimum 3 characters required."
                                        data-parsley-maxlength-message="Maximum 50 characters allowed." >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="branchName-${driverCount}">Branch Name</label>
                                        <input type="text" class="form-control" id="branchName-${driverCount}" name="branch_name[]" placeholder="Enter branch name"
                                        minlength="3" maxlength="50"
                                        data-parsley-required-message="Please enter branch name."
                                        data-parsley-minlength-message="Minimum 3 characters required."
                                        data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="accountHolderName-${driverCount}">Account Holder Name</label>
                                        <input type="text" class="form-control" id="accountHolderName-${driverCount}" name="account_holder_name[]" placeholder="Enter account holder name"
                                        minlength="3" maxlength="50"
                                        data-parsley-required-message="Please enter account holder name."
                                        data-parsley-minlength-message="Minimum 3 characters required."
                                        data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="accountNumber-${driverCount}">Account Number</label>
                                        <input type="number" class="form-control" id="accountNumber-${driverCount}" name="account_number[]" placeholder="Enter account number"
                                         minlength="9" maxlength="18"
                                        data-parsley-required-message="Please enter account number."
                                        data-parsley-minlength-message="Minimum 9 digits required."
                                        data-parsley-maxlength-message="Maximum 18 digits allowed.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="ifscCode-${driverCount}">IFSC Code</label>
                                        <input type="text" class="form-control" id="ifscCode-${driverCount}" name="ifsc_code[]" placeholder="Enter IFSC code"
                                        minlength="11" maxlength="11"
                                        data-parsley-required-message="Please enter IFSC code."
                                        data-parsley-minlength-message="Must be exactly 11 characters."
                                        data-parsley-maxlength-message="Must be exactly 11 characters.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="upiId-${driverCount}">UPI ID</label>
                                        <input type="text" class="form-control" id="upiId-${driverCount}" name="upi_id[]" placeholder="Enter UPI ID"
                                        minlength="6" maxlength="50"
                                        data-parsley-required-message="Please enter UPI ID."
                                        data-parsley-minlength-message="UPI ID must be at least 6 characters."
                                        data-parsley-maxlength-message="UPI ID cannot exceed 50 characters.">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="licenseNumber-${driverCount}">Driving License Number</label>
                                        <input type="number" class="form-control" id="licenseNumber-${driverCount}" name="driving_license_number[]" placeholder="Enter driving license number"
                                         minlength="10" maxlength="20"

                                        data-parsley-required-message="Please enter driving license number."
                                        data-parsley-minlength-message="Driving license number must be at least 10 characters."
                                        data-parsley-maxlength-message="Driving license number cannot exceed 20 characters." >
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="aadharNumber-${driverCount}">Aadhar Card Number</label>
                                        <input type="number" class="form-control" id="aadharNumber-${driverCount}" name="aadhar_card_number[]" placeholder="Enter Aadhar card number"
                                         minlength="12" maxlength="12"
                                        data-parsley-required-message="Please enter Aadhar card number."
                                        data-parsley-pattern-message="Invalid Aadhar number."
                                        data-parsley-minlength-message="Must be 12 digits."
                                        data-parsley-maxlength-message="Must be 12 digits.">
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="driverImage-${driverCount}">Driver Profile Picture</label>
                                        <input type="file" class="form-control ProfielPictureNew mb-2" data-id="${driverCount}" id="driverImage-${driverCount}" accept="image/*" name="driver_profile_picture[]"
                                        data-parsley-required-message="Please upload diver profile picture.">
                                        <img class="imagePreview" id="ProfielPictureNewPreview_${driverCount}" />
                                         <span id="ProfielPictureNewError_${driverCount}" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="my-3">
                                        <h5>Driving License</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                   <label class="form-label" for="aadhar-1-back">Front side of Driving
                                            License</label>
                                    <input type="file" class="form-control licenseFront" data-id="${driverCount}" id="licenseFront-${driverCount}" accept="image/*" name="front_driver_license[]"
                                    data-parsley-required-message="Please upload driving license front image."
                                    data-parsley-errors-container="#drivingLicence-error-container">
                                    <div id="drivingLicence-error-container" class="text-danger"></div>
                                    <img class="imagePreview" id="licenseFrontPreview_${driverCount}" />
                                    <span id="licenseFrontError_${driverCount}" class="text-danger"></span>
                                </div>
                                <div class="col-md-6">
                                   <label class="form-label" for="aadhar-1-back">Back side of Driving
                                            License</label>
                                    <input type="file" class="form-control licenseBack" data-id="${driverCount}" id="licenseBack-${driverCount}" accept="image/*" name="back_driver_license[]"
                                    data-parsley-required-message="Please upload driving license back image."
                                data-parsley-errors-container="#drivingLicenceBack-error-container">
                                <div id="drivingLicenceBack-error-container" class="text-danger"></div>
                                <img class="imagePreview" id="licenseBackPreview_${driverCount}" />
                                <span id="licenseBackError_${driverCount}" class="text-danger"></span>
                                </div>
                                <div class="col-md-6">
                                    <div class="my-3">
                                        <label class="form-label" for="aadharFront-${driverCount}">Frontside of Aadhar Card</label>
                                        <input type="file" class="form-control aadharFrontN" data-id="${driverCount}" id="aadharFront-${driverCount}" accept="image/*" name="front_aadhar_card[]"
                                        data-parsley-required-message="Please upload aadhar card front image.">
                                         <img class="imagePreview" id="aadharFrontNPreview_${driverCount}" />
                                          <span id="aadharFrontNError_${driverCount}" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mt-3 mb-5">
                                        <label class="form-label" for="aadharBack-${driverCount}">Backside of Aadhar Card</label>
                                        <input type="file" class="form-control aadharBackN" data-id="${driverCount}" id="aadharBack-${driverCount}" accept="image/*" name="back_aadhar_card[]"
                                        data-parsley-required-message="Please upload aadhar card back image.">
                                         <img class="imagePreview" id="aadharBackNPreview_${driverCount}" />
                                         <span id="aadharBackNError_${driverCount}" class="text-danger"></span>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-12">
                                    <div class="d-flex justify-content-start align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="assignDriver[]" value="1" id="assignDriver-${driverCount}">
                                            <label class="form-label mb-0" for="assignDriver-${driverCount}">
                                                <h5 class="mb-0">Assign Driver to Cab </h5>
                                            </label>
                                             <br><label id="assignDriver[]-error" class="error" for="assignDriver[]"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#driverDetailsContainer').append(newDriverCard);
            });

            $('#driverDetailsContainer').on('click', '.removeDriver', function() {
                $(this).closest('.card').remove();
            });
            $(document).on('click', '.deleteDriver', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('.deletedFleg_' + id).val(1);
                $('.card_' + id).hide();
            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var cabZoneSelect = document.getElementById("cab-zone");
            if (cabZoneSelect) {
                new Choices(cabZoneSelect, {
                    removeItemButton: true
                });
            }
        });

        // Image Preview
        $(function() {
            $('#register-front').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#registerFrontPreview');
                $('#registerFrontError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#registerFrontError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#register-back').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#registerBackPreview');
                $('#registerBackError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#registerBackError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#insurance').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#insurancePreview');
                $('#insuranceError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#insuranceError').text('Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('.driver1Image').on('change', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#driver1ImagePreview_' + id);
                $('#driver1ImageError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#driver1ImageError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('.license-1-front').on('change', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#license-1-frontPreview_' + id);
                $('#license-1-frontError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#license-1-frontError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('.license-1-back').on('change', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#license-1-backPreview_' + id);
                $('#license-1-backError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#license-1-backError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('.aadhar-1-front').on('change', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#aadhar-1-frontPreview_' + id);
                $('#aadhar-1-frontError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadhar-1-frontError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('.aadhar-1-back').on('change', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#aadhar-1-backPreview_' + id);
                $('#aadhar-1-backError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadhar-1-backError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });


            $('#aadhar-1-back').on('change', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#aadhar-1-backPreview_' + id);
                $('#aadhar-1-backError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadhar-1-backError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('body').on('change', '.licenseFront', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#licenseFrontPreview_' + id);
                $('#licenseFrontError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#licenseFrontError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('body').on('change', '.licenseBack', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#licenseBackPreview_' + id);
                $('#licenseBackError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#licenseBackError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('body').on('change', '.ProfielPictureNew', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#ProfielPictureNewPreview_' + id);
                $('#ProfielPictureNewError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#ProfielPictureNewError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('body').on('change', '.aadharFrontN', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#aadharFrontNPreview_' + id);
                $('#aadharFrontNError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadharFrontNError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('body').on('change', '.aadharBackN', function(event) {
                const file = event.target.files[0];
                var id = $(this).data('id');
                const imagePreview = $('#aadharBackNPreview_' + id);
                $('#aadharBackNError_' + id).text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadharBackNError_' + id).text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });


            $('body').on('change', '.assignDriverButton', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                if ($(this).is(":checked")) {
                    $('.assign_' + id).val(1);
                }
            })

        });
        (function() {
            const expiryDatePicker = new Datepicker(document.querySelector('#expiryDate'), {
                buttonClass: 'btn',
                format: 'yyyy-mm-dd',
                autohide: true
            });
        })();
    </script>
@endsection
