@extends('layouts.main')

@section('title', 'Pending Ads')
@section('breadcrumb-item', 'Advertisements')

@section('breadcrumb-item-active', 'Pending Ads')

@section('css')

    <!-- GLightBox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- Popup Animation -->
    <link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">

    <link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>View Pending Ads Details</h5>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="customerAdOrders" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Gender</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>Mobile Number</th>
                                    <th>Email ID</th>
                                    <th>Ad Screen</th>
                                    <th>Ad Type</th>
                                    <th>Ad Placement Preview</th>
                                    <th>Ad Image</th>
                                    <th>Banner URL</th>
                                    <th>Gender</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>Live Location</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>25-11-2024</td>
                                    <td>Ram Singh</td>
                                    <td>Male</td>
                                    <td>India</td>
                                    <td>Goa</td>
                                    <td>+91 9874563210</td>
                                    <td>ramsingh123@gmail.com</td>
                                    <td>Home Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/home-box-preview.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/home-box-preview.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel1.com"
                                            target="_blank">https://www.examplehotel1.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Goa</td>
                                    <td>Goa</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>12:00 AM</td>
                                    <td>12:00 PM</td>
                                    <td>Rs.1799 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>26-11-2024</td>
                                    <td>Neha Sharma</td>
                                    <td>Female</td>
                                    <td>India</td>
                                    <td>Mumbai</td>
                                    <td>+91 9876543210</td>
                                    <td>neha.sharma@gmail.com</td>
                                    <td>Booking Conf. Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/confirmed-book-preview.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/confirmed-book-preview.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel2.com"
                                            target="_blank">https://www.examplehotel2.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Mumbai</td>
                                    <td>Mumbai</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>10:00 AM</td>
                                    <td>10:00 PM</td>
                                    <td>Rs.2499 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>27-11-2024</td>
                                    <td>Arjun Verma</td>
                                    <td>Male</td>
                                    <td>India</td>
                                    <td>Delhi</td>
                                    <td>+91 9877634567</td>
                                    <td>arjun.verma@gmail.com</td>
                                    <td>Finding Taxi Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/finding-taxi-box-1.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/finding-taxi-box-1.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel3.com"
                                            target="_blank">https://www.examplehotel3.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Delhi</td>
                                    <td>Delhi</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>06:00 AM</td>
                                    <td>06:00 PM</td>
                                    <td>Rs.1599 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>28-11-2024</td>
                                    <td>Amit Singh</td>
                                    <td>Male</td>
                                    <td>India</td>
                                    <td>Chennai</td>
                                    <td>+91 9882345678</td>
                                    <td>amit.singh@gmail.com</td>
                                    <td>Driver Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/your-driver-preview.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/your-driver-preview.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel4.com"
                                            target="_blank">https://www.examplehotel4.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Chennai</td>
                                    <td>Chennai</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>07:00 AM</td>
                                    <td>07:00 PM</td>
                                    <td>Rs.1999 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>29-11-2024</td>
                                    <td>Preeti Kumari</td>
                                    <td>Female</td>
                                    <td>India</td>
                                    <td>Kolkata</td>
                                    <td>+91 9891234567</td>
                                    <td>preeti.kumari@gmail.com</td>
                                    <td>Home Screen Ad</td>
                                    <td>Leaderboard-1</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/home-leader-1.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/home-leader-1.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/leader-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/leader-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel5.com"
                                            target="_blank">https://www.examplehotel5.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Kolkata</td>
                                    <td>Kolkata</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>09:00 AM</td>
                                    <td>09:00 PM</td>
                                    <td>Rs.2199 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>30-11-2024</td>
                                    <td>Suresh Kumar</td>
                                    <td>Male</td>
                                    <td>India</td>
                                    <td>Pune</td>
                                    <td>+91 9883344556</td>
                                    <td>suresh.kumar@gmail.com</td>
                                    <td>Rating Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/after-rating-preview.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/after-rating-preview.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel6.com"
                                            target="_blank">https://www.examplehotel6.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Pune</td>
                                    <td>Pune</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>11:00 AM</td>
                                    <td>11:00 PM</td>
                                    <td>Rs.1499 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>01-12-2024</td>
                                    <td>Manoj Yadav</td>
                                    <td>Male</td>
                                    <td>India</td>
                                    <td>Hyderabad</td>
                                    <td>+91 9977665544</td>
                                    <td>manoj.yadav@gmail.com</td>
                                    <td>Booking Conf. Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/confirmed-book-preview.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/confirmed-book-preview.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel7.com"
                                            target="_blank">https://www.examplehotel7.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Hyderabad</td>
                                    <td>Hyderabad</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>08:00 AM</td>
                                    <td>08:00 PM</td>
                                    <td>Rs.1799 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>02-12-2024</td>
                                    <td>Anjali Rao</td>
                                    <td>Female</td>
                                    <td>India</td>
                                    <td>Chandigarh</td>
                                    <td>+91 9977554433</td>
                                    <td>anjali.rao@gmail.com</td>
                                    <td>Driver Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/your-driver-preview.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/your-driver-preview.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel8.com"
                                            target="_blank">https://www.examplehotel8.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Chandigarh</td>
                                    <td>Chandigarh</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>02:00 AM</td>
                                    <td>02:00 PM</td>
                                    <td>Rs.1999 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>03-12-2024</td>
                                    <td>Ravi Kumar</td>
                                    <td>Male</td>
                                    <td>India</td>
                                    <td>Jaipur</td>
                                    <td>+91 9911223344</td>
                                    <td>ravi.kumar@gmail.com</td>
                                    <td>Rating Screen Ad</td>
                                    <td>Box Ad</td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/preview-images/after-rating-preview.png') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/preview-images/after-rating-preview.png') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="advertise-photo">
                                            <a href="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                class="glightbox" data-glightbox="type: image">
                                                <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}"
                                                    alt="image" />
                                            </a>
                                        </div>
                                    </td>
                                    <td><a href="https://www.examplehotel9.com"
                                            target="_blank">https://www.examplehotel9.com</a></td>
                                    <td>All</td>
                                    <td>India</td>
                                    <td>Jaipur</td>
                                    <td>Jaipur</td>
                                    <td>01-12-2024</td>
                                    <td>31-12-2024</td>
                                    <td>03:00 AM</td>
                                    <td>03:00 PM</td>
                                    <td>Rs.2499 INR</td>
                                    <td>
                                        <button class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                            data-bs-target="#renewAd"><i class="feather icon-eye"></i></button>
                                        <a href="#" class="btn btn-sm btn-light-success me-1"><i
                                                class="feather icon-edit"></i></a>
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
    <div class="modal fade" id="renewAd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Customer Ad Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-1">
                        <div class="col-12">
                            <h5>Customer Details</h5>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Name</h6>
                            <p class="cab-model-location">Anjali Rao</p>
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
                            <h6 class="mb-1">Mobile Numebr</h6>
                            <p class="cab-model-location">+91 9874563210</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Email ID</h6>
                            <p class="cab-model-location">anjalirow123@gmail.com</p>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-12">
                            <h5>Advertisement Details</h5>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Ad Date</h6>
                            <p class="cab-model-location">25-11-2024</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Ad Screen</h6>
                            <p class="cab-model-location">Home Screen</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Ad Type</h6>
                            <p class="cab-model-location">Box Ad</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Uploaded Ad Image</h6>
                            <div class="advertise-photo">
                                <a href="{{ URL::asset('build/images/ad-images/leader-ad-2.jpg') }}" class="glightbox"
                                    data-glightbox="type: image">
                                    <img src="{{ URL::asset('build/images/ad-images/leader-ad-2.jpg') }}"
                                        alt="image" />
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Banner URL</h6>
                            <a href="https://www.examplehotel9.com" target="_blank">https://www.examplehotel9.com</a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Ad Placement Preview</h6>
                            <div class="advertise-photo">
                                <a href="{{ URL::asset('build/images/preview-images/home-leader-2.png') }}"
                                    class="glightbox" data-glightbox="type: image">
                                    <img src="{{ URL::asset('build/images/preview-images/home-leader-2.png') }}"
                                        alt="image" />
                                </a>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-12">
                            <h5>Audiance</h5>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">Gender</h6>
                            <p class="cab-model-location">All</p>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">Country</h6>
                            <p class="cab-model-location">India</p>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">State</h6>
                            <p class="cab-model-location">Goa</p>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">Live Location</h6>
                            <p class="cab-model-location">Goa</p>
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="col-12">
                            <h5>Schedule</h5>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">Start Date</h6>
                            <p class="cab-model-location">01-12-2024</p>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">End Date</h6>
                            <p class="cab-model-location">31-12-2024</p>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">Start Time</h6>
                            <p class="cab-model-location">12:00 AM</p>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h6 class="mb-1">End Time</h6>
                            <p class="cab-model-location">12:00 PM</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-12 text-end mt-3">
                        <button class="btn btn-danger me-3">Reject</button>
                        <button class="btn btn-success">Accept</button>
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

    <!-- GLightBox -->
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    <!-- Date Range JS -->
    <script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>

    <!-- DataTable JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        var table = $('#customerAdOrders').DataTable();

        // GLightBox
        const lightbox = GLightbox({
            touchNavigation: true,
            loop: true,
            width: "90vw",
            height: "90vh"
        });

        // Delete Button Sweet Alert
        document.querySelectorAll('.sa-bs-error-ico').forEach(function(element) {
            element.addEventListener('click', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Are You Sure! You want to Reject',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                })
            });
        });

        // Select with Search
        document.addEventListener('DOMContentLoaded', function() {
            var element = document.querySelector('#selectAdvertisers');
            new Choices(element, {
                searchPlaceholderValue: 'Search Advertisers'
            });

            var element = document.querySelector('#selectPages');
            new Choices(element, {
                searchPlaceholderValue: 'Search Page'
            });
        })

        // Date Range for Ads
        const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
            buttonClass: 'btn'
        });
    </script>
    <!-- [Page Specific JS] end -->
@endsection
