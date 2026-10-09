@extends('layouts.main')

@section('title', $title)

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

        .table-striped>tbody>tr {
            text-align: center;
        }
        /* .form-switch.custom-switch-color .form-check-input {
            background-color: #FF213D;
        }
        .form-switch.custom-switch-color  .form-check-input:checked {
            background-color: #0AA06E;
        } */

        .btn-no-dark-hover {
            background-color: #f8f9fa; /* light background */
            color: #1a1e23;
            border: none !important;
            transition: none;
        }

        .btn-no-dark-hover:hover,
        .btn-no-dark-hover:focus,
        .btn-no-dark-hover:active {
            background-color: #f8f9fa !important; /* no dark hover */
            color: #1a1e23 !important;
            box-shadow: none !important;
            border: none !important;
        }
    </style>

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Trip Management</li>
                        <li class="breadcrumb-item" aria-current="page">{{$title}}</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">{{$title}}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- DOM/Jquery table start -->
        <div class="col-12">
            @include('layouts.message')
        </div>
        <div class="col-sm-12">
            <div class="card table-full-view">
                <div class="card-header">
                    <div class="d-flex .align-items-center justify-content-between">
                        <h5>{{$title}} </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive upcoming-trips">
                        <table id="newBooking" class="table table-striped table-bordered nowrap table-responsive">
                            <thead>
                                <tr>
                                    {{-- <th>Booking Date</th> --}}
                                    <th style="text-align: center;">Pickup Date & Time</th>
                                    <th style="text-align: center;">Booking ID</th>
                                    {{-- <th>Trip Type</th> --}}
                                    <th style="text-align: center;">Pickup From</th>
                                    <th style="text-align: center;">Drop To</th>
                                    {{-- <th>Pickup Address</th>
                                    <th>Drop-off Address</th>
                                    <th>Cab Type</th>
                                    <th>
                                        <div style="color:purple" class="fw-bolder">Traveller Name</div>
                                    </th>
                                    <th>
                                        <div style="color:purple" class="fw-bolder">Gender</div>
                                    </th>
                                    <th>
                                        <div style="color:purple" class="fw-bolder">Mobile</div>
                                    </th>
                                    <th>
                                        <div style="color:purple" class="fw-bolder">State</div>
                                    </th>
                                    <th>
                                        <div style="color:purple" class="fw-bolder">Country</div>
                                    </th>
                                    <th>
                                        <div class="text-primary">Driver Name</div>
                                    </th>
                                    <th>
                                        <div class="text-primary">Driver Mobile Number</div>
                                    </th>
                                    <th>
                                        <div class="text-primary">Cab Model</div>
                                    </th>
                                    <th>
                                        <div class="text-primary">Cab Color</div>
                                    </th>
                                    <th>
                                        <div class="text-primary">Cab Number</div>
                                    </th>
                                    <th>Part Payment</th>
                                    <th>Base Fare</th>
                                    <th>
                                        <div style="color:green">Balance Amount</div>
                                    </th>

                                    <th>
                                        <div class="text-danger">Refund</div>
                                    </th> --}}
                                    <th style="text-align: center;">
                                        <div >Call Status</div>
                                    </th>
                                    {{-- <th>Status</th> --}}
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- DOM/Jquery table end -->
    </div>

    <!-- Assign Driver Model -->
    <div class="modal fade" id="driverDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Assign Driver</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <span class="assignDriverModel"></span>
            </div>
        </div>
    </div>

    <!-- change Driver Model -->
    <div class="modal fade" id="changeDriver" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Change Driver</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <span class="changeDriverModel"></span>
            </div>
        </div>
    </div>

    <!-- Customer Invoice Model -->
    <div class="modal fade" id="invoiceModel" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Invoice</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 d-print-none">
                        <ul class="list-inline ms-auto mb-0 d-flex justify-content-end flex-wrap">
                            <li class="list-inline-item align-bottom me-2">
                                <a href="#" class="avtar avtar-s btn-link-secondary">
                                    <i class="ph-duotone ph-download-simple f-22"></i>
                                </a>
                            </li>
                            <li class="list-inline-item align-bottom me-2">
                                <a href="#" class="avtar avtar-s btn-link-secondary">
                                    <i class="ph-duotone ph-printer f-22"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="row align-items-center g-3">
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="{{ URL::asset('build/images/logo.svg') }}" class="img-fluid"
                                                    alt="images">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 text-sm-end">
                                            <p class="mb-2">INV - 000457</p>
                                            <h6>Date <span class="text-muted f-w-400">03/8/2023</span></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="mb-0">Thanks for Riding,</h6>
                                        <h5>Priya Sharma</h5>
                                        <br>
                                        <p class="mb-0">Goa, India</p>
                                        <p class="mb-0">Mobile : 9988198982</p>
                                        <p class="mb-0">Email ID : priyasharma123@gmail.com</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="mb-3">Booking Details</h6>
                                        <p class="mb-0">Service Type : Airport to City</p>
                                        <p class="mb-0">Booking Date : 11-12-2024</p>
                                        <p class="mb-0">Pickup Date : 12-12-2024</p>
                                        <p class="mb-0">Booking ID : BK987456321</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Trip Breakup</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Base Fare (30 kms) </td>
                                                    <td class="text-end">₹1200.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Surge Charges </td>
                                                    <td class="text-end">₹0.00.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Addl. 5 Kms</td>
                                                    <td class="text-end">₹150.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Waiting Charges 30 Mins</td>
                                                    <td class="text-end">₹100.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-start">
                                        <hr class="mb-2 mt-1">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="invoice-total ms-auto">
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="text-muted mb-1 text-start">Part Payment</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">- ₹300.0.000</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-muted mb-1 text-start">Discount</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">- ₹0.0.000</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-muted mb-1 text-start">Taxes</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">- ₹0.0.000</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-start">Total :</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">₹1711.0.000</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Note</label>
                                    <p class="mb-0">It was a pleasure working with you and your team. We hope you
                                        will keep us in mind for future freelance
                                        projects. Thank You!</p>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded p-3 text-center">
                                        498-2, Gudem, Siolim, Bardez, North Goa, Goa - 403517<br>
                                        GST: 30AAECO0806H1Z1 PAN No: AAECO0806H CIN: U62013GA2023PTC015947<br>
                                        Tel: +91 832 227 2276 Web: www.goataxi.cab<br>
                                        © OfinIT Solutions Pvt. Ltd.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Buttons -->
                    <div class="row d-print-none align-items-center justify-content-end">
                        <div class="col-auto btn-page">
                            <button class="btn btn-primary btn-print-invoice">Print</button>
                            <button class="btn btn-outline-secondary btn-print-invoice">Download</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SOS Model -->
    <div class="modal fade" id="SOSDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">View SOS Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-1">
                        <div class="col-12">
                            <h5 class="mt-2">SOS | <span class="sos-action">(Called Police / Alerted Contacts)</span>
                            </h5>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-danger">SOS by Customer / Driver</h6>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Customer Name</h6>
                            <p class="cab-model-location">Ankit Singh</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Mobile Numebr</h6>
                            <p class="cab-model-location">9898198981</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Cab Number</h6>
                            <p class="cab-model-location">GA 03 AG 1234</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Driver Name</h6>
                            <p class="cab-model-location">Alex D'Souza</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Driver Mobile Number</h6>
                            <p class="cab-model-location">9898298982</p>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Cab Number</h6>
                            <p class="cab-model-location">GA 03 AG 1234</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Driver Name</h6>
                            <p class="cab-model-location">Alex D'Souza</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Driver Mobile Number</h6>
                            <p class="cab-model-location">9898298982</p>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="dt-responsive table-responsive">
                            <table id="sosDetails" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>10-12-2024</td>
                                        <td>04:30 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10-12-2024</td>
                                        <td>04:29 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10-12-2024</td>
                                        <td>04:28 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10-12-2024</td>
                                        <td>04:27 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10-12-2024</td>
                                        <td>04:26 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <!--Trip Complete Process Model -->
    <div class="modal fade" id="tripProcess" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">View Booking Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span class="tripProcessBody"></span>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <div id="modal-container">

    </div>
@endsection

@section('scripts')

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        var status = urlParams.get('status');

        const AIRPORT_PICKUP = {{ App\Enums\Type::AIRPORT_PICKUP }};
        const AIRPORT_DROP = {{ App\Enums\Type::AIRPORT_DROP }};
        const AIRPORT_ONE_ID = {{ App\Enums\Type::AIRPORT_ONE_ID }};
        const AIRPORT_TWO_ID = {{ App\Enums\Type::AIRPORT_TWO_ID }};
        const AIRPORT_ONE = @json(App\Enums\Type::AIRPORT_ONE);
        const AIRPORT_TWO = @json(App\Enums\Type::AIRPORT_TWO);
        const CAB_HATCHBACK = @json(App\Enums\Type::CAB_HATCHBACK);
        const CAB_SEDAN = @json(App\Enums\Type::CAB_SEDAN);
        const CAB_SUV = @json(App\Enums\Type::CAB_SUV);
        const CAB_HATCHBACK_ID = {{ App\Enums\Type::CAB_HATCHBACK_ID }};
        const CAB_SEDAN_ID = {{ App\Enums\Type::CAB_SEDAN_ID }};
        const CAB_SUV_ID = {{ App\Enums\Type::CAB_SUV_ID }};

        // Select with Search
        document.addEventListener('DOMContentLoaded', function() {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'Search driver',
                    searchPlaceholderValue: 'Search Driver / Cab Number / Mobile Number'
                });
            }
        })

        function getEnvironmentsDetails(pickupTime) {

            var environments = @json(getEnvironments());
            var maxHour = 0;
            var minHour = 0;
            $.each(environments, function(e, v) {
                console.log('Setting data new: ' + v.title);
                if (v.title == 'bookingcancellationhour') {
                    maxHour = v.value;
                } else if (v.title == 'bookingcancellationminhour') {
                    minHour = v.value;
                }
            });
            var currentDate = new Date();
            var pickupParts = pickupTime.split(':');
            var pickupDate = new Date(currentDate);
            pickupDate.setHours(pickupParts[0], pickupParts[1], 0, 0);
            var timeDifference = pickupDate - currentDate;
            var maxTimeInMillis = maxHour * 60 * 60 * 1000;
            var minTimeInMillis = minHour * 60 * 60 * 1000;
            if (timeDifference >= minTimeInMillis && timeDifference <= maxTimeInMillis) {
                return true;
            }
            return false;
        }

        $(document).ready(function() {
            $('.assignDriverForm').parsley();
        });
        $('#newBooking').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.trips.list') }}",
                type: "POST",
                data: function(data) {
                    data.search = $('input[type="search"]').val();
                    data._token = "{{ csrf_token() }}";
                    data.status = status;
                }
            },
            order: [

            ],
            pageLength: 10,
            searching: true,
            columns: [
                // {
                //     data: 'booking_date',
                //     name: 'booking_date',
                //     render: function(data, type, row) {
                //         return row.booking_date ?? '--';
                //     }
                // },
                {
                    data: 'pickup_date',
                    name: 'pickup_date',
                    render: function(data, type, row) {
                        var date = row.pickup_date ?? '--';
                        var date2 = row.pickup_date ?? '--';
                        var time = row.pickup_time ?? '--';
                        var bookingdate = row.booking_date ?? '--';

                        if (date !== '--' && time !== '--') {
                            if (date !== '--') {
                                var parts = date.split('-');
                                var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                                date = formattedDate;
                            }
                            var dateTime = new Date(date + ' ' + time);
                            var day = dateTime.getDate();
                            var month = dateTime.getMonth() + 1;
                            var year = dateTime.getFullYear().toString().slice(-2);

                            var formattedDate = `${day}-${month}-${year}`;
                            var formattedTime = dateTime.toLocaleString('en-IN', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                            return `<div>
                                        ${date2} <span class="fw-bolder text-danger">${formattedTime}</span>
                                    </div>
                                    <div>
                                        <span class="small text-decoration-none">Booking Date: ${bookingdate}</span>
                                    </div>
                                    `;
                        } else {
                            return date2 + ' ' + time;
                        }
                    }
                },
                {
                    data: 'booking_id',
                    name: 'booking_id',
                    render: function(data, type, row) {
                        var otp = row.trip_otp ?? '-';//+ '----' +row.id ?? '--';
                        var base_kms = row.cab_rate!= null ? row.cab_rate.base_km : null;

                        var kmsText = base_kms ? `${base_kms} Kms | ` : '';
                        return `<div>
                                ${row.booking_id ?? '--'}
                            </div>
                            <div>
                                <span class="text-decoration-none">${kmsText}OTP: ${otp}</span>
                            </div>
                            `;
                    }
                },
                // {
                //     data: 'trip_type',
                //     name: 'trip_type',
                //     render: function(data, type, row) {
                //         return row.trip_type;
                //     }
                // },
                {
                    data: 'pickup_from',
                    name: 'pickup_from',
                    render: function(data, type, row) {
                        let html = '--';
                        if (row.air_port_drop == AIRPORT_PICKUP) {
                            if (row.pickup_from == AIRPORT_ONE_ID) {
                                html = AIRPORT_ONE;
                            } else if (row.pickup_from == AIRPORT_TWO_ID) {
                                html = AIRPORT_TWO;
                            } else {
                                html = row.get_pickup_from ? row.get_pickup_from.name : '--';
                            }
                        } else {
                            html = row.get_pickup_from ? row.get_pickup_from.name : '--';
                        }
                        return html;
                    }
                },
                {
                    data: 'drop_to',
                    name: 'drop_to',
                    render: function(data, type, row) {
                        html = '--';
                        if (row.air_port_drop == AIRPORT_DROP) {
                            if (row.drop_to == AIRPORT_ONE_ID) {
                                html = AIRPORT_ONE;
                            } else if (row.drop_to == AIRPORT_TWO_ID) {
                                html = AIRPORT_TWO;
                            } else {
                                html = row.get_drop_to ? row.get_drop_to.name : '--';
                            }
                        } else {
                            html = row.get_drop_to ? row.get_drop_to.name : '--';
                        }
                        return html;
                    }
                },
                // {
                //     data: 'pickup_address',
                //     name: 'pickup_address',
                //     render: function(data, type, row) {
                //         html = '--';
                //         if (row.pickup_address) {
                //             html = row.pickup_address;
                //         } else {
                //             if (row.pickup_from == AIRPORT_ONE_ID) {
                //                 html = AIRPORT_ONE;
                //             } else if (row.pickup_from == AIRPORT_TWO_ID) {
                //                 html = AIRPORT_TWO;
                //             } else {
                //                 html = row.get_pickup_from ? row.get_pickup_from.name : '--';
                //             }
                //         }
                //         return html;
                //     }
                // },
                // {
                //     data: 'drop_of_address',
                //     name: 'drop_of_address',
                //     render: function(data, type, row) {
                //         html = '--';
                //         if (row.drop_of_address) {
                //             html = row.drop_of_address;
                //         } else {
                //             if (row.drop_to == AIRPORT_ONE_ID) {
                //                 html = AIRPORT_ONE;
                //             } else if (row.drop_to == AIRPORT_TWO_ID) {
                //                 html = AIRPORT_TWO;
                //             } else {
                //                 html = row.get_drop_to ? row.get_drop_to.name : '--';
                //             }
                //         }
                //         return html;
                //     }
                // },
                // {
                //     data: 'cab_type',
                //     name: 'cab_type',
                //     render: function(data, type, row) {
                //         $type = '';
                //         if (row.cab_type == CAB_HATCHBACK_ID) {
                //             $type = CAB_HATCHBACK;
                //         } else if (row.cab_type == CAB_SEDAN_ID) {
                //             $type = CAB_SEDAN;
                //         } else if (row.cab_type == CAB_SUV_ID) {
                //             $type = CAB_SUV;
                //         } else {
                //             $type = '--';
                //         }
                //         return $type;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         if (row.get_sos_details.length > 0) {
                //             var sosHtml = `<button class="btn btn-sm btn-danger" data-bs-toggle="modal"
            //                         data-bs-target="#SOSDetails">SOS</button>`;
                //             return sosHtml;
                //         } else {
                //             return '--';
                //         }
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var imagePath = "{{ asset('build/images/user/avatar-1.jpg') }}";
                //         var image = row.user ? row.user.image ? row.user.image : '' : '';
                //         if (image != '') {
                //             imagePath = "{{ asset('storage/customer/') }}" + "/" + image;
                //         }
                //         var html = ` <div class="d-flex align-items-center gap-2">
                //                         <a href="${imagePath}" class="glightbox">
                //                             <img src="${imagePath}" alt="image"
                //                                 class="img-radius customer-profile-image" />
                //                         </a>
                //                         <div style="color:purple" class="fw-bolder">${row.user?row.user.name:'--'}</div>
                //                     </div>`;
                //         return html;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var gender = '';
                //         if (row.user.gender == 1) {
                //             gender = 'Male';
                //         } else if (row.user.gender == 0) {
                //             gender = 'Female';
                //         } else if (row.user.gender == 2) {
                //             gender = 'Other';
                //         } else {
                //             gender = '--';
                //         }
                //         var genderHtml =
                //             `<div style="color:purple" class="fw-bolder">${gender}</div>`;
                //         return genderHtml;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var mobile =
                //             `<div style="color:purple" class="fw-bolder">+91${row.user?row.user.phone_number:'--'}</div>`;
                //         return mobile;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var state =
                //             `<div style="color:purple" class="fw-bolder">${row.user?row.user.get_state_details?row.user.get_state_details.name:'':'--'}</div>`;
                //         return state;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var country =
                //             `<div style="color:purple" class="fw-bolder">${row.user?row.user.get_country_details?row.user.get_country_details.name:'':'--'}</div>`;
                //         return country;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var name = row.assign_driver ? row.assign_driver.name : "--";
                //         var html =
                //             `<div class="text-primary fw-bolder">${name}</div>`;
                //         return html;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var mobile = row.assign_driver ? row.assign_driver.mobile : "--";
                //         var html =
                //             `<div class="text-primary fw-bolder">${mobile}</div>`;
                //         return html;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var cabDetails = row.assign_driver ? row.assign_driver.get_cab_details ? row
                //             .assign_driver.get_cab_details : '--' : "--";
                //         var model = '--';
                //         if (cabDetails.model == 1) {
                //             model = 'Baleno, Swift or similar';
                //         } else if (cabDetails.model == 3) {
                //             model = 'Dzire, Etios or similar';

                //         } else if (cabDetails.model == 4) {
                //             model = 'Xylo, Ertiga or similar';

                //         }
                //         var html =
                //             `<div class="text-primary fw-bolder">${model}</div>`;
                //         return html;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {

                //         var cabDetails = row.assign_driver ? row.assign_driver.get_cab_details ? row
                //             .assign_driver.get_cab_details.get_color_details ? row.assign_driver
                //             .get_cab_details.get_color_details.name : '--' : '--' : "--";
                //         var html =
                //             `<div class="text-primary fw-bolder">${cabDetails}</div>`;
                //         return html;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var cabDetails = row.assign_driver ? row.assign_driver.get_cab_details ? row
                //             .assign_driver.get_cab_details : '--' : "--";
                //         var html =
                //             `<div class="text-primary fw-bolder">${cabDetails.number??'--'}</div>`;
                //         return html;
                //     }
                // },

                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var partPayment = row.part_payment ? `${getCurrencySign()+row.part_payment}` : '--';
                //         return partPayment;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var fullPayment = row.base_fare ? `${getCurrencySign()+row.base_fare}` : '--';
                //         return fullPayment;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var totalPayemnt = row.cash_with_driver ?
                //             `${getCurrencySign()+row.cash_with_driver}` : '--';
                //         var html = `<span style="color:green" class="fw-bolder">${totalPayemnt}</span>`;
                //         return html;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var refund = row.refund ? `${getCurrencySign()+row.refund}` : '--';
                //         var html = `<span class="text-danger fw-bolder">${refund}</span>`;
                //         return html;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var partPayment = row.part_payment ? `${getCurrencySign()+row.part_payment}` : '--';
                //         return partPayment;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var fullPayment = row.base_fare ? `${getCurrencySign()+row.base_fare}` : '--';
                //         var taxAmount = row.tax_amount ? `${getCurrencySign()+row.tax_amount}` : '--';

                //         return fullPayment + `<br><span class="small">+Other Charges </span><span class="small badge bg-danger text-decoration-none">${taxAmount}</span>`;
                //     }
                // },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var totalPayemnt = row.cash_with_driver ?
                //             `${getCurrencySign()+row.cash_with_driver}` : '--';
                //         var html = `<span style="color:green" class="fw-bolder">${totalPayemnt}</span>`;
                //         return html;
                //     }
                // },
                {
                    data: 'default',
                    name: 'default',
                    render: function(data, type, row) {
                        var call_status = row.call_status;

                        if(call_status == 0) {
                            var html = `<div class="form-check form-switch custom-switch-color d-flex justify-content-center align-items-center" style="gap: 2.5rem;">
                                <label class="form-check-label mb-0" >DNC</label>
                                <input class="form-check-input" type="checkbox" value="0" role="switch" data-id="${row.id}" style="accent-color: red;">
                            </div>`;
                        } else {
                            var html = `<div class="form-check form-switch custom-switch-color d-flex justify-content-center align-items-center" style="gap: 2.5rem;">
                                <label class="form-check-label mb-0" >SWC</label>
                                <input class="form-check-input" type="checkbox" value="1" role="switch" data-id="${row.id}" checked>
                            </div>`;
                        }
                        return html;
                    }
                },
                // {
                //     data: 'default',
                //     name: 'default',
                //     render: function(data, type, row) {
                //         var status = row.status;
                //         var statusHtml = ``;
                //         var assignDriver = ``;
                //         if (status != 0) {
                //             assignDriver =
                //                 `<span class="badge bg-primary text-decoration-none">Driver Assigned</span>`;
                //         }
                //         if (status == 3) {
                //             statusHtml =
                //                 `<span class="badge bg-danger text-decoration-none">Cancel</span>`;
                //         } else if (status == 4 && row.refund) {
                //             statusHtml =
                //                 `<span class="badge bg-warning text-decoration-none">Refunded</span>`;
                //         } else if (status == 4) {
                //             statusHtml =
                //                 `<span class="badge bg-success text-decoration-none">Completed</span>`;
                //         } else if(row.assigned_driver_id){
                //             statusHtml = `<span class="badge bg-info text-decoration-none">Driver Assigned</span>`;
                //         }
                //         else {
                //             // if(!assignDriver){
                //             statusHtml = `--`;
                //             // }
                //         }
                //         return statusHtml;
                //         // return statusHtml + `&nbsp;` + assignDriver;
                //     }
                // },
                {
                    data: 'default',
                    name: 'default',
                    render: function(data, type, row) {
                        var status = row.status;
                        var statusHtml = ``;
                        var cancelButton = ``;
                        var completeButton = ``;
                        var changeDriver = ``;
                        var assignDriver = ``;
                        if(row.assigned_driver_id && (status == 0)){
                            assignDriver = `<span class="btn btn-sm btn-light-dark" style="cursor: default;">Driver Assigned</span>`;
                        }
                        if (status == 0 || status == 1) {
                            var urlTemplate = '{{ route('admin.trips.cancelBooking', [':id', ':type']) }}';
                            var cancelUrl = urlTemplate.replace(':id', row.id).replace(':type', 'cancel');
                            cancelButton =
                                `<a href="#!" class="btn btn-sm btn-light-danger me-1 cancelBooking" data-type="${row.cab_type}" data-url="${cancelUrl}"><i class="ti ti-circle-x"></i></a>`;

                        }
                        if (status == 1) {
                            var urlTemplate = '{{ route('admin.trips.cancelBooking', [':id', ':type']) }}';
                            var completeUrl = urlTemplate.replace(':id', row.id).replace(':type',
                                'complete');
                            completeButton =
                                `<a href="#!" class="btn btn-sm btn-light-success me-1 completeBooking" data-type="${row.cab_type}" data-url="${completeUrl}"><i class="ti ti-circle-check"></i></a>`;
                            completeButton +=
                                `<a href="#!" class="btn btn-sm btn-light-warning me-1 markNoShow" title="Mark as no-show" data-id="${row.id}" data-booking="${row.booking_id}"><i class="ti ti-user-off"></i></a>`;
                        }
                        if (status == 5) {
                            statusHtml = `<span class="btn btn-sm btn-light-warning" style="cursor: default;">No-show</span> <a href="#!" class="btn btn-sm btn-light-secondary undoNoShow" data-id="${row.id}">Undo</a>`;
                        }
                        if (status == 0) {
                            statusHtml = `<a href="#!" class="btn btn-sm btn-no-dark-hover me-1 assignDriver" data-type="${row.cab_type}" data-booking="${row.id}" data-bs-toggle="modal"
                                            data-bs-target="#driverDetails"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0_2433_125)">
                                                <path d="M13.2125 11.2996L11.3281 10.7715C9.75315 9.79961 10.2531 10.109 10.1531 10.0465V9.00273C10.8031 8.31523 11.3906 7.28086 11.4563 5.18711C11.6375 5.03398 11.7563 4.80586 11.7563 4.54336C11.7563 4.27773 11.7563 4.02461 11.6656 3.62461L12.0688 2.83086C12.3219 2.33086 12.1031 1.71836 11.5906 1.49023L8.3969 0.0839844C8.1469 -0.0253906 7.85627 -0.0253906 7.60627 0.0839844L4.40627 1.49336C3.89377 1.71836 3.67502 2.33086 3.92815 2.83086L4.33127 3.62773C4.2469 4.02773 4.2469 4.27773 4.2469 4.55586C4.2469 4.78711 4.3344 5.01211 4.54377 5.18711C4.60627 7.25273 5.20627 8.30898 5.8469 8.99336V10.0434C5.7469 10.1059 6.2469 9.79648 4.67502 10.7715L2.79065 11.2996C1.6594 11.6184 0.868774 12.6621 0.868774 13.8402V15.5746C0.868774 15.759 1.01877 15.909 1.20315 15.909C1.38752 15.909 1.53127 15.759 1.53127 15.5746V13.8402C1.53127 12.9621 2.1219 12.1809 2.96877 11.9434L4.65627 11.4684L5.53127 12.8777C5.7969 13.3059 6.3594 13.4684 6.8219 13.1965L7.0344 13.409L6.79065 15.6309C6.7719 15.8152 6.90315 15.9777 7.0844 15.9996C7.26565 16.0184 7.43127 15.8871 7.45315 15.7059L7.65002 13.9121C7.7594 13.9559 7.87502 13.9777 7.9969 13.9777C8.11877 13.9777 8.2344 13.9527 8.34377 13.9121L8.54065 15.7059C8.56252 15.9121 8.7719 16.034 8.9094 15.9996C9.09377 15.9809 9.22503 15.8152 9.20315 15.6309L8.9594 13.409L9.1719 13.1965C9.63128 13.4652 10.1969 13.309 10.4625 12.8777L11.3375 11.4684L13.0281 11.9402C13.875 12.1777 14.4656 12.959 14.4656 13.8371V15.5715C14.4656 15.7559 14.6157 15.9059 14.8 15.9059C14.9844 15.9059 15.1344 15.7559 15.1344 15.5715V13.8371C15.1344 12.6652 14.3438 11.6215 13.2125 11.2996ZM8.00002 11.5309L6.51252 10.0934V9.60898C6.5219 9.61836 6.73127 9.79336 6.7344 9.79648C7.00002 10.0215 7.3344 10.1434 7.68127 10.1434H8.32502C8.6719 10.1434 9.0094 10.0184 9.27502 9.79336C9.46877 9.63086 9.47502 9.62148 9.49377 9.61211V10.0934L8.00002 11.5309ZM11.0906 4.55898C11.0906 4.64648 11.0188 4.72148 10.9282 4.72148C10.9531 4.72148 10.6406 4.71836 5.0719 4.72148C4.9844 4.72148 4.9094 4.64961 4.9094 4.55898C4.9094 4.32461 4.9094 4.16523 4.95002 3.91836H11.0469C11.0906 4.16211 11.0906 4.34023 11.0906 4.55898ZM9.80315 5.38711L9.51252 5.66836C9.2969 5.87773 9.01252 5.99023 8.71565 5.99023H7.29065C6.99065 5.99023 6.70627 5.87461 6.49065 5.66523L6.20315 5.38711H9.80315ZM5.24377 5.38711L6.02502 6.14336C6.36565 6.47461 6.81252 6.65586 7.28752 6.65586H8.71252C9.1844 6.65586 9.6344 6.47461 9.97503 6.14648L10.7594 5.38711H10.7813C10.6563 7.84023 9.76252 8.50898 8.83752 9.28711C8.69377 9.40898 8.5094 9.47773 8.3219 9.47773H7.67815C7.49065 9.47773 7.30627 9.40898 7.16252 9.28711C6.40315 8.61211 5.34065 8.01211 5.21252 5.38711H5.24377ZM4.67502 2.10273L7.87502 0.693359C7.95627 0.658984 8.0469 0.658984 8.12815 0.693359L11.325 2.10273C11.4906 2.17461 11.5594 2.37148 11.4781 2.53086L11.1125 3.24961H4.8844L4.5219 2.53086C4.44065 2.37148 4.51252 2.17461 4.67502 2.10273ZM6.10002 12.5246L5.26877 11.1871C6.45627 10.4559 6.0469 10.7059 6.1344 10.6527L7.47502 11.9496L6.5094 12.6121C6.3719 12.7059 6.18752 12.6652 6.10002 12.5246ZM7.79377 13.2246L7.38752 12.8184L8.00002 12.3996L8.61252 12.8184L8.20627 13.2246C8.0969 13.334 7.90315 13.334 7.79377 13.2246ZM9.90002 12.5246C9.81252 12.6621 9.62815 12.7059 9.49065 12.6121L8.52502 11.9496L9.86565 10.6527C9.95315 10.7059 9.5469 10.4559 10.7281 11.1871L9.90002 12.5246Z" fill="#1A1E23"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0_2433_125">
                                                <rect width="16" height="16" fill="white"/>
                                                </clipPath>
                                                </defs>
                                                </svg>
                                            </a>`;
                        }
                        else if (status == 3) {
                            statusHtml =
                                `<span class="btn btn-sm btn-light-danger" style="cursor: default;" >Cancelled</span> <a href="#" class="btn btn-sm btn-light-warning refund-amount" data-id="${row.id}" data-time="${row.pickup_time}">Refund</a>`;
                        } else if (status == 1 && !row.assigned_driver_id) {
                            changeDriver = `<a href="#!" class="btn btn-sm btn-light-success me-1 changeDriver" data-type="${row.cab_type}" data-booking="${row.id}" data-bs-toggle="modal"
                                        data-bs-target="#changeDriver">Assign Driver</a>`;

                        }  else if (status == 1 && row.assigned_driver_id) {
                            changeDriver = `<a href="#!" class="btn btn-sm btn-light-success me-1 changeDriver" data-type="${row.cab_type}" data-booking="${row.id}" data-bs-toggle="modal"
                                        data-bs-target="#changeDriver">Change Driver</a>`;

                        }else if(status == 4 && !row.refund){

                            statusHtml =
                                `<span class="btn btn-sm btn-light-success" style="cursor: default;">Completed</span>`;
                        } else if (row.refund) {
                            statusHtml =
                                `<span class="btn btn-sm btn-light-info" style="cursor: default;">Refunded</span>`;
                        }
                        else {
                            statusHtml = `--`;
                        }
                        var viewBtn = `
                            <button type='button' class='btn btn-sm btn-light-info btn-view' data-id="${row.id}">
                                <i class='ti ti-eye'></i>
                            </button>
                        `;
                        return viewBtn + statusHtml + assignDriver +changeDriver + cancelButton + completeButton;
                    }
                },
            ],
            drawCallback: function() {
                GLightbox({
                    touchNavigation: true,
                    loop: true,
                    width: "90vw",
                    height: "90vh"
                });
            }
        });

        $(function() {
            $(document).on('click', '.assignDriverM', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = "{{ route('admin.trips.getDriverList') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    success: function(res) {
                        if (res.status) {
                            if (res.data.length > 0) {
                                $('.bookingId').val(id);
                                var html = `<option value="" hidden>Select Drivers</option>`;
                                $.each(res.data, function(e, v) {
                                    html +=
                                        `<option value="${v.id}">${v.name}/${v.get_cab_details.number}/${v.mobile}</option>`;
                                });
                                $('.DriverSelectHtml').html(html);
                            }
                        }

                    }
                });
            });

            // Cancel Booking
            $(document).on('click', '.cancelBooking', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = $(this).data('url');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to cancel booking!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'get',
                            url: url,
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        text: response.Message,
                                        icon: "success",
                                        button: "Ok",
                                    }).then(function() {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        text: response.Message,
                                        icon: "Error",
                                        button: "Ok",
                                    }).then(function() {
                                        location.reload();
                                    });
                                }
                            }
                        });
                    }
                });

            });

            // Complete Booking
            $(document).on('click', '.completeBooking', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = $(this).data('url');
                Swal.fire({
                    title: "Are you sure?",
                    text: "This booking is completed!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'get',
                            url: url,
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire({
                                        text: response.Message,
                                        icon: "success",
                                        button: "Ok",
                                    }).then(function() {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        text: response.Message,
                                        icon: "Error",
                                        button: "Ok",
                                    }).then(function() {
                                        location.reload();
                                    });
                                }
                            }
                        });
                    }
                });

            });

            $(document).on('click', '.tripProcessModel', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = "{{ route('admin.trips.getBookingDetails') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {
                        id: id
                    },
                    success: function(res) {
                        if (res.status) {
                            $('.tripProcessBody').html(res.html);
                        }

                    }
                });
            });

            // assign driver model
            $(document).on('click', '.assignDriver', function(e) {
                e.preventDefault();
                var cab_type = $(this).data('type');
                var bookingId = $(this).data('booking');
                var url = "{{ route('admin.trips.getDriverList') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {
                        cab_type: cab_type,
                        booking_id: bookingId
                    },
                    success: function(res) {
                        if (res.status) {
                            // $('.booking_id').val(bookingId);
                            // var html = `<option value="" hidden>Select Drivers</option>`;
                            // if(res.data.length>0){
                            //     $.each(res.data,function(e,v){
                            //         html += `<option value="${v.id}">${v.name}/${v.get_cab_details?v.get_cab_details.number:''}/${v.mobile}</option>`;
                            //     });
                            // }
                            // console.log(html);

                            // $('.selectOption').html(html);
                            $('.assignDriverModel').html(res.html);
                        }
                    }
                });
            });
            // assign driver model
            $(document).on('click', '.changeDriver', function(e) {
                e.preventDefault();
                var cab_type = $(this).data('type');
                var bookingId = $(this).data('booking');
                var url = "{{ route('admin.trips.changeDriver') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {
                        cab_type: cab_type,
                        booking_id: bookingId
                    },
                    success: function(res) {
                        if (res.status) {
                            $('.changeDriverModel').html(res.html);
                        }
                    }
                });
            });

            // No-show (admin only, after pickup time): forfeits the advance and issues its invoice.
            $(document).on('click', '.markNoShow', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var bookingId = $(this).data('booking');
                Swal.fire({
                    title: "Mark " + bookingId + " as no-show?",
                    text: "The customer's online advance will be forfeited and invoiced, and they will be notified.",
                    input: "text",
                    inputPlaceholder: "Reason (e.g. customer unreachable at pickup)",
                    inputValidator: (value) => !value && "Please enter a reason.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Mark no-show"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.trips.markNoShow', ':id') }}'.replace(':id', id),
                            type: "POST",
                            data: { reason: result.value, _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                Swal.fire(response.status ? "Done" : "Not allowed", response.Message, response.status ? "success" : "error")
                                    .then(function() { if (response.status) location.reload(); });
                            },
                            error: function() {
                                Swal.fire("Error!", "Could not mark as no-show.", "error");
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.undoNoShow', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: "Undo no-show?",
                    text: "Allowed only on the same day. Any no-show invoice will be reversed with a credit note.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Undo"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.trips.undoNoShow', ':id') }}'.replace(':id', id),
                            type: "POST",
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                Swal.fire(response.status ? "Done" : "Not allowed", response.Message, response.status ? "success" : "error")
                                    .then(function() { if (response.status) location.reload(); });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.refund-amount', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to refund amount",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.trips.refundAmount') }}',
                            type: "POST",
                            data:{
                                id:id,
                                _token:'{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire("Success!",response.message, "success");
                                        location.reload();
                                }else{
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function() {
                                Swal.fire("Error!", "Refund failed.", "error");
                            }
                        });
                    }
                });
            });

            // Refund Amount
            $(document).on('click', '.refund-amounts', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                var time = $(this).data('time');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to refund amount!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (!getEnvironmentsDetails(time)) {
                            Swal.fire("Sorry!", "Customer not eligible for refund.", "error");
                        }
                        // $.ajax({
                        //     url: url,
                        //     type: "GET",
                        //     success: function(response) {
                        //         Swal.fire("Success!",
                        //             "Adds has been deleted.", "success");
                        //         location.reload();
                        //     },
                        //     error: function() {
                        //         Swal.fire("Error!", "Something went wrong.", "error");
                        //     }
                        // });
                    }
                });
            });
            $('#newBooking').on('click','.btn-view',function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    type: "post",
                    url: "{{ route('admin.trips.bookingDetail') }}",
                    data:{
                        _token : "{{ csrf_token() }}",
                        id : id
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status == true) {
                            $('#modal-container').empty();
                            $('#modal-container').html(response.view);
                            $("#bookingDetailModal").modal('show');
                        }
                    }
                });
            });
            $('#newBooking').on('click', '.form-check-input', function() {
                var checkedStatus = $(this).is(':checked');
                var dataId = $(this).data('id');
                $.ajax({
                    type: "post",
                    url: "{{ route('admin.trips.updateCallStatus') }}",
                    data:{
                        _token : "{{ csrf_token() }}",
                        id : dataId,
                        status : checkedStatus,
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status == true) {
                            Swal.fire("Success!",response.message, "success");
                            $('#newBooking').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        });
    </script>
@endsection
