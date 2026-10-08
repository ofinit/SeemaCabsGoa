@extends('layouts.main')

@section('title', 'SOS')
@section('breadcrumb-item', 'Customer Management')

@section('breadcrumb-item-active', 'SOS')

@section('css')

<!-- GLightBox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <!-- DOM/Jquery table start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>SOS Details</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive upcoming-trips">
                    <table id="sosDetailsTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Pickup Date/Time</th>
                                <th>Booking ID</th>
                                <th>Pickup Address</th>
                                <th>Drop-off Address</th>
                                <th>Cab Type</th>
                                <th>Cab Model</th>
                                <th>Fleet Operator</th>
                                <th>Driver Name</th>
                                <th>Driver Mobile Number</th>
                                <th>Traveller Name</th>
                                <th>Gender</th>
                                <th>Mobile</th>
                                <th>SOS</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-12-01 10:00 AM</td>
                                <td>BK12345</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Calangute Beach Road</td>
                                <td>Sedan</td>
                                <td>Honda City</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 234 567 8901</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876543210</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-02 11:30 AM</td>
                                <td>BK12346</td>
                                <td>Panjim Bus Stand</td>
                                <td>Vasco Railway Station</td>
                                <td>SUV</td>
                                <td>Mahindra XUV500</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 987 654 3210</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9988776655</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-success">Resolved</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-03 01:00 PM</td>
                                <td>BK12347</td>
                                <td>Margao Market</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Hatchback</td>
                                <td>Hyundai i20</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 456 789 1230</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>8765432109</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-success">Resolved</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-04 03:45 PM</td>
                                <td>BK12348</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Baga Beach</td>
                                <td>Sedan</td>
                                <td>Maruti Ciaz</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 321 654 9870</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9123456780</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-05 09:00 AM</td>
                                <td>BK12349</td>
                                <td>Colva Beach Road</td>
                                <td>Arpora Night Market</td>
                                <td>SUV</td>
                                <td>Toyota Fortuner</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 789 123 4560</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9543218760</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-success">Resolved</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-06 10:30 AM</td>
                                <td>BK12350</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Calangute Beach</td>
                                <td>SUV</td>
                                <td>Tata Nexon</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 654 321 7890</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9876123456</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-07 02:00 PM</td>
                                <td>BK12351</td>
                                <td>Panjim Bus Stand</td>
                                <td>Vasco Railway Station</td>
                                <td>Sedan</td>
                                <td>Honda Civic</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 876 543 2109</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>9555443322</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-success">Resolved</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-08 09:30 AM</td>
                                <td>BK12352</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Vagator Beach</td>
                                <td>SUV</td>
                                <td>Ford Endeavour</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 210 987 6543</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9898989898</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-success">Resolved</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-09 11:15 AM</td>
                                <td>BK12353</td>
                                <td>Mapusa Bus Stand</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Sedan</td>
                                <td>Volkswagen Vento</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 543 210 9876</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>8765439876</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-success">Resolved</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-12-10 04:30 PM</td>
                                <td>BK12354</td>
                                <td>Vasco Railway Station</td>
                                <td>Panjim Bus Stand</td>
                                <td>Hatchback</td>
                                <td>Maruti Swift</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>+1 098 765 4321</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>9977554433</td>
                                <td><button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#sosLocation">Emergency</button></td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#SOSDetails"><i class="feather icon-eye"></i></a>
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
    <!-- DOM/Jquery table end -->

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

                        <div class="dt-responsive">
                            <table id="sosByCustomer" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Mobile Number</th>
                                        <th>Cab Number</th>
                                        <th>Driver Name</th>
                                        <th>Driver Mobile Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                    class="glightbox">
                                                    <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        alt="image" class="img-radius customer-profile-image" />
                                                </a>
                                                Ankit Singh
                                            </div>
                                        </td>
                                        <td>9898198981</td>
                                        <td>GA 03 AG 1234</td>
                                        <td>Alex D'Souza</td>
                                        <td>9898298982</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="dt-responsive">
                            <table id="sosByDriver" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Cab Number</th>
                                        <th>Driver Name</th>
                                        <th>Mobile Numebr</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>GA 03 AG 1234</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                    class="glightbox">
                                                    <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        alt="image" class="img-radius customer-profile-image" />
                                                </a>
                                                Alex D'Souza
                                            </div>
                                        </td>
                                        <td>9898298982</td>
                                    </tr>
                                </tbody>
                            </table>
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
                                        <td>2024-12-10</td>
                                        <td>04:30 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-10</td>
                                        <td>04:29 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-10</td>
                                        <td>04:28 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-10</td>
                                        <td>04:27 PM</td>
                                        <td><a
                                                href="https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5">https://maps.app.goo.gl/DKBmyQVAWPz19Jyx5</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2024-12-10</td>
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
</div>
</div>

@endsection

@section('scripts')
<!-- [Page Specific JS] start -->

<!-- SweetAlert JS -->
<script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

<!-- DataTable JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

<!-- GLightBox -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<script>

    // Data Table
    var table = $('#sosDetailsTable').DataTable();
    var table = $('#sosByCustomer').DataTable();
    var table = $('#sosByDriver').DataTable();
    var table = $('#sosDetails').DataTable();

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