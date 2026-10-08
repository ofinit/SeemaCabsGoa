@extends('layouts.main')

@section('title', 'View Cab Details')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">Cab Management</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Cab Management</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- DOM/Jquery table start -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Cab Details</h5>
                        <a href="{{ route('admin.cabs.exportCabList') }}">
                            <button class="btn btn-sm btn-light-info">Export</button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="viewCabsTable" class="table table-striped table-bordered nowrap viewCabsTable">
                            <thead>
                                <tr>
                                    {{-- @if (Auth::user()->type == App\Enums\Type::ADMIN)
                                        <th>Fleet Operator</th>
                                        <th>Cab Zone</th>
                                    @else
                                        <th>Cab Zone</th>
                                        @endif --}}
                                    <th>Cab Zone</th>
                                    <th>Cab Type</th>
                                    <th>Model Name</th>
                                    <th>Color</th>
                                    <th>Cab Number</th>
                                    <th>Driver Name</th>
                                    <th>Driver Mobile</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- DOM/Jquery table end -->

        <!-- Model -->
        <div class="modal fade" id="cabDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">View Cab Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <span class="bodyDetails"></span>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        // Data Table
        $(document).ready(function() {
            var table = $('.viewCabsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.cabs.list') }}",
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
                columns: [
                    // {
                    //     data: 'fleet_operator_id',
                    //     name: 'fleet_operator_id',
                    //     render: function(data, type, row) {
                    //         return row.get_fleet_operator_details ? row.get_fleet_operator_details
                    //             .name : '-';
                    //     }
                    // },
                    {
                        data: 'city_list',
                        name: 'city_list',
                        render: function(data, type, row) {
                            return row.city_list;
                        }
                    },
                    {
                        data: 'type',
                        name: 'type',
                        render: function(data, type, row) {
                            $type = 'Hatchback';
                            if (row.type == 2) {
                                $type = 'Sedan';
                            } else if (row.type == 3) {
                                $type = 'SUV';
                            }
                            return $type;
                        }
                    },
                    {
                        data: 'model_id',
                        name: 'model_id',
                        render: function(data, type, row) {
                            return row.get_cab_model_details?row.get_cab_model_details.name:'';
                        }
                    },
                    {
                        data: 'color_id',
                        name: 'color_id',
                        render: function(data, type, row) {
                            return row.get_color_details.name;
                        }
                    },
                    {
                        data: 'number',
                        name: 'number',
                        render: function(data, type, row) {
                            return row.number;
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.get_assigned_driver_details ? row.get_assigned_driver_details
                                .name : '';
                        }
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            return row.get_assigned_driver_details ? row.get_assigned_driver_details
                                .mobile : '';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            $status = '<h5><span class="badge text-bg-success">Active</span></h5>';
                            if (row.status == 0) {
                                $status =
                                    ' <h5><span class="badge text-bg-danger">Suspend</span></h5>';
                            }
                            return $status;
                        }
                    },
                    {
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row) {
                            var editUrl = '{{ route('admin.cabs.edit', ':id') }}'.replace(':id',
                                row
                                .id);
                            var viewUrl = '{{ route('admin.cabs.view', ':id') }}'.replace(':id',
                                row
                                .id);
                            var suspend = '{{ route('admin.cabs.suspend', ':id') }}'.replace(':id',
                                row.id);
                            var html = `  <a href="#!" class="btn btn-sm btn-light-primary me-1 getCabDetails" data-url="${viewUrl}" data-bs-toggle="modal"
                                            data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                        <a href="${editUrl}"
                                            class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                        <a href="#!" class="btn btn-sm btn-light-danger sa-bs-suspend-ico suspendDriver" data-url="${suspend}"><i
                                                class="feather icon-slash"></i></a>`;
                            return html;
                        }
                    }
                ]
            });
            var admin = "<?php echo Auth::user()->type == App\Enums\Type::ADMIN ? 'true' : 'false'; ?>";
            if (admin === 'true') {
                table.column('fleet_operator_id:name').visible(true);
            } else {
                table.column('fleet_operator_id:name').visible(false);
            }

        });


        // Suspend Button Sweet Alert
        document.querySelectorAll('.sa-bs-suspend-ico').forEach(function(element) {
            element.addEventListener('click', function() {
                Swal.fire({
                    icon: 'error',
                    html: 'Are You Sure! You want to suspend Cab Number : <b>GA08X9012</b>',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                })
            });
        });

        // Unsuspend Button Sweet Alert
        document.querySelectorAll('.sa-bs-unsuspend-ico').forEach(function(element) {
            element.addEventListener('click', function() {
                Swal.fire({
                    icon: 'warning',
                    html: 'Are You Sure! You want to un-suspend Cab Number : <b>GA08X9012</b>',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Proceed',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                })
            });
        });
    </script>
    <script>
        $(function() {
            $(document).on('click', '.getCabDetails', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(res) {
                        if (res.status) {
                            $('.bodyDetails').html(res.html);
                        }
                    }
                });
            });
        });
        $(document).ready(function() {
            $(document).on("click", ".glightbox", function(event) {
                event.preventDefault();
                GLightbox({
                    selector: ".glightbox"
                });
            });

            // suspend 
            $(document).on('click', '.suspendDriver', function(e) {
                e.preventDefault();
                var url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't changed this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "GET",
                            success: function(response) {
                                Swal.fire("Success!",
                                    "Cab has been suspended successfully.",
                                    "success");
                                location.reload();
                            },
                            error: function() {
                                Swal.fire("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
