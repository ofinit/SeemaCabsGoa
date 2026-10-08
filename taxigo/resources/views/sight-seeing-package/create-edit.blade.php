@extends('layouts.main')

@section('title', (isset($sightSeeingPackages) ? 'Edit' : 'Add') . ' Sightseeing Packages')

@section('css')
    <!-- Toastr CSS -->
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" />
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
        /* ---- Modern Image Gallery ---- */
        .modern-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 16px;
        }

        .modern-gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1 / 1;
            background: #f5f6f8;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .modern-gallery-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
        }

        .modern-gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .modern-gallery-item:hover .modern-gallery-img {
            transform: scale(1.05);
        }

        /* ---- Overlay ---- */
        .modern-overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 15, 15, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
            gap: 10px;
        }

        .modern-gallery-item:hover .modern-overlay {
            opacity: 1;
        }

        /* ---- Buttons ---- */
        .modern-btn {
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
            cursor: pointer;
        }

        .modern-btn:hover {
            background: #2563eb;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
            color: #fff;
        }

        /* ---- Responsiveness ---- */
        @media (max-width: 576px) {
            .modern-gallery {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: 10px;
            }
            .modern-btn {
                width: 32px;
                height: 32px;
            }
        }
    </style>
    <!-- DataTables CSS -->
@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Sight Seeing Packages</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">{{ isset($sightSeeingPackages) ? 'Edit' : 'Add' }} Sightseeing Packages</h2>
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
                <div class="card-body">
                    <form action="javascript:void(0)" id="sightseeing-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sight_seeing_id" value="{{ isset($sightSeeingPackages) ? $sightSeeingPackages->id : '' }}">
                        <div class="row">
                            <div class="col-lg-12 col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ isset($sightSeeingPackages) ? $sightSeeingPackages->title : '' }}" placeholder="Enter title"
                                        data-parsley-required-message="Please enter title." required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label mb-2">Image</label>
                                    <label class="card-upload-box" for="mailLogoImage">
                                        <div id="bannerImagePreviewContainer" class="d-flex flex-wrap gap-2"></div>
                                        <p class="logo-upload-msg">
                                            <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.75 18.8125C13.09 18.8125 10.9375 16.66 10.9375 14C10.9375 11.34 13.09 9.1875 15.75 9.1875C18.41 9.1875 20.5625 11.34 20.5625 14C20.5625 16.66 18.41 18.8125 15.75 18.8125ZM15.75 11.8125C15.1698 11.8125 14.6134 12.043 14.2032 12.4532C13.793 12.8634 13.5625 13.4198 13.5625 14C13.5625 14.5802 13.793 15.1366 14.2032 15.5468C14.6134 15.957 15.1698 16.1875 15.75 16.1875C16.3302 16.1875 16.8866 15.957 17.2968 15.5468C17.707 15.1366 17.9375 14.5802 17.9375 14C17.9375 13.4198 17.707 12.8634 17.2968 12.4532C16.8866 12.043 16.3302 11.8125 15.75 11.8125Z"
                                                    fill="#666666" />
                                                <path
                                                    d="M26.25 39.8125H15.75C6.2475 39.8125 2.1875 35.7525 2.1875 26.25V15.75C2.1875 6.2475 6.2475 2.1875 15.75 2.1875H22.75C23.4675 2.1875 24.0625 2.7825 24.0625 3.5C24.0625 4.2175 23.4675 4.8125 22.75 4.8125H15.75C7.6825 4.8125 4.8125 7.6825 4.8125 15.75V26.25C4.8125 34.3175 7.6825 37.1875 15.75 37.1875H26.25C34.3175 37.1875 37.1875 34.3175 37.1875 26.25V17.5C37.1875 16.7825 37.7825 16.1875 38.5 16.1875C39.2175 16.1875 39.8125 16.7825 39.8125 17.5V26.25C39.8125 35.7525 35.7525 39.8125 26.25 39.8125Z"
                                                    fill="#666666" />
                                                <path
                                                    d="M31.5 15.3119C30.7825 15.3119 30.1875 14.7169 30.1875 13.9994V3.49941C30.1875 2.97441 30.5025 2.48441 30.9925 2.29191C31.4825 2.09941 32.0425 2.20441 32.4275 2.57191L35.9275 6.07191C36.435 6.57941 36.435 7.41941 35.9275 7.92691C35.42 8.43441 34.58 8.43441 34.0725 7.92691L32.8125 6.66691V13.9994C32.8125 14.7169 32.2175 15.3119 31.5 15.3119Z"
                                                    fill="#666666" />
                                                <path
                                                    d="M28 8.31203C27.6675 8.31203 27.335 8.18953 27.0725 7.92703C26.8284 7.68004 26.6916 7.34678 26.6916 6.99953C26.6916 6.65228 26.8284 6.31902 27.0725 6.07203L30.5725 2.57203C31.08 2.06453 31.92 2.06453 32.4275 2.57203C32.935 3.07953 32.935 3.91953 32.4275 4.42703L28.9275 7.92703C28.665 8.18953 28.3325 8.31203 28 8.31203ZM4.67254 34.4745C4.39282 34.4726 4.12101 34.3814 3.89667 34.2144C3.67232 34.0473 3.50714 33.813 3.42515 33.5455C3.34316 33.2781 3.34864 32.9915 3.4408 32.7273C3.53295 32.4632 3.70697 32.2354 3.93754 32.077L12.565 26.2845C14.455 25.0245 17.0625 25.1645 18.7775 26.617L19.355 27.1245C20.23 27.877 21.7175 27.877 22.575 27.1245L29.855 20.877C31.71 19.2845 34.6325 19.2845 36.505 20.877L39.3575 23.327C39.9 23.7995 39.97 24.622 39.4975 25.182C39.025 25.7245 38.185 25.7945 37.6425 25.322L34.79 22.872C33.915 22.1195 32.445 22.1195 31.57 22.872L24.29 29.1195C22.435 30.712 19.5125 30.712 17.64 29.1195L17.0625 28.612C16.2575 27.9295 14.9275 27.8595 14.035 28.472L5.42504 34.2645C5.18004 34.4045 4.91754 34.4745 4.67254 34.4745Z"
                                                    fill="#666666" />
                                            </svg>
                                        </p>
                                        <span class="logo-upload-msg">Drag & Drop</span>
                                        <span class="bannerImageError text-danger"></span>
                                    </label>
                                    <input type="file" class="d-none mailLogoImage bannerImage" name="images[]"
                                        id="mailLogoImage" accept="image/*" multiple {{ isset($sightSeeingPackages->packageImages) && $sightSeeingPackages->packageImages->isNotEmpty() ? '' : 'required'}}
                                        data-parsley-required-message="Please choose banner image."
                                        data-parsley-errors-container="#bannerImage-error-container">
                                    <span id="bannerImage-error-container" class="text-danger"></span>

                                </div>
                            </div>
                        </div>
                        @if (isset($sightSeeingPackages->packageImages) && $sightSeeingPackages->packageImages->isNotEmpty())
                            <div class="modern-gallery mt-3 mb-3">
                                @foreach ($sightSeeingPackages->packageImages as $image)
                                    <div class="modern-gallery-item" data-img-id="{{ $image->id }}" data-image-name="{{ $image->image }}">
                                        <img src="{{ getFileUrl($image->image) }}" alt="Package Image" class="modern-gallery-img" />

                                        <div class="modern-overlay">
                                            <button type="button" class="modern-btn preview-btn"
                                                data-image="{{ getFileUrl($image->image) }}" title="View">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                            <button type="button" class="modern-btn delete-btn delete-image-btn"
                                                data-img-id="{{ $image->id }}" data-image-name="{{ $image->image }}" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label" for="enterStartTime">Start Time</label>
                                    <div class="input-group timepicker">
                                        <span class="input-group-text time-picker">
                                            <i class="feather icon-clock"></i>
                                        </span>
                                        <input class="form-control openTimePicker" name="start_time" id="enterStartTime"
                                            placeholder="Select time" type="time" required
                                            data-parsley-error-message="Please select start time."
                                            data-parsley-errors-container="#startTime-error-container" value="{{ isset($sightSeeingPackages) ? $sightSeeingPackages->start_time : '' }}">
                                    </div>
                                    <span id="startTime-error-container" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label" for="enterEndTime">End Time</label>
                                    <div class="input-group timepicker">
                                        <span class="input-group-text time-picker">
                                            <i class="feather icon-clock"></i>
                                        </span>
                                        <input
                                            class="form-control openEndTimePicker"
                                            id="enterEndTime"
                                            name="end_time"
                                            type="time"
                                            placeholder="Select time"
                                            required
                                            data-parsley-error-message="Please select end time."
                                            data-parsley-endtimeafterstart="#enterStartTime"
                                            data-parsley-endtimeafterstart-message="End time must be greater than start time."
                                            data-parsley-errors-container="#endTime-error-container"
                                            value="{{ isset($sightSeeingPackages) ? $sightSeeingPackages->end_time : '' }}"
                                        >
                                    </div>
                                    <span id="endTime-error-container" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="form-label" for="location">Location</label>
                                    <div class="input-group timepicker">
                                        <span class="input-group-text">
                                            <i class="feather icon-map-pin"></i>
                                        </span>
                                        <input type="text" class="form-control" id="location" name="location"
                                            placeholder="Enter location"
                                            data-parsley-error-message="Please enter Location"
                                            data-parsley-errors-container="#location-error-container" value="{{ isset($sightSeeingPackages) ? $sightSeeingPackages->location : '' }}"
                                            required>
                                    </div>
                                    <span id="location-error-container" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Add description..." rows="5"
                                        cols="4">{{ isset($sightSeeingPackages) ? $sightSeeingPackages->description : '' }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="terms_and_condition">Terms & Condition</label>
                                    <textarea class="form-control" id="terms_and_condition" name="terms_and_condition"
                                        placeholder="Enter terms & condition..." rows="5" cols="4">{{ isset($sightSeeingPackages) ? $sightSeeingPackages->terms_and_condition : '' }}</textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12">
                                <h4>Pricing Details</h4>
                                <div class="mb-3">
                                    <div id="city-price-container">
                                        @php
                                            $packageCabPrices = $sightSeeingPackages->packageCabPrices ?? collect([]);
                                        @endphp

                                        @if ($packageCabPrices->isNotEmpty())
                                            @foreach ($packageCabPrices as $price)
                                                <div class="row datetime-row align-items-end mb-3">
                                                    <input type="hidden" name="package_cab_price_id[]" value="{{ $price->id }}">
                                                    <div class="col-lg-2 col-md-4">
                                                        <label class="form-label">City</label>
                                                        <select class="form-control" name="city[]" required
                                                            data-parsley-required-message="Please select city.">
                                                            <option value="">Select City</option>
                                                            @foreach ($cities as $city)
                                                                <option value="{{ $city->id }}" {{ $price->city_id == $city->id ? 'selected' : '' }}>
                                                                    {{ $city->name ?? '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-3 col-md-4">
                                                        <label class="form-label">Hatchback Price</label>
                                                        <input type="number" class="form-control" name="hatch_back_price[]" placeholder="Hatchback price"
                                                            value="{{ $price->hatchback_price }}" required
                                                            data-parsley-required-message="Please enter hatchback price.">
                                                    </div>

                                                    <div class="col-lg-3 col-md-6">
                                                        <label class="form-label">Sedan Price</label>
                                                        <input type="number" class="form-control" name="sedan_price[]" placeholder="Sedan price"
                                                            value="{{ $price->sedan_price }}" required
                                                            data-parsley-required-message="Please enter Sedan price.">
                                                    </div>

                                                    <div class="col-lg-3 col-md-4">
                                                        <label class="form-label">SUV Price</label>
                                                        <input type="number" class="form-control" name="suv_price[]" placeholder="SUV price"
                                                            value="{{ $price->suv_price }}" required
                                                            data-parsley-required-message="Please enter SUV price.">
                                                    </div>

                                                    <div class="col-lg-1 col-md-1 text-end button-cell">
                                                        <button type="button" class="btn btn-light-danger mt-4 delete-cab-price" data-id="{{ $price->id }}" title="Remove"
                                                            style="margin-bottom: 11px;">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            {{-- Default empty row for new entry --}}
                                            <div class="row datetime-row align-items-end mb-3">
                                                <input type="hidden" name="package_cab_price_id[]" value="">
                                                <div class="col-lg-2 col-md-4">
                                                    <label class="form-label">City</label>
                                                    <select class="form-control" name="city[]" required
                                                        data-parsley-required-message="Please select city.">
                                                        <option value="">Select City</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name ?? '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-lg-3 col-md-4">
                                                    <label class="form-label">Hatchback Price</label>
                                                    <input type="number" class="form-control" name="hatch_back_price[]" placeholder="Hatchback price"
                                                        required data-parsley-required-message="Please enter hatchback price.">
                                                </div>

                                                <div class="col-lg-3 col-md-6">
                                                    <label class="form-label">Sedan Price</label>
                                                    <input type="number" class="form-control" name="sedan_price[]" placeholder="Sedan price" required
                                                        data-parsley-required-message="Please enter Sedan price.">
                                                </div>

                                                <div class="col-lg-3 col-md-4">
                                                    <label class="form-label">SUV Price</label>
                                                    <input type="number" class="form-control" name="suv_price[]" placeholder="SUV price" required
                                                        data-parsley-required-message="Please enter SUV price.">
                                                </div>

                                                <div class="col-lg-1 col-md-1 text-end button-cell">
                                                    <button type="button" class="btn btn-light-danger mt-4 remove-row" title="Remove"
                                                        style="margin-bottom: 11px;">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mb-3">
                                    <button type="button" class="btn btn-light-primary mt-4 add-row" title="Add Row"
                                        style="margin-bottom: 11px;">
                                        <i class="feather icon-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-primary btn-save"
                                    style="margin-bottom: 11px;">
                                    Save
                                </button>
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
    <!-- Toastr JS -->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>

    <script>
        $(function() {
            $('.openTimePicker').on('click', function(e) {
                e.preventDefault();
                const timeInput = document.getElementById('enterStartTime');
                if (timeInput.showPicker) {
                    timeInput.showPicker();
                } else {
                    timeInput.focus();
                }
            });
            $('.openEndTimePicker').on('click', function(e) {
                e.preventDefault();
                const timeInput = document.getElementById('enterEndTime');
                if (timeInput.showPicker) {
                    timeInput.showPicker();
                } else {
                    timeInput.focus();
                }
            });

            $(document).ready(function() {
                window.Parsley.addValidator('endtimeafterstart', {
                    requirementType: 'string',
                    validateString: function (endTimeValue, startTimeSelector) {
                        const startTimeValue = $(startTimeSelector).val();

                        // Skip check if either time is empty (let 'required' handle it)
                        if (!startTimeValue || !endTimeValue) return true;

                        const [startHour, startMinute] = startTimeValue.split(':').map(Number);
                        const [endHour, endMinute] = endTimeValue.split(':').map(Number);

                        const startTotalMinutes = startHour * 60 + startMinute;
                        const endTotalMinutes = endHour * 60 + endMinute;

                        return endTotalMinutes > startTotalMinutes;
                    },
                    messages: {
                        en: 'End time must be greater than start time.'
                    }
                });
                $('#enterStartTime, #enterEndTime').on('change', function() {
                    $('#enterEndTime').parsley().validate();
                });

                imagePreview();
                appendCityRows();

                $('.delete-image-btn').on('click', function(e) {
                    e.preventDefault();
                    var button = $(this);

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You want to delete this Image?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, Delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteImg(button);
                        }
                    });
                });

                $('.delete-cab-price').on('click', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You want to delete this cab price?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, Delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteCabPrice(button);
                        }
                    });
                });
            });

            function imagePreview() {
                // Image preview
                $('#mailLogoImage').on('change', function(event) {
                    const files = event.target.files;
                    const previewContainer = $('#bannerImagePreviewContainer');
                    const uploadMsg = $('.logo-upload-msg');
                    previewContainer.empty();

                    if (files.length > 0) {
                        uploadMsg.hide();
                        Array.from(files).forEach(file => {
                            if (!file.type.startsWith('image/')) return;
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $('<img>')
                                    .attr('src', e.target.result)
                                    .addClass('img-thumbnail')
                                    .css({
                                        width: '100px',
                                        height: '100px',
                                        objectFit: 'cover',
                                        borderRadius: '8px'
                                    })
                                    .appendTo(previewContainer);
                            };
                            reader.readAsDataURL(file);
                        });
                    } else uploadMsg.show();
                });
            }

            $('.time-picker').on('click', function(e) {
                e.preventDefault();
                const timeInput = $(this).closest('.timepicker').find('input[type="time"]')[0];
                if (timeInput) {
                    if (timeInput.showPicker) {
                        timeInput.showPicker()
                    } else {
                        timeInput.focus();
                    }
                }
            });

            function appendCityRows() {
                // Add new pricing row
                $(document).on('click', '.add-row', function() {
                    let newRow = `
                <div class="row datetime-row align-items-end mt-2">
                    <input type="hidden" name="package_cab_price_id[]" >
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">City</label>
                        <select class="form-control" name="city[]" required>
                            <option value="">Select City</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label">Hatchback Price</label>
                        <input type="number" class="form-control" name="hatch_back_price[]" required placeholder="Hatch back price">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Sedan Price</label>
                        <input type="number" class="form-control" name="sedan_price[]" required placeholder="Sedan price">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label">SUV Price</label>
                        <input type="number" class="form-control" name="suv_price[]" required placeholder="SUV price">
                    </div>
                    <div class="col-lg-1 col-md-1 text-end button-cell">
                        <button type="button" class="btn btn-light-danger mt-4 remove-row"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>`;
                    $('#city-price-container').append(newRow);
                });

                // Remove pricing row
                $(document).on('click', '.remove-row', function() {
                    $(this).closest('.datetime-row').remove();
                });
            }


            // ✅ Form Submit
            $('.btn-save').on('click', function(e) {

                e.preventDefault();

                let form = $('#sightseeing-form');

                // Validate using Parsley
                if (!form.parsley().validate()) return;

                let formData = new FormData(form[0]);

                $.ajax({
                    url: "{{ route('admin.sightseeingPackages.save') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.btn-save').attr('disabled', true).text('Saving...');
                    },
                    success: function(response) {
                        if (response.status == true) {
                            toastr.success('Sightseeing package added successfully!');
                            form.trigger('reset');
                            $('#bannerImagePreviewContainer').empty();
                            $('.logo-upload-msg').show();
                            setTimeout(() => {
                                $('.btn-save').attr('disabled', false).text('Save');
                                window.location.href = "{{ route('admin.sightseeingPackages.index') }}";
                            }, 1000);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            const firstError = Object.values(errors)[0][0];
                            toastr.error(firstError);
                        } else {
                            toastr.error("Something went wrong. Please try again.");
                        }
                    },
                    complete: function() {
                        $('.btn-save').attr('disabled', false).text('Save');
                    }
                });

            });
        });

        function deleteImg(data) {
            var imageId = data.data('img-id');

            $.ajax({
                url: "{{ route('admin.sightseeingPackages.deletePackageImage') }}",
                type: "delete",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: imageId,
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Deleted!", response.message, "success");
                        data.closest('div[data-image-name]').remove();
                    } else {
                        Swal.fire("Error!", response.message || "Failed to delete.", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Something went wrong while deleting.", "error");
                }
            });
        }

        function deleteCabPrice(data) {
            var cabPriceId = data.data('id');
            $.ajax({
                url: "{{ route('admin.sightseeingPackages.deletePackageCabPrice') }}",
                type: "delete",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: cabPriceId,
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Deleted!", response.message, "success");
                        $(data).closest('.datetime-row').remove();
                    } else {
                        Swal.fire("Error!", response.message || "Failed to delete.", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Something went wrong while deleting.", "error");
                }
            });
        }
        $(document).on('click', '.preview-btn', function (e) {
            e.preventDefault();

            const imageUrl = $(this).data('image');
            console.log(imageUrl);
            if (!imageUrl) return;

            const lightbox = GLightbox({
                elements: [
                    {
                        href: imageUrl,
                        type: 'image'
                    }
                ],
                touchNavigation: true,
                loop: false,
                width: "90vw",
                height: "90vh"
            });

            lightbox.open();
        });
    </script>
@endsection
