@extends('layouts.main')

@section('title', 'In-Transit Trips Details')
@section('breadcrumb-item', 'Trip Management')

@section('breadcrumb-item-active', 'In-Transit Trips Details')

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
                <h5>In Transit Trip Details</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive upcoming-trips">
                    <table id="inTransitTrip" class="table table-striped table-bordered nowrap">
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
                                <th>Total Amount</th>
                                <th>Transaction ID</th>
                                <th>Action</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-12-2024</td>
                                <td>01-12-2024 08:00 AM</td>
                                <td>GOA12301</td>
                                <td>751528</td>
                                <td>Airport to City</td>
                                <td>Dabolim Airport</td>
                                <td>Calangute Beach</td>
                                <td>Dabolim Airport, Vasco, Goa</td>
                                <td>Calangute Beach Road, Goa</td>
                                <td>Sedan</td>
                                <td>Toyota Etios</td>
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
                                        Rahul Mehta
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876543210</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1500</td>
                                <td>Rs.300</td>
                                <td>Rs.1500</td>
                                <td>TXN12345</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>03-12-2024</td>
                                <td>03-12-2024 10:00 AM</td>
                                <td>GOA12302</td>
                                <td>751528</td>
                                <td>City to Airport</td>
                                <td>Panjim City</td>
                                <td>Dabolim Airport</td>
                                <td>Fontainhas, Panjim, Goa</td>
                                <td>Dabolim International Terminal, Vasco</td>
                                <td>SUV</td>
                                <td>Hyundai Creta</td>
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
                                        Priya Sharma
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9876543211</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.2000</td>
                                <td>Rs.400</td>
                                <td>Rs.2000</td>
                                <td>TXN12346</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>04-12-2024</td>
                                <td>04-12-2024 05:00 PM</td>
                                <td>GOA12303</td>
                                <td>751528</td>
                                <td>City to City</td>
                                <td>Anjuna Beach</td>
                                <td>Vagator Beach</td>
                                <td>Anjuna Market Road, Anjuna</td>
                                <td>Vagator Beach Road, Vagator</td>
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
                                        Aman Verma
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876543212</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1200</td>
                                <td>Rs.200</td>
                                <td>Rs.1200</td>
                                <td>TXN12347</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>05-12-2024</td>
                                <td>05-12-2024 11:30 AM</td>
                                <td>GOA12304</td>
                                <td>751528</td>
                                <td>Airport to City</td>
                                <td>Dabolim Airport</td>
                                <td>Baga Beach</td>
                                <td>Dabolim Airport, Vasco, Goa</td>
                                <td>Tito's Lane, Baga, Goa</td>
                                <td>SUV</td>
                                <td>Tata Harrier</td>
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
                                        Sneha Patil
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9876543213</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1800</td>
                                <td>Rs.300</td>
                                <td>Rs.1800</td>
                                <td>TXN12348</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>06-12-2024</td>
                                <td>06-12-2024 09:15 AM</td>
                                <td>GOA12305</td>
                                <td>751528</td>
                                <td>City to City</td>
                                <td>Calangute Beach</td>
                                <td>Fort Aguada</td>
                                <td>Calangute Main Road, Goa</td>
                                <td>Fort Aguada Road, Sinquerim</td>
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
                                        Rohan Joshi
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876543214</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.2200</td>
                                <td>Rs.400</td>
                                <td>Rs.2200</td>
                                <td>TXN12349</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>07-12-2024</td>
                                <td>07-12-2024 08:00 PM</td>
                                <td>GOA12306</td>
                                <td>751528</td>
                                <td>City to Airport</td>
                                <td>Arambol Beach</td>
                                <td>Dabolim Airport</td>
                                <td>Arambol Main Beach Road, Goa</td>
                                <td>Dabolim Departure Terminal, Vasco</td>
                                <td>SUV</td>
                                <td>Mahindra Scorpio</td>
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
                                        Tanvi Shetty
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9876543215</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1400</td>
                                <td>Rs.150</td>
                                <td>Rs.1400</td>
                                <td>TXN12350</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>08-12-2024</td>
                                <td>08-12-2024 03:00 PM</td>
                                <td>GOA12307</td>
                                <td>751528</td>
                                <td>Airport to City</td>
                                <td>Dabolim Airport</td>
                                <td>Palolem Beach</td>
                                <td>Dabolim Arrival Terminal, Vasco</td>
                                <td>Palolem Beach Road, Canacona</td>
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
                                        Vivek Sharma
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876543216</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1600</td>
                                <td>Rs.300</td>
                                <td>Rs.1600</td>
                                <td>TXN12351</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>09-12-2024</td>
                                <td>09-12-2024 06:45 AM</td>
                                <td>GOA12308</td>
                                <td>751528</td>
                                <td>City to City</td>
                                <td>Panjim City</td>
                                <td>Colva Beach</td>
                                <td>Church Square, Panjim</td>
                                <td>Colva Beach Road, Colva</td>
                                <td>Hatchback</td>
                                <td>Hyundai i20</td>
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
                                        Anita Rao
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9876543217</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.2500</td>
                                <td>Rs.500</td>
                                <td>Rs.2500</td>
                                <td>TXN12352</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>10-12-2024</td>
                                <td>10-12-2024 07:30 PM</td>
                                <td>GOA12309</td>
                                <td>751528</td>
                                <td>Airport to City</td>
                                <td>Dabolim Airport</td>
                                <td>Reis Magos Fort</td>
                                <td>Dabolim Airport, Vasco, Goa</td>
                                <td>Reis Magos Road, Bardez</td>
                                <td>SUV</td>
                                <td>MG Hector</td>
                                <td>Diesel</td>
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
                                        Karthik Nair
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876543218</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1700</td>
                                <td>Rs.200</td>
                                <td>Rs.1700</td>
                                <td>TXN12353</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                            <tr>
                                <td>10-12-2024</td>
                                <td>10-12-2024 08:30 PM</td>
                                <td>GOA12310</td>
                                <td>751528</td>
                                <td>City to City</td>
                                <td>Butterfly Beach</td>
                                <td>Tambdi Surla Temple</td>
                                <td>Butterfly Beach Road, Goa</td>
                                <td>Tambdi Surla, Bhagwan Mahavir</td>
                                <td>Sedan</td>
                                <td>Skoda Rapid</td>
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
                                        Meera Kapoor
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9876543219</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Rs.1700</td>
                                <td>Rs.200</td>
                                <td>Rs.1700</td>
                                <td>TXN12353</td>
                                <td><button class="btn btn-sm btn-light-danger">Passenger No-Show</button></td>
                                <!-- <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#customerDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
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

<script>

    // Data Table
    var table = $('#inTransitTrip').DataTable();

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

</script>
<!-- [Page Specific JS] end -->
@endsection