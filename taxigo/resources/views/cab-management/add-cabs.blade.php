@extends('layouts.main')

@section('title', 'Add Cab Details')

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
            object-fit: contain;
            border-radius: 12px;
            margin: 10px 0px;
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
                        <li class="breadcrumb-item" aria-current="page">Add Cab Details</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Add Cab Details</h2>
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
            <form class="cabForm" id="add-cab" action="{{ route('admin.cabs.store') }}" method="post"
                enctype=multipart/form-data>
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5>Add Cab Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="cab-zone">Cab Zone</label>
                                    <select class="form-control" name="cab_zone[]" id="cab-zone" multiple required
                                        data-parsley-required-message="Please select cab zone."
                                        data-parsley-errors-container="#cabZon-error-container">
                                        <option value="" hidden>Select cab zone</option>
                                        @if (isset($citys) && count($citys) > 0)
                                            @foreach ($citys as $city)
                                                <option value="{{ $city->id ?? '' }}">{{ $city->name ?? '' }}</option>
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
                                        value="{{ old('cab_number') }}" id="cabName" placeholder="Enter cab number"
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
                                                <option value="{{ $key + 1 }}">{{ $list ?? '' }}</option>
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
                                                <option value="{{ $key + 1 }}">{{ $list ?? '' }}</option>
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
                                                <option value="{{ $model->id ?? '' }}">{{ $model->name ?? '' }}</option>
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
                                                <option value="{{ $model->id ?? '' }}">{{ $model->name ?? '' }}</option>
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
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
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
                                            $fuelTypeList = getFuelType();
                                        @endphp
                                        @if (count($fuelTypeList) > 0)
                                            @foreach ($fuelTypeList as $key => $list)
                                                <option value="{{ $key + 1 }}">{{ $list ?? '' }}</option>
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
                                                <option value="{{ $list->id ?? '' }}">Rs. {{ $list->value ?? '' }}
                                                </option>
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
                                                <option value="{{ $list->id ?? '' }}">{{ $list->value ?? '' }} kms
                                                </option>
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
                                                <option value="{{ $list->id ?? '' }}">Rs. {{ $list->value ?? '' }}
                                                </option>
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
                                                <option value="{{ $list->id ?? '' }}">Rs. {{ $list->value ?? '' }} mins
                                                </option>
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
                                    data-parsley-required-message="Please upload registration certificate front image."
                                    data-parsley-errors-container="#frontRegisteration-error-container">
                                <div id="frontRegisteration-error-container" class="text-danger"></div>
                                <img class="imagePreview" id="registerFrontPreview" />
                                <span id="registerFrontError" class="text-danger"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="card-upload-box" for="register-back">
                                    <p>(Backside of Registration Certificate)</p>
                                    <p>Drag & Drop file here or click to browse</p>
                                </label>
                                <input type="file" class="d-none" id="register-back" accept="image/*"
                                    name="back_registration_certificate"
                                    data-parsley-required-message="Please upload registration certificate back image."
                                    data-parsley-errors-container="#backRegisteration-error-container">
                                <div id="backRegisteration-error-container" class="text-danger"></div>
                                <img class="imagePreview" id="registerBackPreview" />
                                <span id="registerBackError" class="text-danger"></span>
                            </div>
                            <div class="col-6">
                                <div class="my-3">
                                    <label class="form-label" for="insurance">Insurance</label>
                                    <input type="file" class="form-control mb-2" id="insurance" accept="image/*"
                                        name="insurance" data-parsley-required-message="Please upload insurance image.">
                                    <img class="imagePreview" id="insurancePreview" />
                                    <span id="insuranceError" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="my-3">
                                    <label class="form-label" for="insurance">Insurance Expiry Date</label>
                                    <input type="text" class="form-control" id="expiryDate"
                                        name="insurance_expiry_date" placeholder="Please select expiry date.">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5>Driver Details</h5>
                        <a id="addNewDriver" class="btn btn-sm btn-light-secondary fw-bold"> <i data-feather="plus"></i>
                            Add New
                            Driver</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="driver1Name">Driver Name</label>
                                    <input type="text" class="form-control" id="driver1Name" name="driver_name[]"
                                        placeholder="Enter Driver name" required minlength="3" maxlength="50"
                                        data-parsley-required-message="Please enter driver name."
                                        data-parsley-minlength-message="Minimum 3 characters required."
                                        data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="driver1Number">Driver Mobile</label>
                                    <input type="number" class="form-control" id="driver1Number" name="driver_mobile[]"
                                        placeholder="Enter driver number" required minlength="10" maxlength="10"
                                        data-parsley-required-message="Please enter driver mobile."
                                        data-parsley-pattern-message="Invalid mobile number."
                                        data-parsley-minlength-message="Must be 10 digits."
                                        data-parsley-maxlength-message="Must be 10 digits.">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="bank1Name">Bank Name</label>
                                    <input type="text" class="form-control" id="bank1Name" name="bank_name[]"
                                        placeholder="Enter bank name" minlength="3" maxlength="50"
                                        data-parsley-required-message="Please enter bank name."
                                        data-parsley-minlength-message="Minimum 3 characters required."
                                        data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="branch1Name">Branch Name</label>
                                    <input type="text" class="form-control" id="branch1Name" name="branch_name[]"
                                        placeholder="Enter branch name" minlength="3" maxlength="50"
                                        data-parsley-required-message="Please enter branch name."
                                        data-parsley-minlength-message="Minimum 3 characters required."
                                        data-parsley-maxlength-message="Maximum 50 characters allowed.">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="account1Name">Account Holder Name</label>
                                    <input type="text" class="form-control" id="account1Name"
                                        name="account_holder_name[]" placeholder="Enter account holder name"
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
                                        name="account_number[]" placeholder="Enter account number" pattern="^\d{9,18}$"
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
                                    <input type="text" class="form-control" id="ifsc1Code" name="ifsc_code[]"
                                        placeholder="Enter IFSC code" pattern="^[A-Z]{4}0[A-Z0-9]{6}$" minlength="11"
                                        maxlength="11" data-parsley-required-message="Please enter IFSC code."
                                        data-parsley-pattern-message="Invalid IFSC code format."
                                        data-parsley-minlength-message="Must be exactly 11 characters."
                                        data-parsley-maxlength-message="Must be exactly 11 characters.">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="upi1Number">UPI ID</label>
                                    <input type="text" class="form-control" id="upi1Number" name="upi_id[]"
                                        placeholder="Enter UPI ID" minlength="6" maxlength="50"
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
                                        name="driving_license_number[]" placeholder="Enter driving license number"
                                        minlength="10" maxlength="20" pattern="^[A-Z0-9-]+$"
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
                                        name="aadhar_card_number[]" placeholder="Enter aadhar card number"
                                        pattern="^\d{12}$" minlength="12" maxlength="12"
                                        data-parsley-required-message="Please enter Aadhar card number."
                                        data-parsley-pattern-message="Invalid Aadhar number."
                                        data-parsley-minlength-message="Must be 12 digits."
                                        data-parsley-maxlength-message="Must be 12 digits.">
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="driver1Image">Driver Profile Picture</label>
                                    <input type="file" class="form-control mb-2" id="driver1Image" accept="image/*"
                                        name="driver_profile_picture[]"
                                        data-parsley-required-message="Please upload diver profile picture.">
                                    <img class="imagePreview" id="driver1ImagePreview" />
                                    <span id="driver1ImageError" class="text-danger"></span>
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
                                <input type="file" class="form-control" id="license-1-front" accept="image/*"
                                    name="front_driver_license[]"
                                    data-parsley-required-message="Please upload driving license front image."
                                    data-parsley-errors-container="#drivingLicence-error-container">
                                <div id="drivingLicence-error-container" class="text-danger"></div>
                                <img class="imagePreview" id="license-1-frontPreview" />
                                <span id="license-1-frontError" class="text-danger"></span>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="aadhar-1-back">Back side of Driving
                                    License</label>
                                <input type="file" class="form-control" id="license-1-back" accept="image/*"
                                    name="back_driver_license[]"
                                    data-parsley-required-message="Please upload driving license back image."
                                    data-parsley-errors-container="#drivingLicenceBack-error-container">
                                <div id="drivingLicenceBack-error-container" class="text-danger"></div>
                                <img class="imagePreview" id="license-1-backPreview" />
                                <span id="license-1-backError" class="text-danger"></span>
                            </div>
                            <div class="col-md-6">
                                <div class="my-3">
                                    <label class="form-label" for="aadhar-1-front">Frontside of Aadhar Card</label>
                                    <input type="file" class="form-control" id="aadhar-1-front" accept="image/*"
                                        name="front_aadhar_card[]"
                                        data-parsley-required-message="Please upload aadhar card front image.">
                                    <img class="imagePreview" id="aadhar-1-frontPreview" />
                                    <span id="aadhar-1-frontError" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mt-3 mb-5">
                                    <label class="form-label" for="aadhar-1-back">Backside of Aadhar Card</label>
                                    <input type="file" class="form-control" id="aadhar-1-back" accept="image/*"
                                        name="back_aadhar_card[]"
                                        data-parsley-required-message="Please upload aadhar card back image.">
                                    <img class="imagePreview" id="aadhar-1-backPreview" />
                                    <span id="aadhar-1-backError" class="text-danger"></span>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="assignDriver[]"
                                            id="assignDriver1" checked>
                                        <label class="form-label mb-0" for="assignDriver1">
                                            <h5 class="mb-0">Assign Driver to Cab </h5>
                                        </label>
                                        <br><label id="assignDriver[]-error" class="error" for="assignDriver[]"></label>
                                    </div>
                                </div>
                                <div id="assignDe-error-container" class="text-danger"></div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        required  minlength="10"
                                        maxlength="10" data-parsley-required-message="Please enter driver mobile."
                                        data-parsley-pattern-message="Invalid mobile number."
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
                                         minlength="10" maxlength="20" pattern="^[A-Z0-9-]+$"
                                        data-parsley-required-message="Please enter driving license number."
                                        data-parsley-minlength-message="Driving license number must be at least 10 characters."
                                        data-parsley-maxlength-message="Driving license number cannot exceed 20 characters."
                                        data-parsley-pattern-message="Invalid format. Use only uppercase letters, numbers, and hyphens." >
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
                                            <input class="form-check-input" type="radio" name="assignDriver[]" id="assignDriver-${driverCount}">
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

                // Append the new driver card to the container
                $('#driverDetailsContainer').append(newDriverCard);
            });

            $('#driverDetailsContainer').on('click', '.removeDriver', function() {
                $(this).closest('.card').remove();
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

            $('#driver1Image').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#driver1ImagePreview');
                $('#driver1ImageError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#driver1ImageError').text(
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

            $('#license-1-front').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#license-1-frontPreview');
                $('#license-1-frontError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#license-1-frontError').text(
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

            $('#license-1-back').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#license-1-backPreview');
                $('#license-1-backError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#license-1-backError').text(
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

            $('#aadhar-1-front').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#aadhar-1-frontPreview');
                $('#aadhar-1-frontError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadhar-1-frontError').text(
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
                const imagePreview = $('#aadhar-1-backPreview');
                $('#aadhar-1-backError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadhar-1-backError').text(
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
                const imagePreview = $('#aadhar-1-backPreview');
                $('#aadhar-1-backError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#aadhar-1-backError').text(
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
