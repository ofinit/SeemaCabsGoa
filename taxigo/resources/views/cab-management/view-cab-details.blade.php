<div class="row gy-1">
    <div class="col-12">
        <h5>Cab Details</h5>
    </div>
    {{-- @if (Auth::user()->type == App\Enums\Type::ADMIN)
        <div class="col-lg-4 col-md-6">
            <h6 class="mb-1">Fleeter Operator</h6>
            <p class="cab-model-location">
                {{ $cab->getFleetOperatorDetails ? $cab->getFleetOperatorDetails->name : '' }}</p>
        </div>
    @endif --}}
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">Cab Zone</h6>
        <p class="cab-model-location">{{ $cab->zone_names ?? '' }}</p>
    </div>
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">Cab Number</h6>
        <p class="cab-model-location">{{ $cab->number ?? '' }}</p>
    </div>
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">Cab Type</h6>
        <p class="cab-model-location">{{ $cab->type ? getCabType($cab->type) : '' }}</p>
    </div>
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">Cab Model</h6>
        <p class="cab-model-location">{{ $cab->model ? getModal($cab->model) : '' }}</p>
    </div>
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">Model Name</h6>
        <p class="cab-model-location">{{ $cab->getCabModelDetails ? $cab->getCabModelDetails->name : '' }}</p>
    </div>
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">Color</h6>
        <p class="cab-model-location">{{ $cab->getColorDetails ? $cab->getColorDetails->name : '' }}</p>
    </div>
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">No. of Seats</h6>
        <p class="cab-model-location">{{ $cab->no_of_seats ?? '' }}</p>
    </div>
    <div class="col-lg-4 col-md-6">
        <h6 class="mb-1">Fuel Type</h6>
        <p class="cab-model-location">{{ $cab->fuel_type ? getFuelType($cab->fuel_type) : '' }}</p>
    </div>
    @if ($cab->front_registration_certificate || $cab->back_registration_certificate)
        <div class="col-md-6">
            <h6 class="mb-1">Registration Certificate</h6>
            <div class="document-container">
                @if ($cab->front_registration_certificate)
                    <div class="main-photo img-box">
                        <a href="{{ $cab->front_registration_image }}" class="glightbox"
                            data-title="Registration Certificate(Front)"
                            data-description='<div><a href="{{ $cab->front_registration_image }}" download class="btn btn-primary download-btn">Download</a></div>'>
                            <img src="{{ $cab->front_registration_image }}" alt="image" />
                        </a>
                    </div>
                @endif
                @if ($cab->back_registration_certificate)
                    <div class="main-photo img-box">
                        <a href="{{ $cab->back_registration_image }}" class="glightbox"
                            data-title="Registration Certificate(Back)"
                            data-description='<div><a href="{{ $cab->back_registration_image }}" download class="btn btn-primary download-btn">Download</a></div>'>
                            <img src="{{ $cab->back_registration_image }}" alt="image" />
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
    @if ($cab->insurance)
        <div class="col-md-6">
            <h6 class="mb-1">Insurance</h6>
            <div class="document-container">
                <div class="main-photo img-box">
                    <a href="{{ $cab->insurance_image }}" class="glightbox" data-title="Insurance"
                        data-description='<div><a href="{{ $cab->insurance_image }}" download class="btn btn-primary download-btn">Download</a></div>'>
                        <img src="{{ $cab->insurance_image }}" alt="image" />
                    </a>
                </div>
            </div>
        </div>
    @endif
    <div class="col-lg-4 col-md-6 mt-4">
        <h6 class="mb-1">Insurance Expiry Date</h6>
        <p class="cab-model-location">{{ $cab->insurance_expiry_date ?? '--' }}</p>
    </div>

    <div class="col-12">
        <h5 class="my-3">Driver Details</h5>
    </div>
    @if (isset($cab->getDriverDetails) && count($cab->getDriverDetails) > 0)
        @foreach ($cab->getDriverDetails as $list)
            <div class="driver-1 mb-5">
                <div class="row">
                    @if (isset($list->assignDriver) && $list->assignDriver == App\Enums\Type::DRIVERASSIGNSTATUS)
                        <div class="col-12 mt-3">
                            <div class="alert alert-success">Driver <strong>{{ $list->name ?? '' }}</strong> Assigned
                                to
                                Cab
                            </div>
                        </div>
                    @endif

                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Driver Name</h6>
                        <p class="cab-model-location">{{ $list->name ?? '' }}</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Driver Mobile</h6>
                        <p class="cab-model-location">{{ $list->mobile ?? '' }}</p>
                    </div>
                    @if ($list->bank_name)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Bank Name</h6>
                            <p class="cab-model-location">{{ $list->bank_name ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->branch_name)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Branch Name</h6>
                            <p class="cab-model-location">{{ $list->branch_name ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->account_holder_name)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Account Holder Name</h6>
                            <p class="cab-model-location">{{ $list->account_holder_name ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->account_number)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Account Number</h6>
                            <p class="cab-model-location">{{ $list->account_number ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->ifsc_code)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">IFSC Code</h6>
                            <p class="cab-model-location">{{ $list->ifsc_code ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->upi_id)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">UPI ID</h6>
                            <p class="cab-model-location">{{ $list->upi_id ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->driving_license_number)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Driving License Number</h6>
                            <p class="cab-model-location">{{ $list->driving_license_number ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->aadhar_card_number)
                        <div class="col-12">
                            <h6 class="mb-1">Aadhar Card Number</h6>
                            <p class="cab-model-location">{{ $list->aadhar_card_number ?? '' }}</p>
                        </div>
                    @endif
                    @if ($list->profile_picture)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Driver Profile Image</h6>
                            <div class="main-photo img-box">
                                <a href="{{ asset('/storage/profile/') . '/' . $list->profile_picture }}"
                                    class="glightbox">
                                    <img src="{{ asset('/storage/profile/') . '/' . $list->profile_picture }}"
                                        alt="image" />
                                </a>
                            </div>
                        </div>
                    @endif

                    @if ($list->front_license || $list->back_license)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Driving License</h6>
                            <div class="document-container">
                                @if ($list->front_license)
                                    <div class="main-photo img-box">
                                        <a href="{{ asset('/storage/license/') . '/' . $list->front_license }}"
                                            class="glightbox" data-title="Driving License(Front)"
                                            data-description='<div><a href="{{ asset('/storage/license/') . '/' . $list->front_license }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                            <img src="{{ asset('/storage/license/') . '/' . $list->front_license }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                @endif
                                @if ($list->back_license)
                                    <div class="main-photo img-box">
                                        <a href="{{ asset('/storage/license/') . '/' . $list->back_license }}"
                                            class="glightbox" data-title="Driving License(Back)"
                                            data-description='<div><a href="{{ asset('/storage/license/') . '/' . $list->back_license }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                            <img src="{{ asset('/storage/license/') . '/' . $list->back_license }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if ($list->front_aadhar_card || $list->back_aadhar_card)
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Aadhar Card</h6>
                            <div class="document-container">
                                @if ($list->front_aadhar_card)
                                    <div class="main-photo img-box">
                                        <a href="{{ asset('/storage/aadhar/') . '/' . $list->front_aadhar_card }}"
                                            class="glightbox" data-title="Aadhar Card(Front)"
                                            data-description='<div><a href="{{ asset('/storage/aadhar/') . '/' . $list->front_aadhar_card }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                            <img src="{{ asset('/storage/aadhar/') . '/' . $list->front_aadhar_card }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                @endif
                                @if ($list->back_aadhar_card)
                                    <div class="main-photo img-box">
                                        <a href="{{ asset('/storage/aadhar/') . '/' . $list->back_aadhar_card }}"
                                            class="glightbox" data-title="Aadhar Card(Back)"
                                            data-description='<div><a href="{{ asset('/storage/aadhar/') . '/' . $list->back_aadhar_card }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                            <img src="{{ asset('/storage/aadhar/') . '/' . $list->back_aadhar_card }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
