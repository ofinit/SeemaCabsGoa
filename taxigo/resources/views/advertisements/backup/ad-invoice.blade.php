@extends('layouts.main')

@section('title', 'Ad Invoice')
@section('breadcrumb-item', 'Advertisements')

@section('breadcrumb-item-active', 'Ad Invoice')

@section('css')

<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/flatpickr.min.css') }}">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between ali mb-3 g-3">
                    <div class="col-sm-auto">
                        <form class="form-search">
                            <i class="ph-duotone ph-magnifying-glass icon-search"></i>
                            <input type="search" class="form-control" placeholder="Search...">
                            <button class="btn btn-light-secondary btn-search">Search</button>
                        </form>
                    </div>
                    <div class="col-sm-auto">
                        <input type="date" class="form-control">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Advertiser</th>
                                <th>Mobile Number</th>
                                <th>Ad Screen</th>
                                <th>Ad Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Amount</th>
                                <th>Transaction ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-12-2024</td>
                                <td>John Doe</td>
                                <td>9876543210</td>
                                <td>Home Screen</td>
                                <td>Leaderboard Ad</td>
                                <td>01-12-2024</td>
                                <td>31-12-2024</td>
                                <td>12:00 AM</td>
                                <td>12:00 PM</td>
                                <td>Rs.1200</td>
                                <td>TRX987654</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>01-12-2024</td>
                                <td>John Doe</td>
                                <td>9876543210</td>
                                <td>Home Screen</td>
                                <td>Leaderboard Ad</td>
                                <td>01-12-2024</td>
                                <td>31-12-2024</td>
                                <td>12:00 AM</td>
                                <td>12:00 PM</td>
                                <td>Rs.1200</td>
                                <td>TRX987654</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>01-12-2024</td>
                                <td>John Doe</td>
                                <td>9876543210</td>
                                <td>Home Screen</td>
                                <td>Leaderboard Ad</td>
                                <td>01-12-2024</td>
                                <td>31-12-2024</td>
                                <td>12:00 AM</td>
                                <td>12:00 PM</td>
                                <td>Rs.1200</td>
                                <td>TRX987654</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>01-12-2024</td>
                                <td>John Doe</td>
                                <td>9876543210</td>
                                <td>Home Screen</td>
                                <td>Leaderboard Ad</td>
                                <td>01-12-2024</td>
                                <td>31-12-2024</td>
                                <td>12:00 AM</td>
                                <td>12:00 PM</td>
                                <td>Rs.1200</td>
                                <td>TRX987654</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>01-12-2024</td>
                                <td>John Doe</td>
                                <td>9876543210</td>
                                <td>Home Screen</td>
                                <td>Leaderboard Ad</td>
                                <td>01-12-2024</td>
                                <td>31-12-2024</td>
                                <td>12:00 AM</td>
                                <td>12:00 PM</td>
                                <td>Rs.1200</td>
                                <td>TRX987654</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

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
                            <div class="col-12">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="mb-0">To.</h6>
                                    <h5>Priya Sharma</h5>
                                    <p class="mb-0">XYZ.pvt.ltd</p>
                                    <p class="mb-0">Goa, India</p>
                                    <p class="mb-0">9988198982</p>
                                    <p class="mb-0">xyz123@gmail.com</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Ad Screen</th>
                                                <th>Ad Type</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Home Screen</td>
                                                <td>Leaderboard Ad</td>
                                                <td>01-12-2024</td>
                                                <td>31-12-2024</td>
                                                <td>12:00 AM</td>
                                                <td>12:00 PM</td>
                                                <td class="text-end">Rs.1200</td>
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
                                            <p class="text-muted mb-1 text-start">Taxes</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="f-w-600 mb-1 text-end">Rs.0.00</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="f-w-600 mb-1 text-start">Total :</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="f-w-600 mb-1 text-end">Rs.2400.00</p>
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
                                    Service Tax: 30AAECO0806H1Z1 PAN No: AAECO0806H CIN: U62013GA2023PTC015947<br>
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

@endsection

@section('scripts')
<!-- [Page Specific JS] start -->



<!-- [Page Specific JS] end -->
@endsection