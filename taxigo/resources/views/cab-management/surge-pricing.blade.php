@extends('layouts.main')

@section('title', 'Surge Pricing')
@section('css')
    <style>
        .parsley-errors-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
            color: red;
            font-size: 14px;
        }

        .parsley-errors-list li {
            display: inline;
        }

        #enterStartTime::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        /* Optional cleanup styling */
        #enterStartTime {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 0;
        }

        #enterEndTime::-webkit-calendar-picker-indicator {
            display: none;
            -webkit-appearance: none;
        }

        /* Optional cleanup styling */
        #enterEndTime {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 0;
        }
    </style>
@endsection
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.cabs.index') }}">Cab Management</a></li>
                        <li class="breadcrumb-item" aria-current="page">Surge Pricing</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Surge Pricing</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>
        <form class="surgePriceForm" method="POST" action="{{ route('admin.cabs.storeSurgePrice') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center gap-2">
                            <h5>Enable Surge Pricing</h5>
                            <div class="form-check form-switch custom-switch-v1">
                                <input type="hidden" name="surge_title" value="surge_pricing" id="">
                                <input type="checkbox" name="surge_enable" class="form-check-input input-success"
                                    id="surgePricingSwitch" {{ @$data['surge_enable'] == 'on' ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- <div class="col-12 mb-3">
                                <label class="form-label" for="customerSurge">Customer Surge Pricing</label>
                                <div class="input-group">
                                    <input type="text" id="customerSurge" class="form-control"
                                        placeholder="Enter percentage" aria-label="Recipient's username" name="surge_price"
                                        aria-describedby="basic-addon2"
                                        value="{{ @$data['surge_price'] ?? old('surge_price') }}" required
                                        data-parsley-type="number" data-parsley-pattern="^[0-9]*(\.[0-9]+)?$"
                                        data-parsley-required-message="Price is required." data-parsley-min="0"
                                        data-parsley-max="100" data-parsley-min-message="Price cannot be less than 0."
                                        data-parsley-error-message="Please enter a valid price (numeric or decimal)."
                                        data-parsley-errors-container="#pricing-error-container">
                                    <span class="input-group-text" id="basic-addon2">%</span>
                                </div>
                                <span id="pricing-error-container" class="text-danger"></span>
                            </div> --}}
                            <div id="datetime-container">
                                @if (isset($data['surge_start_date']) && count($data['surge_start_date']) > 0)
                                    @foreach ($data['surge_start_date'] as $key => $value)
                                        <div class="row datetime-row align-items-end mt-2">
                                            <div class="col-lg-2 col-md-4">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" class="form-control" name="surge_start_date[]"
                                                    value="{{ $value ?? '' }}" required
                                                    data-parsley-error-message="Start Date is required."
                                                    data-parsley-errors-container="#start-date-error-container">
                                            </div>
                                            <div class="col-lg-2 col-md-4">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" class="form-control" name="surge_start_time[]"
                                                    value="{{ $data['surge_start_time'][$key] }}" required
                                                    data-parsley-error-message="Start Time is required."
                                                    data-parsley-errors-container="#start-time-error-container">
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <label class="form-label">End Date</label>
                                                <input type="date" class="form-control" name="surge_end_date[]" required
                                                    value="{{ $data['surge_end_date'][$key] }}"
                                                    data-parsley-error-message="End date is required."
                                                    data-parsley-errors-container="#end-date-error-container">
                                            </div>
                                            <div class="col-lg-2 col-md-4">
                                                <label class="form-label">End Time</label>
                                                <input type="time" class="form-control" name="surge_end_time[]" required
                                                    value="{{ $data['surge_end_time'][$key] }}"
                                                    data-parsley-error-message="End time is required."
                                                    data-parsley-errors-container="#end-time-error-container">
                                            </div>
                                            <div class="col-lg-2 col-md-2">
                                                <label class="form-label">Surge %</label>
                                                <input type="number" class="form-control" name="surge_percentage[]" required
                                                    value="{{ isset($data['surge_percentage'][$key]) ? $data['surge_percentage'][$key] : ''}}"
                                                    data-parsley-required-message="Please enter percentage"
                                                    data-parsley-type="number"
                                                    data-parsley-min="-100"
                                                    data-parsley-min-message="Percentage must be greater than or equal to -100">
                                            </div>
                                            <div class="col-lg-1 col-md-1 text-end button-cell">
                                                <button type="button" class="btn btn-danger btn-sm mt-4 remove-row"
                                                    title="Remove" style="margin-bottom: 11px;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                @if ($key == count($data['surge_start_date']) - 1)
                                                    <button type="button" class="btn btn-success btn-sm mt-4 add-row"
                                                        title="Add Row" style="margin-bottom: 11px;">
                                                        <i class="feather icon-plus"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row datetime-row align-items-end">
                                        <div class="col-lg-2 col-md-4">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="surge_start_date[]" required
                                                data-parsley-error-message="Start Date is required."
                                                data-parsley-errors-container="#start-date-error-container">
                                        </div>
                                        <div class="col-lg-2 col-md-4">
                                            <label class="form-label">Start Time</label>
                                            <input type="time" class="form-control" name="surge_start_time[]" required
                                                data-parsley-error-message="Start Time is required."
                                                data-parsley-errors-container="#start-time-error-container">
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="surge_end_date[]" required
                                                data-parsley-error-message="End date is required."
                                                data-parsley-errors-container="#end-date-error-container">
                                        </div>
                                        <div class="col-lg-2 col-md-4">
                                            <label class="form-label">End Time</label>
                                            <input type="time" class="form-control" name="surge_end_time[]" required
                                                data-parsley-error-message="End time is required."
                                                data-parsley-errors-container="#end-time-error-container">
                                        </div>
                                        <div class="col-lg-2 col-md-2">
                                            <label class="form-label">Surge %</label>
                                            <input type="number" class="form-control" name="surge_percentage[]" required
                                                data-parsley-required-message="Please enter percentage"
                                                data-parsley-type="number"
                                                data-parsley-min="-100"
                                                data-parsley-min-message="Percentage must be greater than or equal to -100">
                                        </div>
                                        <div class="col-lg-1 col-md-1 text-end">
                                            <button type="button" id="add-row" class="btn btn-success btn-sm mt-4 add-row"
                                                title="Add Row" style="margin-bottom: 11px;/* margin: 0px !important; */">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 text-end">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
        // [ DOM/jquery ]
        var total, pageTotal;
        $('.surgePriceForm').parsley();

        var table = $('#dom-jqry').DataTable();
        // [ column Rendering ]
        $('#colum-render').DataTable({
            columnDefs: [{
                    render: function(data, type, row) {
                        return data + ' (' + row[3] + ')';
                    },
                    targets: 0
                },
                {
                    visible: false,
                    targets: [3]
                }
            ]
        });
        // [ Multiple Table Control Elements ]
        $('#multi-table').DataTable({
            dom: '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>'
        });
        // [ Complex Headers With Column Visibility ]
        $('#complex-header').DataTable({
            columnDefs: [{
                visible: false,
                targets: -1
            }]
        });
        // [ Language file ]
        $('#lang-file').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'
            }
        });
        // [ Setting Defaults ]
        $('#setting-default').DataTable();
        // [ Row Grouping ]
        var table1 = $('#row-grouping').DataTable({
            columnDefs: [{
                visible: false,
                targets: 2
            }],
            order: [
                [2, 'asc']
            ],
            displayLength: 25,
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api
                    .rows({
                        page: 'current'
                    })
                    .nodes();
                var last = null;

                api
                    .column(2, {
                        page: 'current'
                    })
                    .data()
                    .each(function(group, i) {
                        if (last !== group) {
                            $(rows)
                                .eq(i)
                                .before('<tr class="group"><td colspan="5">' + group + '</td></tr>');

                            last = group;
                        }
                    });
            }
        });

        // Select Date
        const boxAdStartDate = new Datepicker(document.querySelector('#enterStartDate'), {
            buttonClass: 'btn',
            format: 'yyyy-mm-dd', // Ensures correct format
            autohide: true
        });

        const boxAdEndDate = new Datepicker(document.querySelector('#enterEndDate'), {
            buttonClass: 'btn',
            format: 'yyyy-mm-dd', // Ensures correct format
            autohide: true
        });

        // [ Order by the grouping ]
        $('#row-grouping tbody').on('click', 'tr.group', function() {
            var currentOrder = table.order()[0];
            if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                table.order([2, 'desc']).draw();
            } else {
                table.order([2, 'asc']).draw();
            }
        });
        // [ Footer callback ]
        $('#footer-callback').DataTable({
            footerCallback: function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i :
                        0;
                };

                // Total over all pages
                total = api
                    .column(4)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Total over this page
                pageTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(4).footer()).html('$' + pageTotal + ' ( $' + total + ' total)');
            }
        });
        // [ Custom Toolbar Elements ]
        $('#c-tool-ele').DataTable({
            dom: '<"toolbar">frtip'
        });
        // [ Custom Toolbar Elements ]
        $('div.toolbar').html('<b>Custom tool bar! Text/images etc.</b>');
        // [ custom callback ]
        $('#row-callback').DataTable({
            createdRow: function(row, data, index) {
                if (data[5].replace(/[\$,]/g, '') * 1 > 150000) {
                    $('td', row).eq(5).addClass('highlight');
                }
            }
        });

        // Delete Button Sweet Alert
        document.querySelectorAll('.sa-bs-error-ico').forEach(function(element) {
            element.addEventListener('click', function() {
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

        $(function() {
            $('.openTimePicker').on('click', function(e) {
                e.preventDefault();
                const timeInput = document.getElementById('enterStartTime');
                if (timeInput.showPicker) {
                    timeInput.showPicker();
                } else {
                    timeInput.focus();
                }
            });

            $('.openEndTimePicker').on('click', function(e) {
                e.preventDefault();
                const timeInput = document.getElementById('enterEndTime');
                if (timeInput.showPicker) {
                    timeInput.showPicker();
                } else {
                    timeInput.focus();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            window.Parsley.addValidator('enddateafterstart', {
                requirementType: 'string',
                validateString: function(endDateValue, startDateSelector) {
                    if (!endDateValue) return true; // Let 'required' handle it
                    const startDateValue = $(startDateSelector).val();
                    if (!startDateValue) return true;

                    const endDate = new Date(endDateValue);
                    const startDate = new Date(startDateValue);
                    return endDate >= startDate;
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            // Function to ensure only last row has the Add button
            function refreshAddButton() {
                $('.add-row').remove();

                let lastRow = $('.datetime-row').last();
                let addButton = `
            <button type="button" class="btn btn-success btn-sm mt-4 add-row" title="Add Row" style="margin-bottom: 11px;">
                <i class="feather icon-plus"></i>
            </button>`;
                lastRow.find('.button-cell').append(addButton);
            }

            // Add new row
            $(document).on('click', '.add-row', function() {
                let newRow = `
                <div class="row datetime-row align-items-end mt-2">
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="surge_start_date[]" required
                        data-parsley-error-message="Start Date is required."
                        data-parsley-errors-container="#start-date-error-container">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Start Time</label>
                        <input type="time" class="form-control" name="surge_start_time[]" required
                        data-parsley-error-message="Start Time is required."
                        data-parsley-errors-container="#start-time-error-container">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="surge_end_date[]" required
                        data-parsley-error-message="End date is required."
                        data-parsley-errors-container="#end-date-error-container">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">End Time</label>
                        <input type="time" class="form-control" name="surge_end_time[]" required
                        data-parsley-error-message="End time is required."
                        data-parsley-errors-container="#end-time-error-container">
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <label class="form-label">Surge %</label>
                        <input type="number" class="form-control" name="surge_percentage[]" required
                            data-parsley-required-message="Please enter percentage"
                            data-parsley-type="number"
                            data-parsley-min="-100"
                            data-parsley-min-message="Percentage must be greater than or equal to -100">
                    </div>
                    <div class="col-lg-1 col-md-1 text-end button-cell">
                        <button type="button" class="btn btn-danger btn-sm mt-4 remove-row" title="Remove" style="margin-bottom: 11px;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>`;
                $('#datetime-container').append(newRow);
                refreshAddButton();
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.datetime-row').remove();
                refreshAddButton();
            });
        });
    </script>

    <!-- [Page Specific JS] end -->
@endsection
