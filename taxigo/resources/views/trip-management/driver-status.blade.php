@extends('layouts.main')

@section('title', 'Driver Status')
@section('breadcrumb-item', 'Trip Management')

@section('breadcrumb-item-active', 'Driver Status')

@section('css')

    <!-- GLightBox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- Popup Animation -->
    <link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/star-rating.min.css') }}">

@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

    <div class="row">

        <div class="col-md-3 col-sm-6">
            <div class="card statistics-card-1 overflow-hidden ">
                <div class="card-body">
                    <img src="{{asset('build/images/widget/img-status-5.svg')}}" alt="img" class="img-fluid img-bg">
                    <h5 class="mb-4">Drivers</h5>
                    <div class="d-flex align-items-center mt-3">
                        <h3 class="f-w-300 d-flex align-items-center m-b-0">Total : 120</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card statistics-card-1 overflow-hidden ">
                <div class="card-body">
                    <img src="{{asset('build/images/widget/img-status-5.svg')}}" alt="img" class="img-fluid img-bg">
                    <h5 class="mb-4">Ready For Rides</h5>
                    <div class="d-flex align-items-center mt-3">
                        <h3 class="f-w-300 d-flex align-items-center m-b-0">Total : 45</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card statistics-card-1 overflow-hidden ">
                <div class="card-body">
                    <img src="{{ URL::asset('build/images/widget/img-status-5.svg') }}" alt="img" class="img-fluid img-bg">
                    <h5 class="mb-4">In-Transit</h5>
                    <div class="d-flex align-items-center mt-3">
                        <h3 class="f-w-300 d-flex align-items-center m-b-0">Total : 25</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card statistics-card-1 overflow-hidden ">
                <div class="card-body">
                    <img src="{{ URL::asset('build/images/widget/img-status-5.svg') }}" alt="img" class="img-fluid img-bg">
                    <h5 class="mb-4">Unavailable</h5>
                    <div class="d-flex align-items-center mt-3">
                        <h3 class="f-w-300 d-flex align-items-center m-b-0">Total : 50</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <img src="{{ URL::asset('build/images/preview-images/driver-status.png') }}" alt="" class="w-100 h-100">
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Driver Status</h5>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table id="driverAvailable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Fleet Operator</th>
                                    <th>Driver Name</th>
                                    <th>Mobile Number</th>
                                    <th>Cab Zone</th>
                                    <th>Cab Number</th>
                                    <th>Live Location</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Operator-1</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>9898298982</td>
                                    <td>Baga Beach</td>
                                    <td>GA 03 DC 9856</td>
                                    <td><a href="#">Calangute</a></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-danger text-decoration-none">Unavailable</a>
                                    </td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#driverStatus"><i class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Operator-1</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>9898298982</td>
                                    <td>Baga Beach</td>
                                    <td>GA 03 DC 9856</td>
                                    <td><a href="#">Baga</a></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-warning text-decoration-none">Intransit</a>
                                    </td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#driverStatus"><i class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Operator-1</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>9898298982</td>
                                    <td>Baga Beach</td>
                                    <td>GA 03 DC 9856</td>
                                    <td><a href="#">Arpora</a></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success text-decoration-none">Ready For Rides</a>
                                    </td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#driverStatus"><i class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Operator-1</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>9898298982</td>
                                    <td>Baga Beach</td>
                                    <td>GA 03 DC 9856</td>
                                    <td><a href="#">Anjuna</a></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success text-decoration-none">Ready For Rides</a>
                                    </td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#driverStatus"><i class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Operator-1</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>9898298982</td>
                                    <td>Baga Beach</td>
                                    <td>GA 03 DC 9856</td>
                                    <td><a href="#">Siolin</a></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success text-decoration-none">Ready For Rides</a>
                                    </td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#driverStatus"><i class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Model -->
    <div class="modal fade" id="driverStatus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">View Booking Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="acceptRide" class="form-wizard row justify-content-center">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-3">
                                    <ul class="nav nav-pills nav-justified">
                                        <li class="nav-item" data-target-form="#contactDetailForm">
                                            <a href="#contactDetail" data-bs-toggle="tab" data-toggle="tab"
                                                class="nav-link active">
                                                <i class="ph-duotone ph-chalkboard-teacher"></i>
                                                <span class="d-none d-sm-inline">Accept / Reject</span>
                                            </a>
                                        </li>
                                        <!-- end nav item -->
                                        <li class="nav-item" data-target-form="#jobDetailForm">
                                            <a href="#jobDetail" data-bs-toggle="tab" data-toggle="tab"
                                                class="nav-link icon-btn">
                                                <i class="ph-duotone ph-grid-nine"></i>
                                                <span class="d-none d-sm-inline">Enter OTP</span>
                                            </a>
                                        </li>
                                        <!-- end nav item -->
                                        <li class="nav-item" data-target-form="#educationDetailForm">
                                            <a href="#educationDetail" data-bs-toggle="tab" data-toggle="tab"
                                                class="nav-link icon-btn">
                                                <i class="ph-duotone ph-map-pin-line"></i>
                                                <span class="d-none d-sm-inline">End Ride</span>
                                            </a>
                                        </li>
                                        <!-- end nav item -->
                                        <li class="nav-item">
                                            <a href="#finish" data-bs-toggle="tab" data-toggle="tab"
                                                class="nav-link icon-btn">
                                                <i class="ph-duotone ph-notebook"></i>
                                                <span class="d-none d-sm-inline">Generate Invoice</span>
                                            </a>
                                        </li>
                                        <!-- end nav item -->
                                    </ul>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- START: Define your progress bar here -->
                                        <div id="bar" class="progress mb-3" style="height: 7px;">
                                            <div
                                                class="bar progress-bar progress-bar-striped progress-bar-animated bg-success">
                                            </div>
                                        </div>
                                        <!-- END: Define your progress bar here -->
                                        <!-- START: Define your tab pans here -->
                                        <div class="tab-pane show active" id="contactDetail">
                                            <form id="contactForm" method="post" action="#">
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">Booking ID</h6>
                                                                        <p class="cab-model-location">#GT1245781232</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">Trip Type</h6>
                                                                        <p class="cab-model-location">Airport Pickup</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">From</h6>
                                                                        <p class="cab-model-location">Manohar International Airport (GOX)</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">To</h6>
                                                                        <p class="cab-model-location">Calangut</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-header d-flex align-items-center gap-3">
                                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                                    alt="user-image" class="wid-50 rounded img-fluid">
                                                                <div>
                                                                    <h6 class="mb-1">Jason Whitman</h6>
                                                                    <p class="mb-0">Male</p>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="col-12">
                                                                    <h6 class="mb-1">Date & Time</h6>
                                                                    <p class="cab-model-location">12 Mar, 2024 | 12:23 PM
                                                                    </p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <h6 class="mb-1">Pickup Address</h6>
                                                                    <p class="cab-model-location">Manohar International
                                                                        Airport, Mopa - Airport Road, Village Mopa, Pernem,
                                                                        403512.</p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <h6 class="mb-1">Drop-off Address</h6>
                                                                    <p class="cab-model-location">Calangute Beach, Dr.
                                                                        Afonso Road, Near Calangute Market, Calangute,
                                                                        Bardez, 403516.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- end contact detail tab pane -->
                                        <div class="tab-pane" id="jobDetail">
                                            <form id="jobForm" method="post" action="#">
                                                <div class="row mt-4">
                                                    <div class="col-12">

                                                        <label class="form-label">Enter OTP</label>
                                                        <input type="number" class="form-control" placeholder="Enter OTP">

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- end job detail tab pane -->
                                        <div class="tab-pane" id="educationDetail">
                                            <form id="educationForm" method="post" action="#">
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">Booking ID</h6>
                                                                        <p class="cab-model-location">#GT1245781232</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">Trip Type</h6>
                                                                        <p class="cab-model-location">Airport Pickup</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">From</h6>
                                                                        <p class="cab-model-location">Manohar International Airport (GOX)</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6 class="mb-1">To</h6>
                                                                        <p class="cab-model-location">Calangut</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-header d-flex align-items-center gap-3">
                                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                                    alt="user-image" class="wid-50 rounded img-fluid">
                                                                <div>
                                                                    <h6 class="mb-1">Jason Whitman</h6>
                                                                    <p class="mb-0">Male</p>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="col-12">
                                                                    <h6 class="mb-1">Date & Time</h6>
                                                                    <p class="cab-model-location">12 Mar, 2024 | 12:23 PM
                                                                    </p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <h6 class="mb-1">Pickup Address</h6>
                                                                    <p class="cab-model-location">Manohar International
                                                                        Airport, Mopa - Airport Road, Village Mopa, Pernem,
                                                                        403512.</p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <h6 class="mb-1">Drop-off Address</h6>
                                                                    <p class="cab-model-location">Calangute Beach, Dr.
                                                                        Afonso Road, Near Calangute Market, Calangute,
                                                                        Bardez, 403516.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label d-block" for="number">Waiting
                                                                        Charges (₹ 100 / 30mins)</label>
                                                                    <div class="btn-group btn-group-sm mb-2 border w-100"
                                                                        role="group" id="waitingCharge">
                                                                        <button type="button" id="waitDecrease"
                                                                            onclick="decreaseWait('number')"
                                                                            class="btn btn-link-secondary"><i
                                                                                class="ti ti-minus"></i></button>
                                                                        <input
                                                                            class="border-0 m-0 form-control rounded-0 shadow-none"
                                                                            type="text" id="number" value="0">
                                                                        <button type="button" id="waitIncrease"
                                                                            onclick="increaseWait('number')"
                                                                            class="btn btn-link-secondary"><i
                                                                                class="ti ti-plus"></i></button>
                                                                    </div>
                                                                    <h6 class="mb-1">Amount : ₹350</h6>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label" for="addKMcharge">Additional
                                                                        Km Charges</label>
                                                                    <div class="btn-group btn-group-sm mb-2 border w-100"
                                                                        role="group" id="waitingCharge">
                                                                        <button type="button" id="kmDecrease"
                                                                            onclick="decreaseKm('addKMcharge')"
                                                                            class="btn btn-link-secondary"><i
                                                                                class="ti ti-minus"></i></button>
                                                                        <input
                                                                            class="border-0 m-0 form-control rounded-0 shadow-none"
                                                                            type="text" id="addKMcharge" value="0">
                                                                        <button type="button" id="kmIncrease"
                                                                            onclick="increaseKm('addKMcharge')"
                                                                            class="btn btn-link-secondary"><i
                                                                                class="ti ti-plus"></i></button>
                                                                    </div>
                                                                    <h6 class="mb-1">Amount : ₹350</h6>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- end education detail tab pane -->
                                        <div class="tab-pane" id="finish">
                                            <div class="roW">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="mb-3 d-print-none">
                                                                <ul
                                                                    class="list-inline ms-auto mb-0 d-flex justify-content-end flex-wrap">
                                                                    <li class="list-inline-item align-bottom me-2">
                                                                        <a href="#"
                                                                            class="avtar avtar-s btn-link-secondary">
                                                                            <i
                                                                                class="ph-duotone ph-download-simple f-22"></i>
                                                                        </a>
                                                                    </li>
                                                                    <li class="list-inline-item align-bottom me-2">
                                                                        <a href="#"
                                                                            class="avtar avtar-s btn-link-secondary">
                                                                            <i class="ph-duotone ph-printer f-22"></i>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-12">
                                                                    <div class="row align-items-center g-3">
                                                                        <div class="col-sm-6">
                                                                            <div class="d-flex align-items-center mb-2">
                                                                                <img src="{{ URL::asset('build/images/logo.svg') }}"
                                                                                    class="img-fluid" alt="images">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6 text-sm-end">
                                                                            <p class="mb-2">INV - 000457</p>
                                                                            <h6>Date <span
                                                                                    class="text-muted f-w-400">03/8/2023</span>
                                                                            </h6>
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
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="border rounded p-3 h-100">
                                                                        <h6 class="mb-0">Booking Details</h6>
                                                                        <p class="mb-0">Service Type : Airport to City</p>
                                                                        <p class="mb-0">Booking Date : 11-12-2024</p>
                                                                        <p class="mb-0">Booking ID : BK987456321</p>
                                                                        <p class="mb-0">Pickup Date : 12-12-2024</p>
                                                                        <p class="mb-0">Booking Email ID :
                                                                            priyasharma123@gmail.com</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="table">
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
                                                                                    <td class="text-end">Rs.1200</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Surge Charges </td>
                                                                                    <td class="text-end">Rs.0.00</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Addl. 5 Kms</td>
                                                                                    <td class="text-end">Rs.150</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Waiting Charges 30 Mins</td>
                                                                                    <td class="text-end">Rs.100</td>
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
                                                                                <p class="text-muted mb-1 text-start">
                                                                                    Discount</p>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <p class="f-w-600 mb-1 text-end">Rs.0.00</p>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <p class="text-muted mb-1 text-start">Taxes
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <p class="f-w-600 mb-1 text-end">Rs.0.00</p>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <p class="f-w-600 mb-1 text-start">Total :
                                                                                </p>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <p class="f-w-600 mb-1 text-end">Rs.1711.00
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="form-label">Note</label>
                                                                    <p class="mb-0">It was a pleasure working with you and
                                                                        your team. We hope you
                                                                        will keep us in mind for future freelance
                                                                        projects. Thank You!</p>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="border rounded p-3 text-center">
                                                                        498-2, Gudem, Siolim, Bardez, North Goa, Goa -
                                                                        403517<br>
                                                                        GST: 30AAECO0806H1Z1 PAN No: AAECO0806H CIN:
                                                                        U62013GA2023PTC015947<br>
                                                                        Tel: +91 832 227 2276 Web: www.goataxi.cab<br>
                                                                        © OfinIT Solutions Pvt. Ltd.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end col -->
                                            </div>
                                            <!-- end row -->
                                        </div>
                                        <!-- END: Define your tab pans here -->
                                        <!-- START: Define your controller buttons here-->
                                        <div class="d-flex wizard justify-content-between mt-3">
                                            <div class="first d-none">
                                                <a href="!#" class="btn btn-secondary">
                                                </a>
                                            </div>
                                            <div class="d-flex gap-3">
                                                <div class="previous me-2">
                                                    <a href="!#" class="btn btn-danger">
                                                        Cancel Ride
                                                    </a>
                                                </div>
                                                <div class="me-2">
                                                    <a href="#" class="btn btn-secondary">
                                                        Passenger No-Show
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="next">
                                                <a href="!#" class="btn btn-success">
                                                    Next
                                                </a>
                                            </div>
                                            <div class="last d-none">
                                                <a href="!#" class="btn btn-secondary mt-3 mt-md-0">
                                                </a>
                                            </div>
                                        </div>
                                        <!-- END: Define your controller buttons here-->
                                    </div>
                                </div>
                            </div>
                            <!-- end tab content-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->

    <!-- GLightBox -->
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    <!-- SweetAlert JS -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <!-- Wizard JS -->
    <script src="{{ URL::asset('build/js/plugins/wizard.min.js') }}"></script>

    <!-- DataTable JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        var table = $('#driverAvailable').DataTable();
        var table = $('#driverUnavailable').DataTable();

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

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                floatchart();
            }, 500);
        });

        function floatchart() {
            const dataTable1 = new simpleDatatables.DataTable('#driverAvailable', {
                sortable: false,
            });
            const dataTable2 = new simpleDatatables.DataTable('#driverUnavailable', {
                sortable: false,
            });
        }

        new Wizard("#acceptRide", {
            validate: false,
            progress: true
        });

        // quantity start
        function increaseWait(temp) {
            var value = parseInt(document.getElementById(temp).value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            document.getElementById(temp).value = value;
        }

        function decreaseWait(temp) {
            var value = parseInt(document.getElementById(temp).value, 10);
            value = isNaN(value) ? 0 : value;
            value < 1 ? (value = 1) : '';
            value--;
            document.getElementById(temp).value = value;
        }

        function increaseKm(temp) {
            var value = parseInt(document.getElementById(temp).value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            document.getElementById(temp).value = value;
        }

        function decreaseKm(temp) {
            var value = parseInt(document.getElementById(temp).value, 10);
            value = isNaN(value) ? 0 : value;
            value < 1 ? (value = 1) : '';
            value--;
            document.getElementById(temp).value = value;
        }
        // quantity end
    </script>
    <!-- [Page Specific JS] end -->
@endsection
