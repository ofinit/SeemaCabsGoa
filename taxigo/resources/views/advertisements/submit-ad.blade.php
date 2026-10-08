@extends('layouts.main')

@section('title', 'Submit Ads')

@section('css')
    <style>
        .imagePreview {
            display: none;
            width: 200px;
            height: 200px;
            object-fit: contain;
            border-radius: 12px;
        }

        .LogoImage {
            width: 200px;
            height: 200px;
        }

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

        #enterStartTime::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        /* Optional cleanup styling */
        #enterStartTime {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 0;
        }

        #enterEndTime::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        /* Optional cleanup styling */
        #enterEndTime {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 0;
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
                        <li class="breadcrumb-item">Advertisements</li>
                        <li class="breadcrumb-item" aria-current="page">Submit Ads</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Submit Ads</h2>
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
                    <h5>Upload Ads</h5>
                </div>
                <form class="submitAddsFrom" method="POST" action="{{ route('admin.advertisements.storeUpdate') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="hidden" id="" name="id" value="{{ @$advertisement->id ?? '' }}">
                            <label class="form-label" for="boxAdvertisers">Select Advertiser / Customer</label>
                            <select data-trigger class="form-select" name="customer_id" id="boxAdvertisers" required
                                data-parsley-required-message="Please select customer."
                                data-parsley-errors-container="#customer-error-container">
                                <option value="">Select Advertiser / Customer</option>
                                @if (isset($customers) && count($customers) > 0)
                                    @foreach ($customers as $list)
                                        <option value="{{ $list->id }}" {{ @$advertisement->customer_id == $list->id ? 'selected' : '' }}>
                                            {{ $list->name ?? '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <span id="customer-error-container" class="text-danger"></span>
                        </div>

                        <hr class="my-2">

                        <div class="mb-3">
                            <label class="form-label" for="boxAdvertisers">Select Ad Screen</label>
                            <div class="col-12 d-flex flex-column">
                                @php
                                    $screen = @$advertisement->screens
                                        ? json_decode(@$advertisement->screens, true)
                                        : [];
                                @endphp
                              @if(!empty($advertisementsScreens))
                                @foreach ($advertisementsScreens as $key => $adScreen)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" name="screens[]" type="checkbox" value="{{ $adScreen->id }}"
                                            onchange="calculatePrice(this)" {{ in_array($adScreen->id, $screen) ? 'checked' : '' }}
                                            id="homeScreen-{{$key}}" required
                                            data-parsley-required-message="Please select at least one screen."
                                            data-parsley-errors-container="#screen-error-container">
                                        <label class="form-check-label" for="homeScreen-{{$key}}">{{ $adScreen->title }}

                                        </label>
                                    </div>
                                @endforeach
                                @endif
                            </div>
                            <span id="screen-error-container" class="text-danger"></span>
                        </div>

                        <hr class="my-2">

                        <div class="mb-3">
                            <label class="form-label mb-2">Upload Banner Image</label>
                            <label class="card-upload-box" for="mailLogoImage">
                                <img class="imagePreview" id="bannerImageImagePreview" />
                                @if (@$advertisement->banner_image)
                                    <img class="LogoImage" id="" src="{{ @$advertisement->add_banner_image ?? '' }}" />
                                @endif
                                <p class="logo-upload-msg">Upload Banner Image</p>
                                <span class="logo-upload-msg">Please upload a banner image with dimensions 200×200
                                    pixels.</span>
                                <span class="bannerImageError text-danger"></span>
                            </label>
                            <input type="file" class="d-none mailLogoImage bannerImage" name="banner_image"
                                id="mailLogoImage" accept="image/*" {{ @$advertisement->banner_image ? '' : 'required' }}
                                data-parsley-required-message="Please choose banner image."
                                data-parsley-errors-container="#bannerImage-error-container">
                            <span id="bannerImage-error-container" class="text-danger"></span>

                        </div>
                        <div class="mb-3">
                            <label class="form-label mb-2" for="adsUrl">Banner URL / WhatsApp Link / Telegram Link /
                                Social
                                Media Link</label>
                            <input class="form-control" name="banner_url" type="url" name="adsUrl" id="adsUrl"
                                placeholder="Enter your url" required data-parsley-required-message="Please enter url."
                                value="{{ @$advertisement->banner_url ?? old('banner_url') }}">
                        </div>

                        <hr class="my-5">

                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Audience</h5>
                                <p>Define who you want to see your ads.</p>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="selectGender">Select Gender</label>
                                    <select data-trigger class="form-select" name="gender" id=""
                                        onchange="checkGender(this)" required
                                        data-parsley-required-message="Please select gender."
                                        data-parsley-errors-container="#gender-error-container">
                                        <option value="">Select Gender</option>
                                        <option value="all" {{ @$advertisement ? (@$advertisement->gender == App\Enums\Type::AllValue ? 'selected' : '') : 'selected' }}>
                                            All</option>
                                        <option value="0" {{ @$advertisement ? (@$advertisement->gender == 0 ? 'selected' : '') : '' }}>
                                            Female
                                        </option>
                                        <option value="1" {{ @$advertisement->gender == 1 ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="2" {{ @$advertisement->gender == 2 ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                    <span id="gender-error-container" class="text-danger"></span>
                                    <h6 class="mt-2 text-primary">Reach up to <span
                                            class="totalUserGenderCount">{{ @$genderCount ?? count($customers) }}</span>
                                        Users</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="selectCountry">Select Country</label>
                                    <select class="form-select selectCountry" name="country_id" id=""
                                        onchange="getState(this)" data-parsley-error-message="Please select a country.">
                                        <option value="">Select country</option>
                                        <option value="all" {{ @$advertisement ? (@$advertisement->country_id == App\Enums\Type::AllValue ? 'selected' : '') : 'selected' }}>
                                            All</option>
                                        @if (isset($country) && count($country) > 0)
                                            @foreach ($country as $list)
                                                <option value="{{ $list->id ?? '' }}" {{ @$advertisement->country_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <h6 class="mt-2 text-primary">Reach up to <span
                                            class="totalUserCountryCount">{{ @$countryCount ?? count($customers) }}</span>
                                        Users</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="selectState">Select State</label>
                                    <select data-trigger class="form-select selectState" name="state_id" id=""
                                        onchange="getCity(this)" data-parsley-error-message="Please select a state.">
                                        <option value="">Select State</option>
                                        <option value="all" {{ @$advertisement ? (@$advertisement->state_id == App\Enums\Type::AllValue ? 'selected' : '') : 'selected' }}>
                                            All</option>
                                        @if (isset($states) && count($states) > 0)
                                            @foreach ($states as $list)
                                                <option value="{{ $list->id ?? '' }}" {{ @$advertisement->state_id == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <h6 class="mt-2 text-primary">Reach up to <span
                                            class="totalUserStateCount">{{ @$stateCount ?? count($customers) }}</span>
                                        Users</h6>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="selectLiveLocation">Select Live Location</label>
                                    <select data-trigger class="form-select selectLiveLocation " name="location"
                                        id="selectLiveLocation" onchange="checkLocation(this)"
                                        data-parsley-error-message="Please select a live location.">
                                        <option value="">Select Live Location</option>
                                        <option value="all" {{ @$advertisement ? (@$advertisement->location == App\Enums\Type::AllValue ? 'selected' : '') : 'selected' }}>
                                            All</option>
                                        @if (isset($location) && count($location) > 0)
                                            @foreach ($location as $list)
                                                <option value="{{ $list->id ?? '' }}" {{ @$advertisement->location == $list->id ? 'selected' : '' }}>
                                                    {{ $list->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <h6 class="mt-2 text-primary"><span
                                            class="totalUserLocationCount">{{ @$locationCount ?? count($customers) }}</span>
                                        Users have
                                        enabled on
                                        live location</h6>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Budget & Schedule</h5>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="enterStartDate">Start Date</label>
                                    <input type="text" class="form-control" name="start_date" id="enterStartDate"
                                        placeholder="Select date" required
                                        data-parsley-error-message="Please select start date."
                                        value="{{ @$advertisement->start_date ?? old('start_date') }}">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="enterStartTime">Time</label>
                                    <div class="input-group timepicker">
                                        <input class="form-control" name="start_time" id="enterStartTime"
                                            placeholder="Select time" type="time" required
                                            data-parsley-error-message="Please select start time."
                                            data-parsley-errors-container="#startTime-error-container"
                                            value="{{ @$advertisement->start_time ?? old('start_time') }}">
                                        <span class="input-group-text openTimePicker">
                                            <i class="feather icon-clock"></i>
                                        </span>
                                    </div>
                                    <span id="startTime-error-container" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="enterEndDate">End Date</label>
                                    <input type="text" class="form-control" name="end_date" id="enterEndDate"
                                        placeholder="Select date" required
                                        data-parsley-error-message="Please select valid end date."
                                        value="{{ @$advertisement->end_date ?? old('end_date') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="enterEndTime">Time</label>
                                    <div class="input-group timepicker">
                                        <input class="form-control" id="enterEndTime" name="end_time"
                                            placeholder="Select time" type="time" required
                                            data-parsley-error-message="Please select end time."
                                            data-parsley-errors-container="#endTime-error-container"
                                            value="{{ @$advertisement->end_time ?? old('end_time') }}">
                                        <span class="input-group-text openEndTimePicker">
                                            <i class="feather icon-clock"></i>
                                        </span>
                                    </div>
                                    <span id="endTime-error-container" class="text-danger"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input checkBillingDetails" type="checkbox" value="Billing Details"
                                    id="billingDetails">
                                <label class="form-check-label" for="billingDetails">Enter Billing Details</label>
                            </div>
                        </div>

                        <div class="row mb-3 d-none billingDetailsHtml">
                            <div class="col-12">
                                <h5>Billing Details</h5>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="enterCompanyName">Company Name</label>
                                    <input class="form-control" name="billing_company_name" id="enterCompanyName"
                                        placeholder="Enter comapny name" type="text"
                                        data-parsley-error-message="Please enter company name."
                                        value="{{ @$advertisement->billing_company_name ?? old('billing_company_name') }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="enterGst">GST <span>(Optional)</span></label>
                                    <input class="form-control" name="billing_gst" id="enterGst"
                                        placeholder="Enter gst number" type="text"
                                        value="{{ @$advertisement->billing_gst ?? old('billing_gst') }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="enterPan">PAN</label>
                                    <input class="form-control" name="billing_pan" id="enterPan"
                                        placeholder="Enter pan number" type="text"
                                        data-parsley-error-message="Please enter pan number."
                                        pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$"
                                        data-parsley-pattern-message="Please enter a valid PAN number."
                                        value="{{ @$advertisement->billing_pan ?? old('billing_pan') }}">
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <div class="col-12">
                                <h5>Payment Method</h5>
                            </div>
                            <div class="col-lg-4 col-md-6 d-flex gap-3">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" value="1" {{
                                        @$advertisement->payment_method == 1 ? 'checked' : '' }} id="adPaymentOnline">
                                    <label class="form-check-label" for="adPaymentOnline"> Online Payment</label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" value="2"
                                        id="adPaymentCash" {{ @$advertisement ? (@$advertisement->payment_method == 2 ?
                                    'checked' : '') : 'checked' }}>
                                    <label class="form-check-label" for="adPaymentCash"> Cash Payment</label>
                                </div>
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Default</h5>
                            </div>
                            <div class="col-lg-4 col-md-6 d-flex gap-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" name="default" type="checkbox" value="1"
                                        id="billingDetails" {{ @$advertisement->default == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="billingDetails">Mark as default</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 mt-5">
                                <h5>Total Amount</h5>
                                <input type="hidden" class="totalAmountInput" name="total_amount" id=""
                                    value="{{ @$advertisement->total_amount ?? 0 }}">
                                <h1>{{ getCurrencySign() }}<span
                                        class="totalAmount">{{ @$advertisement->total_amount ?? 0 }}</span></h1>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary">Submit Ad</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>View Ad Details</h5>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="addsTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Comapny Name</th>
                                    <th>Customer Name</th>
                                    <th>GST</th>
                                    <th>PAN</th>
                                    <th>Ad Image</th>
                                    <th>Banner URL</th>
                                    {{-- <th>Budget</th> --}}
                                    <th>Total Amount</th>
                                    <th>Start Date & Time</th>
                                    <th>End Date & Time</th>
                                    <th>Gender</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>Live Location</th>
                                    <th>No. of Clicks</th>
                                    {{-- <th>No. of Leads</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Model -->
    <div class="modal fade" id="advertiserDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header justify-content-between">
                    <h4 class="modal-title" id="exampleModalLabel">View Advertiser </h4>
                    <div class="d-flex align-items-center gap-2">
                        <a id="exportLink" class="exportLink" href="#">
                            <button class="btn btn-sm btn-light-info">Export</button>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <span class="mbody"></span>
                </div>
                <div class="modal-footer">
                    <!-- Type Here -- Model Footer -->
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function () {


            $('.submitAddsFrom').parsley();

            $('.openTimePicker').on('click', function (e) {
                e.preventDefault();
                const timeInput = document.getElementById('enterStartTime');
                if (timeInput.showPicker) {
                    timeInput.showPicker();
                } else {
                    timeInput.focus();
                }
            });

            $('.openEndTimePicker').on('click', function (e) {
                e.preventDefault();
                const timeInput = document.getElementById('enterEndTime');
                if (timeInput.showPicker) {
                    timeInput.showPicker();
                } else {
                    timeInput.focus();
                }
            });


            $('#addsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.advertisements.list') }}",
                    type: "POST",
                    data: function (data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [{
                    data: 'billing_company_name',
                    name: 'billing_company_name',
                    render: function (data, type, row) {
                        return row.billing_company_name ?? '--';
                    }
                },
                {
                    data: 'customer_id',
                    name: 'customer_id',
                    render: function (data, type, row) {
                        return row.user.name ?? '--';
                    }
                },
                {
                    data: 'billing_gst',
                    name: 'billing_gst',
                    render: function (data, type, row) {
                        return row.billing_gst;
                    }
                },
                {
                    data: 'billing_pan',
                    name: 'billing_pan',
                    render: function (data, type, row) {
                        return row.billing_pan;
                    }
                },
                {
                    data: 'banner_image',
                    name: 'banner_image',
                    render: function (data, type, row) {
                        var image = '{{ asset('storage/banner') }}' + '/' + row
                            .banner_image;
                        var imageHtml = `<div class="ad-photo">
                                                <a href="${image}"
                                                    class="glightbox" data-glightbox="type: image">
                                                    <img src="${image}"
                                                        alt="image" />
                                                </a>
                                            </div>`;
                        return imageHtml;
                    }
                },

                {
                    data: 'banner_url',
                    name: 'banner_url',
                    render: function (data, type, row) {
                        return row.banner_url;
                    }
                },

                {
                    data: 'default',
                    name: 'default',
                    render: function (data, type, row) {
                        return row.total_amount ?? '--';
                    }
                },
                {
                    data: 'default',
                    name: 'default',
                    render: function (data, type, row) {
                        var date = row.start_date ?? '--';
                        var time = row.start_time ?? '--';

                        if (date !== '--' && time !== '--') {
                            // Convert DD-MM-YYYY to YYYY-MM-DD
                            var [day, month, year] = date.split('-');
                            var isoDate = `${year}-${month}-${day}`;
                            var dateTime = new Date(`${isoDate}T${time}`);

                            // Format date as DD-MM-YYYY
                            var formattedDate =
                                `${day.padStart(2, '0')}-${month.padStart(2, '0')}-${year}`;

                            // Format time as hh:mm am/pm
                            var formattedTime = dateTime.toLocaleString('en-IN', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }).toLowerCase();

                            return `${formattedDate} <span class="fw-bolder text-danger">${formattedTime}</span>`;
                        } else {
                            return date + ' ' + time;
                        }

                    }
                },
                {
                    data: 'default',
                    name: 'default',
                    render: function (data, type, row) {
                        var date = row.end_date ?? '--';
                        var time = row.end_time ?? '--';

                        if (date !== '--' && time !== '--') {
                            // Convert DD-MM-YYYY to YYYY-MM-DD
                            var [day, month, year] = date.split('-');
                            var isoDate = `${year}-${month}-${day}`;
                            var dateTime = new Date(`${isoDate}T${time}`);

                            // Format date as DD-MM-YYYY
                            var formattedDate =
                                `${day.padStart(2, '0')}-${month.padStart(2, '0')}-${year}`;

                            // Format time as hh:mm am/pm
                            var formattedTime = dateTime.toLocaleString('en-IN', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }).toLowerCase();

                            return `${formattedDate} <span class="fw-bolder text-danger">${formattedTime}</span>`;
                        } else {
                            return date + ' ' + time;
                        }

                    }
                },
                {
                    data: 'gender',
                    name: 'gender',
                    render: function (data, type, row) {
                        if (row.gender == 10001) {
                            return 'All';
                        }
                        var gender = 'Male';
                        if (row.user.gender == 0) {
                            gender = 'Female';
                        } else if (row.user.gender == 2) {
                            gender = 'Other';
                        }
                        return gender;
                    }
                },
                {
                    data: 'default',
                    name: 'default',
                    render: function (data, type, row) {
                        if (row.country_id == 10001) {
                            return 'All';
                        } else {
                            return row.country ? row.country.name : '--';
                        }
                    }
                },
                {
                    data: 'default',
                    name: 'default',
                    render: function (data, type, row) {
                        if (row.state_id == 10001) {
                            return 'All';
                        } else {
                            return row.state ? row.state.name : '--';
                        }
                    }
                },
                {
                    data: 'default',
                    name: 'default',
                    render: function (data, type, row) {
                        if (row.location == 10001) {
                            return 'All';
                        } else {
                            return row.location ? row.location.name : '--';
                        }
                    }
                },
                {
                    data: 'default',
                    name: 'default',
                    render: function (data, type, row) {
                        return row.total_clicks ?? '--';
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (data, type, row) {
                        var editUrl = '{{ route('admin.advertisements.edit', ':id') }}'
                            .replace(':id',
                                row
                                    .id);
                        var viewUrl = '{{ route('admin.cabs.view', ':id') }}'.replace(':id',
                            row
                                .id);
                        var deletea = '{{ route('admin.advertisements.delete', ':id') }}'
                            .replace(':id',
                                row.id);
                        var html = `
                                            <a href="${editUrl}"
                                                class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-danger sa-bs-suspend-ico deleteAdds" data-url="${deletea}"><i
                                                    class="feather icon-trash-2"></i></a>`;
                        // var html = `  <a href="#!" class="btn btn-sm btn-light-primary me-1 getCabDetails advertiserDetails" data-url="${viewUrl}" data-id="${row.id}" data-bs-toggle="modal"
                        //                 data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                        //             <a href="${editUrl}"
                        //                 class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                        //             <a href="#!" class="btn btn-sm btn-light-danger sa-bs-suspend-ico deleteAdds" data-url="${deletea}"><i
                        //                     class="feather icon-trash-2"></i></a>`;
                        return html;
                    }
                }
                ],
                drawCallback: function () {
                    GLightbox({
                        touchNavigation: true,
                        loop: true,
                        width: "90vw",
                        height: "90vh"
                    });
                }
            });
        });

        function getState(e) {
            var countryId = e.value;
            $('.selectLiveLocation').prop('selectedIndex', 0);
            if (countryId) {
                var url = '{{ route('admin.getStates', ':id') }}'.replace(':id', countryId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('.selectState').removeAttr('id');
                        // Clear the existing options
                        $('.selectState').empty().append(
                            '<option value="">Select State</option><option value="all">All</option>'
                        );
                        $.each(data, function (key, state) {
                            $('.selectState').append('<option value="' + state.id + '">' + state.name +
                                '</option>');
                        });

                        $('.selectState').attr('id', 'selectState');
                        getCustomerCount('country', countryId);
                    },
                });
            } else {
                $('.selectState').empty().append('<option value="">Select State</option>');
            }
        }

        function checkLocation(e) {
            var value = e.value;
            $('.selectState').prop('selectedIndex', 0);
            $('.selectCountry').prop('selectedIndex', 0);
            if (value !== "" && value !== "all") {
                $('.selectCountry').removeAttr('required');
                $('.selectState').removeAttr('required');
            } else {
                $('.selectCountry').attr('required', 'true');
                $('.selectState').attr('required', 'true');
            }
            getCustomerCount('location', value);
        }

        function checkGender(e) {
            var value = e.value;
            getCustomerCount('gender', value);
        }

        function getCustomerCount(type, value) {
            var url = "{{ route('admin.advertisements.getTotalCustomerCount') }}";
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    type: type,
                    value: value
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        if (type == 'gender') {
                            $('.totalUserGenderCount').html(res.count);
                        }
                        if (type == 'country') {
                            $('.totalUserCountryCount').html(res.count);
                        }
                        if (type == 'state') {

                            $('.totalUserStateCount').html(res.count);
                        }
                        if (type == 'location') {
                            $('.totalUserLocationCount').html(res.count);
                        }
                    }
                },
            });
        }

        function calculatePrice(e) {
            var id = e.value;
            var totalAmount = parseInt($('.totalAmountInput').val());
            var url = "{{ route('admin.advertisements.getScreenAmount') }}";
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status) {
                        var amount = parseFloat(res.amount)
                        if (e.checked) {
                            totalAmount += amount;
                        } else {
                            totalAmount -= amount;
                        }
                        $('.totalAmountInput').val(totalAmount.toFixed(2));
                        $('.totalAmount').html(totalAmount.toFixed(2));
                    }
                },
            });
        }

        function getCity(e) {
            var stateId = e.value;
            getCustomerCount('state', stateId);
            // if (stateId) {
            //     var url = '{{ route('admin.getCity', ':id') }}'.replace(':id', stateId);
            //     $.ajax({
            //         url: url,
            //         type: 'GET',
            //         dataType: 'json',
            //         success: function(data) {
            //             $('#city').empty().append(
            //                 '<option value="">Select City</option><option value="all">All</option>');

            //             $.each(data, function(key, city) {
            //                 $('#city').append('<option value="' + city.id + '">' + city.name +
            //                     '</option>');
            //             });
            //         },
            //     });
            // } else {
            //     $('#city').empty().append('<option value="">Select City</option>');
            // }
        }

        $(function () {
            // preview driver app logo
            $('.bannerImage').on('change', function (event) {
                const file = event.target.files[0];
                const imagePreview = $('#bannerImageImagePreview');
                $('.bannerImageError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.bannerImageError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });
        });
        // Data Table
        var table1 = $('#boxAdDetails').DataTable();
        var table2 = $('#leader-1AdDetails').DataTable();
        var table3 = $('#leader-2AdDetails').DataTable();
        var table4 = $('#adViewDetails').DataTable();

        // GLightBox
        const lightbox = GLightbox({
            touchNavigation: true,
            loop: true,
            width: "90vw",
            height: "90vh"
        });

        // Delete Button Sweet Alert
        document.querySelectorAll('.sa-bs-error-ico').forEach(function (element) {
            element.addEventListener('click', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Are You Sure!',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                })
            });
        });

        // Select with Search
        // document.addEventListener('DOMContentLoaded', function() {
        //     var element1 = document.querySelector('#boxAdvertisers');
        //     var element2 = document.querySelector('#selectGender');
        //     var element3 = document.querySelector('#selectCountry');
        //     var element4 = document.querySelector('#selectState');
        //     var element5 = document.querySelector('#selectLiveLocation');

        //     new Choices(element1, {
        //         searchPlaceholderValue: 'Search Advertiser / Customer'
        //     });

        //     new Choices(element2, {
        //         searchPlaceholderValue: 'Search Gender'
        //     });

        //     new Choices(element3, {
        //         searchPlaceholderValue: 'Search Country'
        //     });

        //     new Choices(element4, {
        //         searchPlaceholderValue: 'Search State'
        //     });

        //     new Choices(element5, {
        //         searchPlaceholderValue: 'Search Live Location'
        //     });
        // })

        // Select Date
        const boxAdStartDate = new Datepicker(document.querySelector('#enterStartDate'), {
            buttonClass: 'btn',
            format: 'dd-mm-yyyy',
            autohide: true
        });

        const boxAdEndDate = new Datepicker(document.querySelector('#enterEndDate'), {
            buttonClass: 'btn',
            format: 'dd-mm-yyyy',
            autohide: true
        });
        document.querySelector('#enterStartDate').addEventListener('changeDate', function (e) {
            const selectedStartDate = e.detail.date;
            boxAdEndDate.setOptions({
                minDate: selectedStartDate
            });
        });
    </script>

    <script>
        (function () {
            $('.checkBillingDetails').on('click', function () {
                if ($(this).is(':checked')) {
                    $('.billingDetailsHtml').removeClass('d-none');
                    // Add "required" for fields
                    $('#enterCompanyName').attr('required', 'required');
                    $('#enterPan').attr('required', 'required');
                } else {
                    $('.billingDetailsHtml').addClass('d-none');
                    // Remove "required" for fields
                    $('#enterCompanyName').removeAttr('required');
                    $('#enterPan').removeAttr('required');

                    // Optionally reset validation errors
                    $('#enterCompanyName').parsley().reset();
                    $('#enterPan').parsley().reset();
                }
            });

            $(document).on('click', '.advertiserDetails', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = "{{ route('admin.advertisements.getAdvertiserDetails') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {
                        id: id
                    },
                    success: function (res) {
                        if (res.status) {
                            var exportUrl =
                                "{{ route('admin.advertisements.exportAdvertiserList') }}?id=" +
                                id;
                            $('.exportLink').attr('href', exportUrl);
                            $('.mbody').html(res.html);
                        }

                    }
                });
            });
            // delete
            $(document).on('click', '.deleteAdds', function (e) {
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
                            success: function (response) {
                                Swal.fire("Success!",
                                    "Adds has been deleted.", "success");
                                location.reload();
                            },
                            error: function () {
                                Swal.fire("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });
        })();
    </script>
    <script>
        $(function () {
            // Register custom validator for end date >= start date
            window.Parsley.addValidator('enddateafterstart', {
                requirementType: 'string',
                validateString: function (endDateValue, startDateSelector) {
                    if (!endDateValue) return true; // Let 'required' handle empty field
                    const startDateValue = $(startDateSelector).val();
                    if (!startDateValue) return true; // Skip if start date not filled yet

                    const endDate = new Date(endDateValue);
                    const startDate = new Date(startDateValue);

                    return endDate >= startDate;
                }
            });

            // Trigger validation when start date changes
            $('#enterStartDate').on('change', function () {
                $('#enterEndDate').parsley().validate();
            });
        });
    </script>

    <!-- [Page Specific JS] end -->
@endsection
