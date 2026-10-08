@extends('layouts.main')

@section('title', 'Completed Trips Details')
@section('breadcrumb-item', 'Trip Management')

@section('breadcrumb-item-active', 'Completed Trips Details')

@section('css')

<!-- GLightBox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

<!-- Date Range CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Completed Trips Details</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive upcoming-trips">
                    <table id="completedTrip" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Booking Date</th>
                                <th>Pickup Date/Time</th>
                                <th>Booking ID</th>
                                <th>Trip Start OTP</th>
                                <th>Trip Type</th>
                                <th>Pickup From</th>
                                <th>Drop To</th>
                                <th>Pickup Address</th>
                                <th>Drop-off Address</th>
                                <th>Cab Type</th>
                                <th>Cab Model</th>
                                <th>Fuel Type</th>
                                <th>Fleet Operator</th>
                                <th>Driver Name</th>
                                <th>Driver Mobile Number</th>
                                <th>Traveller Name</th>
                                <th>Gender</th>
                                <th>Mobile</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>Part Payment</th>
                                <th>Full Payment</th>
                                <th>Addl. Charges</th>
                                <th>Total Amount</th>
                                <th>Transaction ID</th>
                                <th>Invoice</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-12-2024</td>
                                <td>01-12-2024 10:00 AM</td>
                                <td>BK12345</td>
                                <td>3456</td>
                                <td>Airport to City</td>
                                <td>Goa Airport</td>
                                <td>Calangute</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Calangute Beach Road</td>
                                <td>Sedan</td>
                                <td>Honda City</td>
                                <td>Petrol</td>
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
                                <td>+1 234 567 8901</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rohit Sharma
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876543210</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1500</td>
                                <td>Rs.300</td>
                                <td>Rs.30</td>
                                <td>Rs.1500</td>
                                <td>TXN12345</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>02-12-2024</td>
                                <td>02-12-2024 11:30 AM</td>
                                <td>BK12346</td>
                                <td>5678</td>
                                <td>City to City</td>
                                <td>Panjim</td>
                                <td>Vasco</td>
                                <td>Panjim Bus Stand</td>
                                <td>Vasco Railway Station</td>
                                <td>SUV</td>
                                <td>Mahindra XUV500</td>
                                <td>Diesel</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Jane Smith
                                    </div>
                                </td>
                                <td>+1 987 654 3210</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Priya Singh
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9988776655</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.2000</td>
                                <td>Rs.400</td>
                                <td>Rs.40</td>
                                <td>Rs.2000</td>
                                <td>TXN12346</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>03-12-2024</td>
                                <td>03-12-2024 01:00 PM</td>
                                <td>BK12347</td>
                                <td>2345</td>
                                <td>City to Airport</td>
                                <td>Margao</td>
                                <td>Goa Airport</td>
                                <td>Margao Market</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Hatchback</td>
                                <td>Hyundai i20</td>
                                <td>Petrol</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Mike Brown
                                    </div>
                                </td>
                                <td>+1 456 789 1230</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Ankit Verma
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>8765432109</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1200</td>
                                <td>Rs.200</td>
                                <td>Rs.20</td>
                                <td>Rs.1200</td>
                                <td>TXN12347</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>04-12-2024</td>
                                <td>04-12-2024 03:45 PM</td>
                                <td>BK12348</td>
                                <td>9876</td>
                                <td>Airport to City</td>
                                <td>Goa Airport</td>
                                <td>Baga</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Baga Beach</td>
                                <td>Sedan</td>
                                <td>Maruti Ciaz</td>
                                <td>Diesel</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Susan White
                                    </div>
                                </td>
                                <td>+1 321 654 9870</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Shalini Mehra
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9123456780</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1800</td>
                                <td>Rs.300</td>
                                <td>Rs.30</td>
                                <td>Rs.1800</td>
                                <td>TXN12348</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>05-12-2024</td>
                                <td>05-12-2024 09:00 AM</td>
                                <td>BK12349</td>
                                <td>1357</td>
                                <td>City to City</td>
                                <td>Colva</td>
                                <td>Arpora</td>
                                <td>Colva Beach Road</td>
                                <td>Arpora Night Market</td>
                                <td>SUV</td>
                                <td>Toyota Fortuner</td>
                                <td>Diesel</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Chris Green
                                    </div>
                                </td>
                                <td>+1 789 123 4560</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rahul Nair
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9543218760</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.2200</td>
                                <td>Rs.400</td>
                                <td>Rs.40</td>
                                <td>Rs.2200</td>
                                <td>TXN12349</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>06-12-2024</td>
                                <td>06-12-2024 10:30 AM</td>
                                <td>BK12350</td>
                                <td>1122</td>
                                <td>Airport to City</td>
                                <td>Goa Airport</td>
                                <td>North Goa</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Calangute Beach</td>
                                <td>SUV</td>
                                <td>Tata Nexon</td>
                                <td>Diesel</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Emma Blue
                                    </div>
                                </td>
                                <td>+1 654 321 7890</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Sanjay Gupta
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876123456</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1400</td>
                                <td>Rs.150</td>
                                <td>Rs.15</td>
                                <td>Rs.1400</td>
                                <td>TXN12350</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>07-12-2024</td>
                                <td>07-12-2024 02:00 PM</td>
                                <td>BK12351</td>
                                <td>5566</td>
                                <td>City to City</td>
                                <td>Panjim</td>
                                <td>Vasco</td>
                                <td>Panjim Bus Stand</td>
                                <td>Vasco Railway Station</td>
                                <td>Sedan</td>
                                <td>Honda Civic</td>
                                <td>Petrol</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Paul Black
                                    </div>
                                </td>
                                <td>+1 876 543 2109</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Ashok Kumar
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9555443322</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1600</td>
                                <td>Rs.300</td>
                                <td>Rs.30</td>
                                <td>Rs.1600</td>
                                <td>TXN12351</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>08-12-2024</td>
                                <td>08-12-2024 09:30 AM</td>
                                <td>BK12352</td>
                                <td>1234</td>
                                <td>Airport to City</td>
                                <td>Goa Airport</td>
                                <td>Vagator</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Vagator Beach</td>
                                <td>SUV</td>
                                <td>Ford Endeavour</td>
                                <td>Diesel</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Laura Yellow
                                    </div>
                                </td>
                                <td>+1 210 987 6543</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Neha Verma
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9898989898</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.2500</td>
                                <td>Rs.500</td>
                                <td>Rs.50</td>
                                <td>Rs.2500</td>
                                <td>TXN12352</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>09-12-2024</td>
                                <td>09-12-2024 11:15 AM</td>
                                <td>BK12353</td>
                                <td>1123</td>
                                <td>City to Airport</td>
                                <td>Mapusa</td>
                                <td>Goa Airport</td>
                                <td>Mapusa Bus Stand</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Sedan</td>
                                <td>Volkswagen Vento</td>
                                <td>Petrol</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Gary Red
                                    </div>
                                </td>
                                <td>+1 543 210 9876</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Vikas Kumar
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>8765439876</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1700</td>
                                <td>Rs.200</td>
                                <td>Rs.20</td>
                                <td>Rs.1700</td>
                                <td>TXN12353</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>10-12-2024</td>
                                <td>10-12-2024 04:30 PM</td>
                                <td>BK12354</td>
                                <td>7789</td>
                                <td>City to City</td>
                                <td>Vasco</td>
                                <td>Panjim</td>
                                <td>Vasco Railway Station</td>
                                <td>Panjim Bus Stand</td>
                                <td>Hatchback</td>
                                <td>Maruti Swift</td>
                                <td>Petrol</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Alice Brown
                                    </div>
                                </td>
                                <td>+1 098 765 4321</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rita Patel
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9977554433</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1700</td>
                                <td>Rs.200</td>
                                <td>Rs.20</td>
                                <td>Rs.1700</td>
                                <td>TXN12353</td>
                                <td><a href="#!" class="btn btn-link">Download</a></td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Details Model -->
<div class="modal fade" id="customerDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">View Trip Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-1">
                    <div class="col-12">
                        <h5>Trip Details</h5>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Booking Date</h6>
                        <p class="cab-model-location">01-12-2024</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Pickup Date & Time</h6>
                        <p class="cab-model-location">2024-12-01 10:00 AM</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Booking ID</h6>
                        <p class="cab-model-location">BK12345</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Trip Type</h6>
                        <p class="cab-model-location">Airport to City</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Pickup From</h6>
                        <p class="cab-model-location">Goa Airport</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Drop To</h6>
                        <p class="cab-model-location">Calangute</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Pickup Address</h6>
                        <p class="cab-model-location">Goa Airport, Dabolim</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Drop-off Address</h6>
                        <p class="cab-model-location">Calangute Beach Road</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
                <div class="row g-1">
                    <div class="col-12">
                        <h5>Cab Details</h5>
                    </div>
                    <div class="col-12 mb-1">
                        <h6 class="mb-1">Driver Profile Image</h6>
                        <div class="main-photo img-box">
                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image" />
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Cab Zone</h6>
                        <p class="cab-model-location">Panaji</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Cab Number</h6>
                        <p class="cab-model-location">GA01X1234</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Cab Type</h6>
                        <p class="cab-model-location">Sedan</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Cab Model</h6>
                        <p class="cab-model-location">Dzire, Etios or similar</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Model Name</h6>
                        <p class="cab-model-location">Honda City</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Color</h6>
                        <p class="cab-model-location">Gray</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Fuel Type</h6>
                        <p class="cab-model-location">Diesel</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Driver Name</h6>
                        <p class="cab-model-location">John Doe</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Driver Mobile Number</h6>
                        <p class="cab-model-location">+1 234 567 8901</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
                <div class="row g-1">
                    <div class="col-12">
                        <h5>Customer Details</h5>
                    </div>

                    <div class="col-12 mb-1">
                        <h6 class="mb-1">Customer Profile Image</h6>
                        <div class="main-photo img-box">
                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image" />
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Name</h6>
                        <p class="cab-model-location">Priya Sharma</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Gender</h6>
                        <p class="cab-model-location">Female</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Country</h6>
                        <p class="cab-model-location">India</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">State</h6>
                        <p class="cab-model-location">Goa</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Email ID</h6>
                        <p class="cab-model-location">priya.sharma@example.in</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Mobile Number</h6>
                        <p class="cab-model-location">+91-99876-54321</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
                <div class="row g-1">
                    <div class="col-12">
                        <h5>SOS | <span class="sos-action">(Called Police / Alerted Contacts)</span></h5>
                    </div>
                    <div class="col-12">
                        No Records Found..
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
                <div class="row g-1">
                    <div class="col-12">
                        <h5>Ratings</h5>
                    </div>
                    <div class="col-12">
                        This ride was soo good...
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-start gap-2">
                            <div class="rating-img">
                                <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                            </div>
                            <div class="rating-img">
                                <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                            </div>
                            <div class="rating-img">
                                <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                            </div>
                            <div class="rating-img">
                                <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                            </div>
                            <div class="rating-img">
                                <img src="{{ URL::asset('build/images/rating/ratings-gray.svg') }}"
                                    class="gray-rating-img" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>
                <div class="row g-1">
                    <div class="col-12">
                        <h5>Invoice Details</h5>
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
                        <div class="col-12">
                            <div class="invoice-total ms-auto">
                                <div class="row">
                                    <div class="col-6">
                                        <p class="text-muted mb-1 text-start">Discount</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="f-w-600 mb-1 text-end">Rs.0.00</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1 text-start">Taxes</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="f-w-600 mb-1 text-end">Rs.0.00</p>
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
                    </div>
                    <div class="col-12">
                        <button class="btn btn-sm btn-light-secondary">Download Invoice</button>
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

<!-- DataTable JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

<!-- GLightBox -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<!-- Date Range JS -->
<script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>

<script>

    // Data Table
    var table = $('#completedTrip').DataTable();

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

    // Date Range 
    const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
        buttonClass: 'btn'
    });
</script>
<!-- [Page Specific JS] end -->
@endsection