@extends('layouts.main')

@section('title', 'Fleet Operator Payments')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Reports
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Fleet Operator Payments</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Fleet Operator Payments</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="my-3">
                <label class="form-label" for="datepicker_range">Select Date Range</label>
                <div class="input-daterange input-group" id="datepicker_range">
                    <input type="text" name="start_date" class="form-control datepicker_rangeS startDate text-left"
                        placeholder="Start date" name="range-start">
                    <input type="text" name="end_date" class="form-control endDate text-end end-ad-date"
                        placeholder="End date" name="range-end">
                    <div style="margin-left: 20px">

                        <button class="btn btn-primary applyDateFilter">Apply</button>
                    </div>
                </div>
                <span class="dateError text-danger"></span>
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
                                        class="avtar rounded-circle bg-light-primary">{{ $payment ?? 0 }}</span></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab-2" data-bs-toggle="tab"
                                data-bs-target="#analytics-tab-2-pane" type="button" role="tab"
                                aria-controls="analytics-tab-2-pane" aria-selected="false">
                                <span class="d-flex align-items-center gap-2">Paid <span
                                        class="avtar rounded-circle bg-light-success">{{ $paymentPaid ?? 0 }}</span></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab-3" data-bs-toggle="tab"
                                data-bs-target="#analytics-tab-3-pane" type="button" role="tab"
                                aria-controls="analytics-tab-3-pane" aria-selected="false">
                                <span class="d-flex align-items-center gap-2">Unpaid <span
                                        class="avtar rounded-circle bg-light-danger">{{ $paymentUnpaid ?? 0 }}</span></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab-4" data-bs-toggle="tab"
                                data-bs-target="#analytics-tab-4-pane" type="button" role="tab"
                                aria-controls="analytics-tab-4-pane" aria-selected="false">
                                <span class="d-flex align-items-center gap-2">Refund <span
                                        class="avtar rounded-circle bg-light-primary">{{ $paymentRefund ?? 0 }}</span></span>
                            </button>
                        </li>
                        <li class="nav-link flex-grow-1 text-end">
                            <a href="{{ route('admin.report.export') }}">
                                <button class="btn btn-sm btn-light-info">Export</button>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-1" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table fleetOperatorPayment" id="pc-dt-simple-1">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            {{-- <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th> --}}
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commission</th>
                                            <th>TDS</th>
                                            <th>Settlement Amount</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-2" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table fleetOperatorPaymentPaid"
                                    id="pc-dt-simple-2">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            {{-- <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th> --}}
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commissions</th>
                                            <th>TDS</th>
                                            <th>Settlement Amount</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="analytics-tab-3-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-3" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table fleetOperatorPaymentUnpaid"
                                    id="pc-dt-simple-3">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            {{-- <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th> --}}
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commissions</th>
                                            <th>TDS</th>
                                            <th>Settlement Amount</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="analytics-tab-4-pane" role="tabpanel"
                            aria-labelledby="analytics-tab-4" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-hover date-picker-table fleetOperatorPaymentRefund"
                                    id="pc-dt-simple-3">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Booking ID</th>
                                            {{-- <th>Fleet Operator</th>
                                            <th>Cab Number</th>
                                            <th>Driver Name</th>
                                            <th>Mobile Number</th> --}}
                                            <th>Pickup From</th>
                                            <th>Drop To</th>
                                            <th>Total Amount</th>
                                            <th>Aggregator Commissions</th>
                                            <th>TDS</th>
                                            <th>Settlement Amount</th>
                                            <th>Refund</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
    <script>
        function getSettlementDetails(settlement, field) {
            console.log("IN");

            var settlement = json_decode(settlement, true);
            console.log(settlement);

            return settlement[field];
        }
        $(function() {
            $('.fleetOperatorPayment').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.report.list') }}",
                    type: "POST",
                    data: function(data) {
                        data.start_date = $('.startDate').val();
                        data.end_date = $('.endDate').val();
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'date',
                        name: 'date',
                        render: function(data, type, row) {
                            var date = row.date ?? '--';

                            var dateTime = new Date(date);
                            var day = dateTime.getDate();
                            var month = dateTime.getMonth() + 1;
                            var year = dateTime.getFullYear().toString().slice(-2);

                            var formattedDate = `${day}-${month}-${year}`;
                            var formattedTime = dateTime.toLocaleString('en-IN', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                            return formattedDate;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.booking_id : '--';
                        }
                    },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details.name :
                    //             '--' : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.number : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         var name = row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.name : '--' : '--';
                    //         var image = row.booking_details ? row.booking_details.assign_driver ?
                    //             row
                    //             .booking_details.assign_driver.image : '--' : '--';
                    //         var imagePath = "{{ asset('build/images/user/avatar-1.jpg') }}";
                    //         if (row.image != null) {
                    //             var imagePath = "{{ asset('customers/') }}" + "/" + image;
                    //         }
                    //         var html = `<div class="d-flex align-items-center gap-2">
                //                                 <a href="${imagePath}"
                //                                     class="glightbox">
                //                                     <img src="${imagePath}"
                //                                         alt="image" class="img-radius customer-profile-image" />
                //                                 </a>
                //                                 ${name??''}
                //                             </div>`;
                    //         return html ?? '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.mobile : '--' : '--';
                    //     }
                    // },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                           var html = '--';
                            var tripType = row.booking_details ? row.booking_details.trip_type:'';
                            if(tripType == 'Airport Transfer'){
                                if(row.booking_details.pickup_from == 1){
                                    html = 'Dabolim Goa Airport (GOI)';
                                }else if(row.booking_details.pickup_from == 2){
                                    html = 'Manohar International Airport (GOX)';
                                }
                            }else{
                                html = row.booking_details.get_pickup_from ? row.booking_details.get_pickup_from.name: '--';
                            }
                            return html;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.get_drop_to ? row
                                .booking_details.get_drop_to.name : '--' : '--';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return getCurrencySign() + row.amount ?? '--';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.company_commission??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.tds_amount??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.fleet_operator_total_payment??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'refund',
                        name: 'refund',
                        render: function(data, type, row) {
                             var amount = row.booking_details ? row.booking_details.refund?row.booking_details.refund:0.00:0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var html = ` <h5><span class="badge bg-success">Paid</span></h5>`;
                            if (row.status == 4) {
                                var html = ` <h5><span class="badge bg-primary">Refund</span></h5>`;
                            } else if (row.status == 2) {
                                var html = ` <h5><span class="badge bg-danger">Unpaid</span></h5>`;

                            }
                            return html ?? '--';
                        }
                    }

                ],
                drawCallback: function() {
                    GLightbox({
                        touchNavigation: true,
                        loop: true,
                        width: "90vw",
                        height: "90vh"
                    });
                }
            });
        });
        $(function() {
            $('.fleetOperatorPaymentRefund').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.report.list.refund') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'date',
                        name: 'date',
                        render: function(data, type, row) {
                            var date = row.date ?? '--';

                            var dateTime = new Date(date);
                            var day = dateTime.getDate();
                            var month = dateTime.getMonth() + 1;
                            var year = dateTime.getFullYear().toString().slice(-2);

                            var formattedDate = `${day}-${month}-${year}`;
                            var formattedTime = dateTime.toLocaleString('en-IN', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                            return formattedDate;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.booking_id : '--';
                        }
                    },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details.name :
                    //             '--' : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.number : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         var name = row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.name : '--' : '--';
                    //         var image = row.booking_details ? row.booking_details.assign_driver ?
                    //             row
                    //             .booking_details.assign_driver.image : '--' : '--';
                    //         var imagePath = "{{ asset('build/images/user/avatar-1.jpg') }}";
                    //         if (row.image != null) {
                    //             var imagePath = "{{ asset('customers/') }}" + "/" + image;
                    //         }
                    //         var html = `<div class="d-flex align-items-center gap-2">
                //                                 <a href="${imagePath}"
                //                                     class="glightbox">
                //                                     <img src="${imagePath}"
                //                                         alt="image" class="img-radius customer-profile-image" />
                //                                 </a>
                //                                 ${name??''}
                //                             </div>`;
                    //         return html ?? '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.mobile : '--' : '--';
                    //     }
                    // },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                             var html = '--';
                            var tripType = row.booking_details ? row.booking_details.trip_type:'';
                            if(tripType == 'Airport Transfer'){
                                if(row.booking_details.pickup_from == 1){
                                    html = 'Dabolim Goa Airport (GOI)';
                                }else if(row.booking_details.pickup_from == 2){
                                    html = 'Manohar International Airport (GOX)';
                                }
                            }else{
                                html = row.booking_details.get_pickup_from ? row.booking_details.get_pickup_from.name: '--';
                            }
                            return html;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.get_drop_to ? row
                                .booking_details.get_drop_to.name : '--' : '--';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return getCurrencySign() + row.amount ?? '--';
                        }
                    },
                     {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.company_commission??0.00;
                            return getCurrencySign() + amount;
                            // return getCurrencySign() + amount+ ` <i class="ti ti-info-circle" data-bs-toggle="tooltip"
                            //                         data-bs-placement="top" data-bs-custom-class="tooltip-left-align"
                            //                         data-bs-html="true"
                            //                         title="Aggregator Commission : Rs.350</br> GST @ 18% : Rs.63 </br> TDS -10% : Rs.35</br> ------------------------------------</br> TOTAL = Rs.378/-">
                            //                     </i>` ?? '--';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.tds_amount??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.fleet_operator_total_payment??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'refund',
                        name: 'refund',
                        render: function(data, type, row) {
                             var amount = row.booking_details ? row.booking_details.refund?row.booking_details.refund:0.00:0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var html = ` <h5><span class="badge bg-success">Paid</span></h5>`;
                            if (row.status == 4) {
                                var html = ` <h5><span class="badge bg-primary">Refund</span></h5>`;
                            } else if (row.status == 2) {
                                var html = ` <h5><span class="badge bg-danger">Unpaid</span></h5>`;

                            }
                            return html ?? '--';
                        }
                    }

                ],
                drawCallback: function() {
                    GLightbox({
                        touchNavigation: true,
                        loop: true,
                        width: "90vw",
                        height: "90vh"
                    });
                }
            });
        });
        $(function() {
            $('.fleetOperatorPaymentUnpaid').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.report.list.unpaid') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'date',
                        name: 'date',
                        render: function(data, type, row) {
                            var date = row.date ?? '--';

                            var dateTime = new Date(date);
                            var day = dateTime.getDate();
                            var month = dateTime.getMonth() + 1;
                            var year = dateTime.getFullYear().toString().slice(-2);

                            var formattedDate = `${day}-${month}-${year}`;
                            var formattedTime = dateTime.toLocaleString('en-IN', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                            return formattedDate;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.booking_id : '--';
                        }
                    },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details.name :
                    //             '--' : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.number : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         var name = row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.name : '--' : '--';
                    //         var image = row.booking_details ? row.booking_details.assign_driver ?
                    //             row
                    //             .booking_details.assign_driver.image : '--' : '--';
                    //         var imagePath = "{{ asset('build/images/user/avatar-1.jpg') }}";
                    //         if (row.image != null) {
                    //             var imagePath = "{{ asset('customers/') }}" + "/" + image;
                    //         }
                    //         var html = `<div class="d-flex align-items-center gap-2">
                //                                 <a href="${imagePath}"
                //                                     class="glightbox">
                //                                     <img src="${imagePath}"
                //                                         alt="image" class="img-radius customer-profile-image" />
                //                                 </a>
                //                                 ${name??''}
                //                             </div>`;
                    //         return html ?? '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.mobile : '--' : '--';
                    //     }
                    // },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                             var html = '--';
                            var tripType = row.booking_details ? row.booking_details.trip_type:'';
                            if(tripType == 'Airport Transfer'){
                                if(row.booking_details.pickup_from == 1){
                                    html = 'Dabolim Goa Airport (GOI)';
                                }else if(row.booking_details.pickup_from == 2){
                                    html = 'Manohar International Airport (GOX)';
                                }
                            }else{
                                html = row.booking_details.get_pickup_from ? row.booking_details.get_pickup_from.name: '--';
                            }
                            return html;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.get_drop_to ? row
                                .booking_details.get_drop_to.name : '--' : '--';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return getCurrencySign() + row.amount ?? '--';
                        }
                    },
                     {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.company_commission??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.tds_amount??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.fleet_operator_total_payment??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'refund',
                        name: 'refund',
                        render: function(data, type, row) {
                             var amount = row.booking_details ? row.booking_details.refund?row.booking_details.refund:0.00:0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var html = ` <h5><span class="badge bg-success">Paid</span></h5>`;
                            if (row.status == 4) {
                                var html = ` <h5><span class="badge bg-primary">Refund</span></h5>`;
                            } else if (row.status == 2) {
                                var html = ` <h5><span class="badge bg-danger">Unpaid</span></h5>`;

                            }
                            return html ?? '--';
                        }
                    }

                ],
                drawCallback: function() {
                    GLightbox({
                        touchNavigation: true,
                        loop: true,
                        width: "90vw",
                        height: "90vh"
                    });
                }
            });
        });
        $(function() {
            $('.fleetOperatorPaymentPaid').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.report.list.paid') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'date',
                        name: 'date',
                        render: function(data, type, row) {
                            var date = row.date ?? '--';

                            var dateTime = new Date(date);
                            var day = dateTime.getDate();
                            var month = dateTime.getMonth() + 1;
                            var year = dateTime.getFullYear().toString().slice(-2);

                            var formattedDate = `${day}-${month}-${year}`;
                            var formattedTime = dateTime.toLocaleString('en-IN', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                            return formattedDate;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.booking_id : '--';
                        }
                    },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details ? row
                    //             .booking_details.get_cab_details.get_fleet_operator_details.name :
                    //             '--' : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.get_cab_details ? row
                    //             .booking_details.get_cab_details.number : '--' : '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         var name = row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.name : '--' : '--';
                    //         var image = row.booking_details ? row.booking_details.assign_driver ?
                    //             row
                    //             .booking_details.assign_driver.image : '--' : '--';
                    //         var imagePath = "{{ asset('build/images/user/avatar-1.jpg') }}";
                    //         if (row.image != null) {
                    //             var imagePath = "{{ asset('customers/') }}" + "/" + image;
                    //         }
                    //         var html = `<div class="d-flex align-items-center gap-2">
                //                                 <a href="${imagePath}"
                //                                     class="glightbox">
                //                                     <img src="${imagePath}"
                //                                         alt="image" class="img-radius customer-profile-image" />
                //                                 </a>
                //                                 ${name??''}
                //                             </div>`;
                    //         return html ?? '--';
                    //     }
                    // },
                    // {
                    //     data: 'default',
                    //     name: 'default',
                    //     render: function(data, type, row) {
                    //         return row.booking_details ? row.booking_details.assign_driver ? row
                    //             .booking_details.assign_driver.mobile : '--' : '--';
                    //     }
                    // },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                             var html = '--';
                            var tripType = row.booking_details ? row.booking_details.trip_type:'';
                            if(tripType == 'Airport Transfer'){
                                if(row.booking_details.pickup_from == 1){
                                    html = 'Dabolim Goa Airport (GOI)';
                                }else if(row.booking_details.pickup_from == 2){
                                    html = 'Manohar International Airport (GOX)';
                                }
                            }else{
                                html = row.booking_details.get_pickup_from ? row.booking_details.get_pickup_from.name: '--';
                            }
                            return html;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.booking_details ? row.booking_details.get_drop_to ? row
                                .booking_details.get_drop_to.name : '--' : '--';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return getCurrencySign() + row.amount ?? '--';
                        }
                    },
                     {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.company_commission??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.tds_amount??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var settlement = JSON.parse(row.amount_settlement);
                            var amount = settlement.fleet_operator_total_payment??0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'refund',
                        name: 'refund',
                       render: function(data, type, row) {
                             var amount = row.booking_details ? row.booking_details.refund?row.booking_details.refund:0.00:0.00;
                            return getCurrencySign() + amount;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            var html = ` <h5><span class="badge bg-success">Paid</span></h5>`;
                            if (row.status == 4) {
                                var html = ` <h5><span class="badge bg-primary">Refund</span></h5>`;
                            } else if (row.status == 2) {
                                var html = ` <h5><span class="badge bg-danger">Unpaid</span></h5>`;

                            }
                            return html ?? '--';
                        }
                    }

                ],
                drawCallback: function() {
                    GLightbox({
                        touchNavigation: true,
                        loop: true,
                        width: "90vw",
                        height: "90vh"
                    });
                }
            });
        });
    </script>
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     setTimeout(function() {
        //         floatchart();
        //     }, 500);
        // });

        // function floatchart() {
        //     const dataTable1 = new simpleDatatables.DataTable('#pc-dt-simple-1', {
        //         sortable: false,
        //     });
        //     const dataTable2 = new simpleDatatables.DataTable('#pc-dt-simple-2', {
        //         sortable: false,
        //     });
        //     const dataTable3 = new simpleDatatables.DataTable('#pc-dt-simple-3', {
        //         sortable: false,
        //     });

        //     // GLightBox
        //     const lightbox = GLightbox({
        //         touchNavigation: true,
        //         loop: true,
        //         width: "90vw",
        //         height: "90vh"
        //     });
        // }

        // Date Range
        // const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
        //     buttonClass: 'btn'
        // });

        $(function() {
            // $('.datepicker_rangeS').on('change', function(e) {
            //     e.preventDefault();
            //     $('#fleetOperatorPayment').DataTable().ajax.reload();
            // });

            const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
                buttonClass: 'btn'
            });
            $('.applyDateFilter').on('click', function(e) {
                $('.dateError').empty('');
                const startDate = $('input[name="start_date"]').val();
                const endDate = $('input[name="end_date"]').val();
                if (startDate == '') {
                    $('.dateError').html('Please select date range.');
                }
                $('.fleetOperatorPayment').DataTable().ajax.reload();
            });

        });
    </script>

    <!-- [Page Specific JS] end -->
@endsection
