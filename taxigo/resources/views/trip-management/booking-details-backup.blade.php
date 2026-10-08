@extends('layouts.main')

@section('title', 'Booking Details')
@section('breadcrumb-item', 'Trip Management')

@section('breadcrumb-item-active', 'Booking Details')

@section('css')

    <!-- GLightBox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- Popup Animation -->
    <link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('build/css/new-style.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

    <div class="row">
        <!-- DOM/Jquery table start -->
        <div class="col-sm-12">
            <div class="card table-full-view">
                <div class="card-header">
                    <div class="d-flex .align-items-center justify-content-between">
                        <h5>Booking Details</h5>
                        <!-- <button class="btn btn-sm btn-outline-secondary" title="Full View"><i class="feather icon-maximize-2"></i></button> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive upcoming-trips">
                        <table id="newBooking" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Booking Date</th>
                                    <th>Pickup Date/Time</th>
                                    <th>Booking ID</th>
                                    <th>Trip Type</th>
                                    <th>Pickup From</th>
                                    <th>Drop To</th>
                                    <th>Pickup Address</th>
                                    <th>Drop-off Address</th>
                                    <th>Cab Type</th>
                                    <th>SOS</th>
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
                                        <div class="text-primary">Trip Start OTP</div>
                                    </th>
                                    <th>
                                        <div class="text-primary">Fleet Operator</div>
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
                                    <th>Full Payment</th>
                                    <th>
                                        <div style="color:green">Total Amount</div>
                                    </th>
                                    <th>
                                        <div class="text-danger">Refund</div>
                                    </th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01-12-2024</td>
                                    <td>02-12-2024 <span class="fw-bolder text-danger">09:30 AM</span></td>
                                    <td>BK1001</td>
                                    <td>In-City Rides</td>
                                    <td>Goa Airport</td>
                                    <td>Panaji</td>
                                    <td>Goa Airport Terminal 1</td>
                                    <td>Panaji City Center</td>
                                    <td>Sedan</td>
                                    <td>--</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div style="color:purple" class="fw-bolder">John Doe</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Male</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">+91 9876543210</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Goa</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">India</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>Rs.300</td>
                                    <td>--</td>
                                    <td>
                                        <div style="color:green" class="fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bolder">--</div>
                                    </td>
                                    <td>--</td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#driverDetails">Assign Driver</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>01-12-2024</td>
                                    <td>02-12-2024 <span class="fw-bolder text-danger">09:30 AM</span></td>
                                    <td>BK1001</td>
                                    <td>Airport Drop</td>
                                    <td>Goa Airport</td>
                                    <td>Panaji</td>
                                    <td>Goa Airport Terminal 1</td>
                                    <td>Panaji City Center</td>
                                    <td>Sedan</td>
                                    <td>--</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div style="color:purple" class="fw-bolder">John Doe</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Male</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">+91 9876543210</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Goa</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">India</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">--</div>
                                    </td>
                                    <td>--</td>
                                    <td>Rs.1200</td>
                                    <td>
                                        <div style="color:green" class="fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bolder">--</div>
                                    </td>
                                    <td>--</td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#driverDetails">Assign Driver</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>03-12-2024</td>
                                    <td>04-12-2024 <span class="fw-bolder text-danger">08:15 PM</span></td>
                                    <td>BK1002</td>
                                    <td>Airport Drop</td>
                                    <td>Goa Airport</td>
                                    <td>Margao</td>
                                    <td>Goa Airport Terminal 2</td>
                                    <td>Margao Market Street</td>
                                    <td>SUV</td>
                                    <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#SOSDetails">SOS</button></td>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div style="color:purple" class="fw-bolder">Jane Smith</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Female</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">+91 9876543211</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Goa</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">India</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">9632</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Operator-1</div>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div class="text-primary fw-bolder">Michael Brown</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">9898298982</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Hyundai Creta</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">White</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">GA01X1234</div>
                                    </td>
                                    <td>Rs.200</td>
                                    <td>--</td>
                                    <td>
                                        <div style="color:green" class="fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bolder">--</div>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-warning text-decoration-none">Intransit</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                                                                                                                                                                                                                                                                                                                        data-bs-target="#driverDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-danger passanger-no-show">Passenger
                                            No-Show</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>03-12-2024</td>
                                    <td>04-12-2024 <span class="fw-bolder text-danger">08:15 PM</span></td>
                                    <td>BK1002</td>
                                    <td>Airport Pickup</td>
                                    <td>Goa Airport</td>
                                    <td>Margao</td>
                                    <td>Goa Airport Terminal 2</td>
                                    <td>Margao Market Street</td>
                                    <td>SUV</td>
                                    <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#SOSDetails">SOS</button></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div style="color:purple" class="fw-bolder">Jane Smith</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Female</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">+91 9876543211</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Goa</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">India</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">9632</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Operator-1</div>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div class="text-primary fw-bolder">Michael Brown</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">9898298982</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Hyundai Creta</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Gray</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">GA01X1254</div>
                                    </td>
                                    <td>--</td>
                                    <td>Rs.2000</td>
                                    <td>
                                        <div style="color:green" class="fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bolder">--</div>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-warning text-decoration-none">Intransit</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                                                                                                                                                                                                                                                                                                                        data-bs-target="#driverDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-danger passanger-no-show">Passenger
                                            No-Show</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>05-12-2024</td>
                                    <td>06-12-2024 <span class="fw-bolder text-danger">2:45 PM</span></td>
                                    <td>BK1003</td>
                                    <td>In-City Rides</td>
                                    <td>Goa Airport</td>
                                    <td>Vasco</td>
                                    <td>Goa Airport Exit Gate</td>
                                    <td>Vasco City Market</td>
                                    <td>Hatchback</td>
                                    <td>--</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div style="color:purple" class="fw-bolder">Michael Brown</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Male</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">+91 9876543212</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Goa</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">India</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">8520</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Operator-1</div>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div class="text-primary fw-bolder">Michael Brown</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">9898298982</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Hyundai Creta</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">White</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">GA01X1234</div>
                                    </td>
                                    <td>Rs.250</td>
                                    <td>--</td>
                                    <td>
                                        <div style="color:green" class="fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bolder">Rs.250</div>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-danger text-decoration-none">Cancelled</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                                                                                                                                                                                                                                                                                                                        data-bs-target="#driverDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-info refund-amount">Refund Amount</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>05-12-2024</td>
                                    <td>06-12-2024 <span class="fw-bolder text-danger">2:45 PM</span></td>
                                    <td>BK1003</td>
                                    <td>Airport Pickup</td>
                                    <td>Goa Airport</td>
                                    <td>Vasco</td>
                                    <td>Goa Airport Exit Gate</td>
                                    <td>Vasco City Market</td>
                                    <td>Hatchback</td>
                                    <td>--</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div style="color:purple" class="fw-bolder">Michael Brown</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Male</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">+91 9876543212</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Goa</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">India</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">8520</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Operator-1</div>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div class="text-primary fw-bolder">Michael Brown</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">9898298982</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Hyundai Creta</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">White</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">GA01X1234</div>
                                    </td>
                                    <td>--</td>
                                    <td>Rs.1200</td>
                                    <td>
                                        <div style="color:green" class="fw-bolder">--</div>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bolder">Rs.1200</div>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-danger text-decoration-none">Cancelled</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                                                                                                                                                                                                                                                                                                                        data-bs-target="#driverDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-info refund-amount">Refund Amount</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>09-12-2024</td>
                                    <td>10-12-2024 <span class="fw-bolder text-danger">11:45 AM</span></td>
                                    <td>BK1005</td>
                                    <td>In-City Rides</td>
                                    <td>Goa Airport</td>
                                    <td>Calangute</td>
                                    <td>Goa Airport Parking Lot</td>
                                    <td>Calangute Beach Road</td>
                                    <td>SUV</td>
                                    <td>--</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div style="color:purple" class="fw-bolder">Robert Wilson</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Male</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">+91 9876543214</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">Goa</div>
                                    </td>
                                    <td>
                                        <div style="color:purple" class="fw-bolder">India</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">8520</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Operator-1</div>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            <div class="text-primary fw-bolder">Michael Brown</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">9898298982</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">Hyundai Creta</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">White</div>
                                    </td>
                                    <td>
                                        <div class="text-primary fw-bolder">GA01X1234</div>
                                    </td>
                                    <td>Rs.350</td>
                                    <td>--</td>
                                    <td>
                                        <span style="color:green" class="fw-bolder">Rs.2200</span>
                                        <i style="color:green" class="ti ti-info-circle" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                            data-bs-html="true"
                                            title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. {5} Kms = Rs.150</br> Waiting Charges {30} Mins = Rs.100</br> Part Payment = Rs.150</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/- </br> ------------------------------------</br> Transection ID = TRX2580">
                                        </i>
                                    </td>
                                    <td>
                                        <div class="text-danger fw-bolder">--</div>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-success text-decoration-none">Completed</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                                                                                                                                                                                                                                                                                                                        data-bs-target="#driverDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-secondary" data-bs-toggle="modal"
                                            data-bs-target="#invoiceModel">Download Invoice</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                            </tbody>
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
                <div class="modal-body p-0">
                    <div class="row p-4">
                        <div class="col-12">
                            <div class="mb-3 verticle-line">
                                <label class="form-label">Search Driver / Cab Number / Mobile Number</label>
                                <select class="form-control" data-trigger name="choices-single-default"
                                    id="choices-single-default">
                                    <option value="">Select</option>
                                    <option value="Driver 1">Driver 1 / GA 03 BY 1234 / 9898198981</option>
                                    <option value="Driver 2">Driver 2 / GA 03 BY 1234 / 9898198981</option>
                                    <option value="Driver 3">Driver 3 / GA 03 BY 1234 / 9898198981</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary bs-success-ico">Assign Driver</button>
                </div>
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
                                                <p class="text-muted mb-1 text-start">Part Payment</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">- Rs.300.00</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-muted mb-1 text-start">Discount</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">- Rs.0.00</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-muted mb-1 text-start">Taxes</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">- Rs.0.00</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-start">Total :</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="f-w-600 mb-1 text-end">Rs.1711.00</p>
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

                        <div class="dt-responsive">
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

@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->

    <!-- GLightBox -->
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    <!-- Sweet Alert -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <!-- DataTable JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Dropdown with Search -->
    <script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

    <script>
        // DataTable JS
        var table = $('#newBooking').DataTable();
        var table = $('#sosDetails').DataTable();

        // Select Multiple JS
        document.addEventListener('DOMContentLoaded', function () {
            var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
                removeItemButton: true
            });
        });

        // Select with Search
        document.addEventListener('DOMContentLoaded', function () {
            var genericExamples = document.querySelectorAll('[data-trigger]');
            for (i = 0; i < genericExamples.length; ++i) {
                var element = genericExamples[i];
                new Choices(element, {
                    placeholderValue: 'Search driver',
                    searchPlaceholderValue: 'Search Driver / Cab Number / Mobile Number'
                });
            }
        })

        // GLightBox
        const lightbox = GLightbox({
            touchNavigation: true,
            loop: true,
            width: "90vw",
            height: "90vh"
        });

        // Success Alert
        document.querySelector('.bs-success-ico').addEventListener('click', function () {
            Swal.fire({
                icon: 'success',
                title: 'Driver Assigned Successfully'
            });
        });

        // Passanger No Show Popup
        document.querySelectorAll('.passanger-no-show').forEach(function (element) {
            element.addEventListener('click', function () {
                Swal.fire({
                    icon: 'warning',
                    html: 'Are You Sure! The Passanger is No Show',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                })
            });
        });

        // Refund Amount Popup
        document.querySelectorAll('.refund-amount').forEach(function (element) {
            element.addEventListener('click', function () {
                Swal.fire({
                    icon: 'warning',
                    html: 'Are You Sure! You want to refund the amount',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                })
            });
        });
    </script>

    <!-- [Page Specific JS] end -->
@endsection
