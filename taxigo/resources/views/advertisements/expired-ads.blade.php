@extends('layouts.main')

@section('title', 'Expired Ads')

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
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.index') }}">Advertisements</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Expired Ads</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Expired Ads</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Expired Ads</h5>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="expiredAddTable" class="table table-striped table-bordered expiredAddTable">
                            <thead>
                                <tr>
                                    <th>Advertiser</th>
                                    <th>Ad Screen</th>
                                    {{-- <th>Ad Type</th> --}}
                                    <th>Ad Image</th>
                                    <th>Banner URL</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>No. of Clicks</th>
                                    {{-- <th>No. of Leads</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Model -->
    <div class="modal fade" id="renewAd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Renew Ad</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" class="renewForm" action="#">
                        @csrf
                        <div class="row gy-1">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="datepicker_range">Select Date Range</label>
                                <div class="input-daterange input-group" id="datepicker_range">
                                    <input type="text" name="start_date" class="form-control text-left"
                                        placeholder="Start date" name="range-start" required
                                        data-parsley-required-message="Please select date range."
                                        data-parsley-errors-container="#date-error-container">
                                    <input type="text" name="end_date" class="form-control text-end end-ad-date"
                                        placeholder="End date" name="range-end">
                                </div>
                                <span id="date-error-container" class="text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="datepicker_range">Start Time</label>
                                <div class="input-group timepicker">
                                    <input name="start_time" class="form-control" id="startTime" placeholder="Select time"
                                        type="time" required data-parsley-required-message="Please choose start time."
                                        data-parsley-errors-container="#starttime-error-container">
                                    <span class="input-group-text">
                                        <i class="feather icon-clock"></i>
                                    </span>
                                </div>
                                <span id="starttime-error-container" class="text-danger"></span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="datepicker_range">End Time</label>
                                <div class="input-group timepicker">
                                    <input class="form-control" id="boxAdStartTime" placeholder="Select time" type="time"
                                        required data-parsley-required-message="Please choose end time."
                                        data-parsley-errors-container="#endTime-error-container">
                                    <span class="input-group-text">
                                        <i class="feather icon-clock"></i>
                                    </span>
                                </div>
                                <span id="endTime-error-container" class="text-danger"></span>
                            </div>
                            <div class="col-12">
                                <input type="hidden" class="totalAmountInput" name="total_amount" value="">
                                <h5>Total Amount : Rs.<span class="totalAmount">0</span></h5>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">Renew</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(function () {
            $('.renewForm').parsley();
            // get data table data
            $('.expiredAddTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.advertisements.expiredAdds.list') }}",
                    type: "POST",
                    data: function (data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [
                    {
                        data: 'user_name',
                        name: 'user_name',
                        render: function (data, type, row) {
                            return row.user.name ?? '--';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function (data, type, row) {
                            return row.screen_list ?? '--';
                        }
                    },
                    {
                        data: 'banner_image',
                        name: 'banner_image',
                        render: function (data, type, row) {
                            imageHtml = "";
                            if (row.banner_image) {

                                var image = '{{ asset('storage/banner') }}' + '/' + row
                                    .banner_image;
                                var imageHtml = `<div class="ad-photo">
                                                                        <a href="${image}"
                                                                            class="glightbox" data-glightbox="type: image">
                                                                            <img src="${image}"
                                                                                alt="image" />
                                                                        </a>
                                                                    </div>`;
                            }
                            return imageHtml ?? '--';
                        }
                    },


                    {
                        data: 'banner_url',
                        name: 'banner_url',
                        render: function (data, type, row) {
                            return row.banner_url ?? '';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function (data, type, row) {
                            var date = row.start_date ?? '--';

                            return date;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function (data, type, row) {
                            var date = row.start_date ?? '--';
                            var time = row.start_time ?? '--';

                            if (date !== '--' && time !== '--') {
                                // Convert from DD-MM-YYYY to YYYY-MM-DD
                                const [day, month, year] = date.split('-');
                                const formattedDate = `${year}-${month}-${day}`;

                                const dateTime = new Date(`${formattedDate}T${time}`);

                                const formattedTime = dateTime.toLocaleString('en-IN', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                }).toLowerCase(); // e.g., "09:21 am"

                                return formattedTime;
                            } else {
                                return time;
                            }
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function (data, type, row) {
                            var date = row.end_date ?? '--';

                            return date;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function (data, type, row) {
                            var date = row.end_date ?? '--';
                            var time = row.end_time ?? '--';

                            if (date !== '--' && time !== '--') {
                                // Convert from DD-MM-YYYY to YYYY-MM-DD
                                const [day, month, year] = date.split('-');
                                const formattedDate = `${year}-${month}-${day}`;

                                const dateTime = new Date(`${formattedDate}T${time}`);

                                const formattedTime = dateTime.toLocaleString('en-IN', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                }).toLowerCase(); // e.g., "09:21 am"

                                return formattedTime;
                            } else {
                                return time;
                            }
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function (data, type, row) {
                            return row.advertisement_user_clicks_count ?? '--';
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        render: function (data, type, row) {
                            var editUrl = '{{ route('admin.advertisements.edit', ':id') }}'
                                .replace(':id',
                                    row
                                        .id);
                            var html =
                                `
                                                                <a href="${editUrl}"
                                                                    class="btn btn-sm btn-outline-danger me-1 renewAd"
                                                                    data-bs-toggle="modal" data-bs-target="#renewAd" data-id="${row.id}">Renew</a>`;
                            return html;
                        }
                    }
                ],
                drawCallback: function () {
                    GLightbox({
                        touchNavigation: true,
                        loop: true,
                        width: "90vw",
                        height: "90vh"
                    });
                }
            });

            $(document).on('click', '.renewAd', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = "{{ route('admin.advertisements.getAdvertiserDetailsAmount') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {
                        id: id
                    },
                    success: function (res) {
                        if (res.status) {
                            var exportUrl =
                                "{{ route('admin.advertisements.expiredAdds.renew', ':advertisement') }}"
                                    .replace(
                                        ':advertisement', id);
                            $('.renewForm').attr('action', exportUrl);
                            $('.totalAmount').html(res.amount);
                            $('.totalAmountInput').val(res.amount);
                        }

                    }
                });
            });
        });

        // Date Range for Ads
        const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
            buttonClass: 'btn'
        });
    </script>
@endsection