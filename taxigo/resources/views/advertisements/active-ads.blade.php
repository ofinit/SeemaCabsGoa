@extends('layouts.main')

@section('title', 'Active Ads')

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
                        <li class="breadcrumb-item" aria-current="page">Active Ads</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Active Ads</h2>
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
                    <h5>Active Ads</h5>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="activeAddTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Advertiser</th>
                                    <th>Ad Screen</th>
                                    <th>Ad Image</th>
                                    <th>Banner URL</th>
                                    <th>Start Date</th>
                                    <th>Start Time</th>
                                    <th>End Date</th>
                                    <th>End Time</th>
                                    <th>No. of Clicks</th>
                                    <th>Views</th>
                                    <th>Status</th>
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
                            <div class="col-12">
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
                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

    <!-- View Number of Leads -->
    <div class="modal fade" id="viewAds" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Number of Leads</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="dt-responsive">
                        <table id="viewNoLeads" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Email ID</th>
                                    <th>Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>31-01-2025</td>
                                    <td>John Smith</td>
                                    <td>+91 9874563210</td>
                                    <td>johnsmith123@gmail.com</td>
                                    <td><a href="#"> <i data-feather="share-2"></i></a></td>
                                </tr>
                                <tr>
                                    <td>31-01-2025</td>
                                    <td>John Smith</td>
                                    <td>+91 9874563210</td>
                                    <td>johnsmith123@gmail.com</td>
                                    <td><a href="#"> <i data-feather="share-2"></i></a></td>
                                </tr>
                                <tr>
                                    <td>31-01-2025</td>
                                    <td>John Smith</td>
                                    <td>+91 9874563210</td>
                                    <td>johnsmith123@gmail.com</td>
                                    <td><a href="#"> <i data-feather="share-2"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
    <div id="modal-container">
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(function () {
            $('.renewForm').parsley();
            // get datatable data
            $('#activeAddTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.advertisements.activeAdds.list') }}",
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
                            var image = '{{ asset('storage/banner') }}' + '/' + row
                                .banner_image;
                            var imageHtml = `<div class="ad-photo">
                                                                                    <a href="${image}"
                                                                                        class="glightbox" data-glightbox="type: image">
                                                                                        <img src="${image}"
                                                                                            alt="image" />
                                                                                    </a>
                                                                                </div>`;
                            return imageHtml;
                        }
                    },


                    {
                        data: 'banner_url',
                        name: 'banner_url',
                        render: function (data, type, row) {
                            return row.banner_url;
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
                        data: 'views',
                        name: 'views',
                        orderable: false,
                        render: function (data, type, row) {
                            return (row.views ?? 0) + ' <small class="text-muted">(CTR ' + (row.ctr ?? '--') + ')</small>';
                        }
                    },
                    {
                        data: 'approval_status',
                        name: 'approval_status',
                        orderable: false,
                        render: function (data, type, row) {
                            var paused = row.approval_status === 'paused';
                            var badge = paused ? '<span class="badge bg-light-warning">Paused</span>' : '<span class="badge bg-light-success">Live</span>';
                            if (row.payment_status && row.payment_status !== 'paid') {
                                badge += ' <span class="badge bg-light-danger">Unpaid</span>';
                            }
                            return badge;
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
                                <a href="javascript:void(0)"
                                    class="btn btn-sm btn-light-primary me-1 view-lead" data-id="${row.id}"><i class="feather icon-eye"></i></a>

                                <a href="${editUrl}"
                                    class="btn btn-sm btn-light-success me-1 renewAd"
                                    data-bs-toggle="modal" data-bs-target="#renewAd" data-id="${row.id}"><i class="feather icon-edit"></i></a>

                                <a href="javascript:void(0)" title="${row.approval_status === 'paused' ? 'Resume' : 'Pause'}"
                                    class="btn btn-sm btn-light-warning me-1 toggle-ad-status" data-id="${row.id}"><i class="feather icon-${row.approval_status === 'paused' ? 'play' : 'pause'}"></i></a>`;
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
                var exportUrl = "{{ route('admin.advertisements.activeAdds.renew', ':advertisement') }}"
                    .replace(
                        ':advertisement', id);
                $('.renewForm').attr('action', exportUrl);
            });

            $(document).on('click', '.toggle-ad-status', function (e) {
                e.preventDefault();
                var btn = $(this);
                if (btn.data('busy')) return;
                btn.data('busy', true);
                $.ajax({
                    url: "{{ route('admin.advertisements.activeAdds.toggleStatus', ':advertisement') }}".replace(':advertisement', btn.data('id')),
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        toastr.success(res.message);
                        $('#activeAddTable').DataTable().ajax.reload(null, false);
                    },
                    error: function (error) {
                        toastr.error((error.responseJSON && error.responseJSON.message) || 'Something went wrong.');
                    },
                    complete: function () { btn.data('busy', false); }
                });
            });
        });
        $(document).ready(function () {
            $('#activeAddTable').on('click', '.view-lead',function (e) {
                var id = $(this).data('id');
                viewLeadListModal(id);
            });
        });
        function viewLeadListModal(id = null) {
            $.ajax({
                type: "post",
                url: "{{ route('admin.advertisements.activeAdds.viewLeads') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    id:id,
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == true) {
                        $('#modal-container').empty();
                        $('#modal-container').html(response.view);
                        $("#viewLeadsModal").modal('show');
                    }
                },
                error:function (error){
                    if(error.responseJSON.status == false)
                    {
                        toastr.error(error.responseJSON.message);
                    }
                }
            });
        }
        // // Data Table
        // var table = $('#viewAdsDetails').DataTable();
        // var table = $('#viewNoLeads').DataTable();

        // // GLightBox
        // const lightbox = GLightbox({
        //     touchNavigation: true,
        //     loop: true,
        //     width: "90vw",
        //     height: "90vh"
        // });

        // // Delete Button Sweet Alert
        // document.querySelectorAll('.sa-bs-error-ico').forEach(function(element) {
        //     element.addEventListener('click', function() {
        //         Swal.fire({
        //             icon: 'error',
        //             title: 'Are You Sure!',
        //             showCancelButton: true,
        //             confirmButtonText: 'Yes, Proceed',
        //             cancelButtonText: 'Cancel',
        //             confirmButtonColor: '#3085d6',
        //             cancelButtonColor: '#d33'
        //         })
        //     });
        // });

        // // Select with Search
        // document.addEventListener('DOMContentLoaded', function() {
        //     var element = document.querySelector('#selectAdvertisers');
        //     new Choices(element, {
        //         searchPlaceholderValue: 'Search Advertisers'
        //     });

        //     var element = document.querySelector('#selectPages');
        //     new Choices(element, {
        //         searchPlaceholderValue: 'Search Page'
        //     });
        // })

        // // Date Range for Ads
        const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
            buttonClass: 'btn'
        });
    </script>
    <!-- [Page Specific JS] end -->
@endsection
