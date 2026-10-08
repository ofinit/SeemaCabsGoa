@extends('layouts.main')

@section('title', 'Ad Invoices')
@section('breadcrumb-item', 'Accounting')

@section('breadcrumb-item-active', 'Ad Invoices')

@section('css')
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/style.css') }}">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs invoice-tab border-bottom mb-3" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="analytics-tab-1" data-bs-toggle="tab"
                            data-bs-target="#analytics-tab-1-pane" type="button" role="tab"
                            aria-controls="analytics-tab-1-pane" aria-selected="true">
                            <span class="d-flex align-items-center gap-2">All <span
                                    class="avtar rounded-circle bg-light-primary">5</span></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="analytics-tab-2" data-bs-toggle="tab"
                            data-bs-target="#analytics-tab-2-pane" type="button" role="tab"
                            aria-controls="analytics-tab-2-pane" aria-selected="false">
                            <span class="d-flex align-items-center gap-2">Active <span
                                    class="avtar rounded-circle bg-light-success">2</span></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="analytics-tab-4" data-bs-toggle="tab"
                            data-bs-target="#analytics-tab-4-pane" type="button" role="tab"
                            aria-controls="analytics-tab-4-pane" aria-selected="false">
                            <span class="d-flex align-items-center gap-2">Expired <span
                                    class="avtar rounded-circle bg-light-danger">2</span></span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel"
                        aria-labelledby="analytics-tab-1" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="pc-dt-simple-1">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice Number</th>
                                        <th>Ad ID</th>
                                        <th>Advertiser</th>
                                        <th>Mobile Number</th>
                                        <th>Ad Placement</th>
                                        <th>Validity</th>
                                        <th>Total Amount</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>01-12-2024</td>
                                        <td>INV-001</td>
                                        <td>#AD98745632</td>
                                        <td>John Doe</td>
                                        <td>9876543210</td>
                                        <td>Home Screen</td>
                                        <td>02-12-2024 to 15-12-2024</td>
                                        <td>Rs.1500.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-danger">Expired</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>02-12-2024</td>
                                        <td>INV-002</td>
                                        <td>#AD98745632</td>
                                        <td>Jane Smith</td>
                                        <td>8765432109</td>
                                        <td>Home Screen</td>
                                        <td>10-12-2024 to 25-12-2024</td>
                                        <td>Rs.1450.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-success">Active</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>03-12-2024</td>
                                        <td>INV-003</td>
                                        <td>#AD98745632</td>
                                        <td>Bob Johnson</td>
                                        <td>7654321098</td>
                                        <td>Home Screen</td>
                                        <td>20-12-2024 to 05-01-2025</td>
                                        <td>Rs.1650.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-success">Active</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>04-12-2024</td>
                                        <td>INV-004</td>
                                        <td>#AD98745632</td>
                                        <td>Alice Brown</td>
                                        <td>6543210987</td>
                                        <td>Home Screen</td>
                                        <td>01-01-2025 to 15-01-2025</td>
                                        <td>Rs.1300.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-success">Active</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>05-12-2024</td>
                                        <td>INV-005</td>
                                        <td>#AD98745632</td>
                                        <td>Charlie White</td>
                                        <td>5432109876</td>
                                        <td>Home Screen</td>
                                        <td>12-01-2025 to 28-01-2025</td>
                                        <td>Rs.1780.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-danger">Expired</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel"
                        aria-labelledby="analytics-tab-2" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="pc-dt-simple-2">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice Number</th>
                                        <th>Ad ID</th>
                                        <th>Advertiser</th>
                                        <th>Mobile Number</th>
                                        <th>Ad Placement</th>
                                        <th>Validity</th>
                                        <th>Total Amount</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>02-12-2024</td>
                                        <td>INV-002</td>
                                        <td>#AD98745632</td>
                                        <td>Jane Smith</td>
                                        <td>8765432109</td>
                                        <td>Home Screen</td>
                                        <td>10-12-2024 to 25-12-2024</td>
                                        <td>Rs.1450.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-success">Active</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>03-12-2024</td>
                                        <td>INV-003</td>
                                        <td>#AD98745632</td>
                                        <td>Bob Johnson</td>
                                        <td>7654321098</td>
                                        <td>Home Screen</td>
                                        <td>20-12-2024 to 05-01-2025</td>
                                        <td>Rs.1650.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-success">Active</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>04-12-2024</td>
                                        <td>INV-004</td>
                                        <td>#AD98745632</td>
                                        <td>Alice Brown</td>
                                        <td>6543210987</td>
                                        <td>Home Screen</td>
                                        <td>01-01-2025 to 15-01-2025</td>
                                        <td>Rs.1300.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-success">Active</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="analytics-tab-4-pane" role="tabpanel"
                        aria-labelledby="analytics-tab-4" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="pc-dt-simple-4">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice Number</th>
                                        <th>Ad ID</th>
                                        <th>Advertiser</th>
                                        <th>Mobile Number</th>
                                        <th>Ad Placement</th>
                                        <th>Validity</th>
                                        <th>Total Amount</th>
                                        <th>Transaction ID</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>01-12-2024</td>
                                        <td>INV-001</td>
                                        <td>#AD98745632</td>
                                        <td>John Doe</td>
                                        <td>9876543210</td>
                                        <td>Home Screen</td>
                                        <td>02-12-2024 to 15-12-2024</td>
                                        <td>Rs.1500.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-danger">Expired</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
                                            <a href="#!" class="btn btn-sm btn-light-secondary me-1"><i
                                                    class="feather icon-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>05-12-2024</td>
                                        <td>INV-005</td>
                                        <td>#AD98745632</td>
                                        <td>Charlie White</td>
                                        <td>5432109876</td>
                                        <td>Home Screen</td>
                                        <td>12-01-2025 to 28-01-2025</td>
                                        <td>Rs.1780.00
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                data-bs-html="true"
                                                title="Base Fare (30 kms) = Rs.1200</br> Surge Charges = Rs.0.00</br> Addl. 5 Kms = Rs.150</br> Waiting Charges 30 Mins = Rs.100</br> Discount = Rs.0.00</br> Taxes = Rs.261</br> ------------------------------------</br> TOTAL = Rs.1711/-">
                                            </i>
                                        </td>
                                        <td>TRX987654</td>
                                        <td>
                                            <h5><span class="badge bg-danger">Expired</span></h5>
                                        </td>
                                        <td>
                                            <a href="#!" class="btn btn-sm btn-light-primary me-1"
                                                data-bs-toggle="modal" data-bs-target="#AdinvoiceModel"><i
                                                    class="feather icon-eye"></i></a>
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
    </div>
</div>

<!-- Ad Invoice Model -->
<div class="modal fade" id="AdinvoiceModel" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
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
                                <p class="mb-0"><span class="text-dark fw-bold">Bill To:</span> ABC Pvt. Ltd.</p>
                                <p class="mb-0">Contact Name: Priya Sharma</p>
                                <p class="mb-0">Goa, India</p>
                                <p class="mb-0">Mobile : 9988774455</p>
                                <p class="mb-0">Email ID : priyasharma123@gmail.com</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0">GST Number : 27AABCU9603R1ZV</p>
                                <p class="mb-0">PAN Number : AABCU9603R</p>
                            </div>
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>AD DESCRIPTION</th>
                                                <th class="text-end">NO. OF DAYS</th>
                                                <th class="text-end">AMOUNT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Home Screen Ad </td>
                                                <td class="text-end">7</td>
                                                <td class="text-end">Rs.1200.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-start">
                                    <hr class="mb-2 mt-1">
                                </div>
                            </div>
                            <div class="col-12 mb-5">
                                <div class="invoice-total ms-auto">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="text-muted mb-1 text-start">Discount</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="f-w-600 mb-1 text-end">- Rs.0.00</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted mb-1 text-start">GST @ 18%</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="f-w-600 mb-1 text-end">Rs.0.00</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="f-w-600 mb-1 text-start">Total :</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="f-w-600 mb-1 text-end">Rs.1200.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-5">
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
<script src="{{ URL::asset('build/js/plugins/simple-datatables.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/invoice-list.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            floatchart();
        }, 500);
    });

    function floatchart() {
        const dataTable1 = new simpleDatatables.DataTable('#pc-dt-simple-1', {
            sortable: false,
        });
        const dataTable2 = new simpleDatatables.DataTable('#pc-dt-simple-2', {
            sortable: false,
        });
        const dataTable3 = new simpleDatatables.DataTable('#pc-dt-simple-3', {
            sortable: false,
        });
    }
</script>

<!-- [Page Specific JS] end -->
@endsection