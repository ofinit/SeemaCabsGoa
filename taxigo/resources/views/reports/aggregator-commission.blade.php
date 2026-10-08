@extends('layouts.main')

@section('title', 'Aggregator Commission')
@section('breadcrumb-item', 'Reports')

@section('breadcrumb-item-active', 'Aggregator Commission')

@section('css')

    <!-- GLightBox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- Date Range CSS -->
    <link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/style.css') }}">
@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="my-3">
                <label class="form-label" for="datepicker_range">Select Date Range</label>
                <div class="input-daterange input-group" id="datepicker_range">
                    <input type="text" class="form-control text-left" placeholder="Start date" name="range-start">
                    <input type="text" class="form-control text-end end-ad-date" placeholder="End date" name="range-end">
                </div>
            </div>
        </div>
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
                                <span class="d-flex align-items-center gap-2">Paid <span
                                        class="avtar rounded-circle bg-light-success">3</span></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab-3" data-bs-toggle="tab"
                                data-bs-target="#analytics-tab-3-pane" type="button" role="tab"
                                aria-controls="analytics-tab-3-pane" aria-selected="false">
                                <span class="d-flex align-items-center gap-2">Unpaid <span
                                        class="avtar rounded-circle bg-light-danger">1</span></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab-4" data-bs-toggle="tab"
                                data-bs-target="#analytics-tab-4-pane" type="button" role="tab"
                                aria-controls="analytics-tab-4-pane" aria-selected="false">
                                <span class="d-flex align-items-center gap-2">Refund <span
                                        class="avtar rounded-circle bg-light-primary">1</span></span>
                            </button>
                        </li>
                        <li class="nav-link flex-grow-1 text-end">
                            <button class="btn btn-light-info">Export</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-1" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table" id="pc-dt-simple-1">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th>
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commission</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>01-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    John Doe
                                                </div>
                                            </td>
                                            <td>9876543210</td>
                                            <td>Goa Airport</td>
                                            <td>Panaji</td>
                                            <td>Rs.1500.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-success">Paid</span></h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>02-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Jane Smith
                                                </div>
                                            </td>
                                            <td>8765432109</td>
                                            <td>Goa Airport</td>
                                            <td>Margao</td>
                                            <td>Rs.1450.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-success">Paid</span></h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>03-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Bob Johnson
                                                </div>
                                            </td>
                                            <td>7654321098</td>
                                            <td>Goa Airport</td>
                                            <td>Vasco</td>
                                            <td>Rs.1650.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-danger">Unpaid</span></h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>04-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Alice Brown
                                                </div>
                                            </td>
                                            <td>6543210987</td>
                                            <td>Goa Airport</td>
                                            <td>Mapusa</td>
                                            <td>Rs.1300.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-success">Paid</span></h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>05-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Charlie White
                                                </div>
                                            </td>
                                            <td>5432109876</td>
                                            <td>Goa Airport</td>
                                            <td>Calangute</td>
                                            <td>--</td>
                                            <td>--</td>
                                            <td>Rs.150.00</td>
                                            <td>
                                                <h5><span class="badge bg-primary">Refund</span></h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-2" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table" id="pc-dt-simple-2">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th>
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commissions</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>01-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    John Doe
                                                </div>
                                            </td>
                                            <td>9876543210.00</td>
                                            <td>Goa Airport</td>
                                            <td>Panaji</td>
                                            <td>Rs.1500.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-success">Paid</span></h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>02-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Jane Smith
                                                </div>
                                            </td>
                                            <td>8765432109</td>
                                            <td>Goa Airport</td>
                                            <td>Margao</td>
                                            <td>Rs.1450.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-success">Paid</span></h5>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>04-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Alice Brown
                                                </div>
                                            </td>
                                            <td>6543210987</td>
                                            <td>Goa Airport</td>
                                            <td>Mapusa</td>
                                            <td>Rs.1300.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-success">Paid</span></h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="analytics-tab-3-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-3" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table" id="pc-dt-simple-3">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th>
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commissions</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>03-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Bob Johnson
                                                </div>
                                            </td>
                                            <td>7654321098</td>
                                            <td>Goa Airport</td>
                                            <td>Vasco</td>
                                            <td>Rs.1650.00</td>
                                            <td>Rs.350.00
                                                <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                                                    data-bs-html="true"
                                                    title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                                                </i>
                                            </td>
                                            <td>--</td>
                                            <td>
                                                <h5><span class="badge bg-danger">Unpaid</span></h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="analytics-tab-4-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-4" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table" id="pc-dt-simple-3">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th>
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commissions</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>05-12-2024</td>
                                            <td>BK987456321</td>
                                            <td>Operator-1</td>
                                            <td>GA01X1234</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                        class="glightbox">
                                                        <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}"
                                                            alt="image" class="img-radius customer-profile-image" />
                                                    </a>
                                                    Charlie White
                                                </div>
                                            </td>
                                            <td>5432109876</td>
                                            <td>Goa Airport</td>
                                            <td>Calangute</td>
                                            <td>--</td>
                                            <td>--</td>
                                            <td>Rs.150.00</td>
                                            <td>
                                                <h5><span class="badge bg-primary">Refund</span></h5>
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

@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->

    <!-- GLightBox -->
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    <!-- Date Range JS -->
    <script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/plugins/simple-datatables.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/flatpickr.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            event.preventDefault()
            setTimeout(function () {
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

            // GLightBox
            const lightbox = GLightbox({
                touchNavigation: true,
                loop: true,
                width: "90vw",
                height: "90vh"
            });
        }

        // Date Range
        const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
            buttonClass: 'btn'
        });

    </script>
    <!-- [Page Specific JS] end -->
@endsection