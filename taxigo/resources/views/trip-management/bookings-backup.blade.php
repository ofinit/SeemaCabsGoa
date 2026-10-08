@extends('layouts.main')

@section('title', 'Bookings')
@section('breadcrumb-item', 'Bookings Management')

@section('breadcrumb-item-active', 'Bookings')

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
                        <h5>Bookings</h5>
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
                                    <th>Trip Start OTP</th>
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
                                    <th>Addl. Charges</th>
                                    <th>Full Payment</th>
                                    <th>Total Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01-12-2024</td>
                                    <td>02-12-2024 09:30</td>
                                    <td>BK1001</td>
                                    <td>Airport to City</td>
                                    <td>--</td>
                                    <td>Goa Airport</td>
                                    <td>Panaji</td>
                                    <td>Goa Airport Terminal 1</td>
                                    <td>Panaji City Center</td>
                                    <td>Sedan</td>
                                    <td>Maruti Dzire</td>
                                    <td>Petrol</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            John Doe
                                        </div>
                                    </td>
                                    <td>Male</td>
                                    <td>+91 9876543210</td>
                                    <td>India</td>
                                    <td>Goa</td>
                                    <td>Rs.1500</td>
                                    <td>--</td>
                                    <td>Rs.150</td>
                                    <td>Rs.1500</td>
                                    <td>TXN12345</td>
                                    <td>--</td>
                                    <td>
                                        <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#customerDetails">Assign Driver</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>03-12-2024</td>
                                    <td>04-12-2024 08:15</td>
                                    <td>BK1002</td>
                                    <td>Airport to City</td>
                                    <td>--</td>
                                    <td>Goa Airport</td>
                                    <td>Margao</td>
                                    <td>Goa Airport Terminal 2</td>
                                    <td>Margao Market Street</td>
                                    <td>SUV</td>
                                    <td>Hyundai Creta</td>
                                    <td>Diesel</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>--</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            Jane Smith
                                        </div>
                                    </td>
                                    <td>Female</td>
                                    <td>+91 9876543211</td>
                                    <td>India</td>
                                    <td>Goa</td>
                                    <td>Rs.2000</td>
                                    <td>--</td>
                                    <td>Rs.200</td>
                                    <td>Rs.2000</td>
                                    <td>TXN12346</td>
                                    <td><a href="#" class="btn btn-sm btn-warning text-decoration-none">Intransit</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                        data-bs-target="#customerDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-danger passanger-no-show">Passenger
                                            No-Show</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>05-12-2024</td>
                                    <td>06-12-2024 14:45</td>
                                    <td>BK1003</td>
                                    <td>Airport to City</td>
                                    <td>8520</td>
                                    <td>Goa Airport</td>
                                    <td>Vasco</td>
                                    <td>Goa Airport Exit Gate</td>
                                    <td>Vasco City Market</td>
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
                                            Michael Brown
                                        </div>
                                    </td>
                                    <td>9898298982</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            Michael Brown
                                        </div>
                                    </td>
                                    <td>Male</td>
                                    <td>+91 9876543212</td>
                                    <td>India</td>
                                    <td>Goa</td>
                                    <td>Rs.1200</td>
                                    <td>Rs.200</td>
                                    <td>Rs.150</td>
                                    <td>Rs.1200</td>
                                    <td>TXN12347</td>
                                    <td><a href="#" class="btn btn-sm btn-danger text-decoration-none">Cancelled</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                        data-bs-target="#customerDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-info">Refund Amount</a>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a> -->
                                    </td>
                                </tr>
                                <tr>
                                    <td>09-12-2024</td>
                                    <td>10-12-2024 11:45</td>
                                    <td>BK1005</td>
                                    <td>Airport to City</td>
                                    <td>8520</td>
                                    <td>Goa Airport</td>
                                    <td>Calangute</td>
                                    <td>Goa Airport Parking Lot</td>
                                    <td>Calangute Beach Road</td>
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
                                            Michael Brown
                                        </div>
                                    </td>
                                    <td>9898298982</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                                <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                    class="img-radius customer-profile-image" />
                                            </a>
                                            Robert Wilson
                                        </div>
                                    </td>
                                    <td>Male</td>
                                    <td>+91 9876543214</td>
                                    <td>India</td>
                                    <td>Goa</td>
                                    <td>Rs.2200</td>
                                    <td>Rs.400</td>
                                    <td>Rs.200</td>
                                    <td>Rs.2200</td>
                                    <td>TXN12349</td>
                                    <td><a href="#" class="btn btn-sm btn-success text-decoration-none">Completed</a></td>
                                    <td>
                                        <!-- <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                                                                        data-bs-target="#customerDetails">Assign Driver</a> -->
                                        <a href="#" class="btn btn-sm btn-light-secondary">Download Invoice</a>
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
    <div class="modal fade" id="customerDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    html: 'Are You Sure! There is No Passanger',
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