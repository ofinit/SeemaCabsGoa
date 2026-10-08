@extends('layouts.main')

@section('title', 'Customer Invoice')
@section('breadcrumb-item', 'Accounting')

@section('breadcrumb-item-active', 'Customer Invoice')

@section('css')

<!-- Date Range Picker -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/flatpickr.min.css') }}">

<!-- GLightBox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

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
                    <div class="col-sm-auto d-flex gap-3">
                        <input type="text" class="form-control" id="customerInvoiceDateRange"
                            placeholder="Select date range">
                        <button class="btn btn-sm btn-light-info">Export</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice Number</th>
                                <th>Booking ID</th>
                                <th>Customer Name</th>
                                <th>Mobile Number</th>
                                <th>Pickup From</th>
                                <th>Drop To</th>
                                <th>Fleet Operator</th>
                                <th>Driver Name</th>
                                <th>Cab Number</th>
                                <th>Amount</th>
                                <th>Transaction ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-12-2024</td>
                                <td>INV-001</td>
                                <td>#GT1245781232</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        John Doe
                                    </div>
                                </td>
                                <td>9876543210</td>
                                <td>Goa Airport</td>
                                <td>Panaji</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rancho
                                    </div>
                                </td>
                                <td>GA 03 AL 9874</td>
                                <td>Rs.1500.00</td>
                                <td>TRX987654</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>02-12-2024</td>
                                <td>INV-002</td>
                                <td>#GT1245781232</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Jane Smith
                                    </div>
                                </td>
                                <td>8765432109</td>
                                <td>Goa Airport</td>
                                <td>Margao</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rancho
                                    </div>
                                </td>
                                <td>GA 03 AL 9874</td>
                                <td>Rs.1450.00</td>
                                <td>TRX876543</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>03-12-2024</td>
                                <td>INV-003</td>
                                <td>#GT1245781232</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Bob Johnson
                                    </div>
                                </td>
                                <td>7654321098</td>
                                <td>Goa Airport</td>
                                <td>Vasco</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rancho
                                    </div>
                                </td>
                                <td>GA 03 AL 9874</td>
                                <td>Rs.1650.00</td>
                                <td>TRX765432</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>04-12-2024</td>
                                <td>INV-004</td>
                                <td>#GT1245781232</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Alice Brown
                                    </div>
                                </td>
                                <td>6543210987</td>
                                <td>Goa Airport</td>
                                <td>Mapusa</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rancho
                                    </div>
                                </td>
                                <td>GA 03 AL 9874</td>
                                <td>Rs.1300.00</td>
                                <td>TRX654321</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#invoiceModel"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                            class="feather icon-download"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>05-12-2024</td>
                                <td>INV-005</td>
                                <td>#GT1245781232</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>5432109876</td>
                                <td>Goa Airport</td>
                                <td>Calangute</td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rancho
                                    </div>
                                </td>
                                <td>GA 03 AL 9874</td>
                                <td>Rs.1780.00</td>
                                <td>TRX543210</td>
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
                                        <div class="d-flex align-items-center mb-2 invoice-img">
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
                                                <td class="text-end">Rs.1200.00</td>
                                            </tr>
                                            <tr>
                                                <td>Surge Charges </td>
                                                <td class="text-end">Rs.0.00</td>
                                            </tr>
                                            <tr>
                                                <td>Addl. 5 Kms</td>
                                                <td class="text-end">Rs.150.00</td>
                                            </tr>
                                            <tr>
                                                <td>Waiting Charges 30 Mins</td>
                                                <td class="text-end">Rs.100.00</td>
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

@endsection

@section('scripts')
<!-- [Page Specific JS] start -->

<!-- Date Range Picker -->
<script src="{{ URL::asset('build/js/plugins/flatpickr.min.js') }}"></script>

<!-- GLightBox -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<script>
    // GLightBox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        width: "90vw",
        height: "90vh"
    });

    // Date Range Picker
    flatpickr(document.querySelector('#customerInvoiceDateRange'), {
        mode: 'range'
    });
</script>

<!-- [Page Specific JS] end -->
@endsection