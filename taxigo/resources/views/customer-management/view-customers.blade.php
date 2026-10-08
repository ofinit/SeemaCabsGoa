@extends('layouts.main')

@section('title', 'View Customer Details')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Customer Management</li>
                        <li class="breadcrumb-item" aria-current="page">View Customer Details</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">View Customer Details</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- View CUstomers -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Customer Details</h5>
                        <a href="{{ route('admin.customers.exportUserList') }}">
                            <button class="btn btn-sm btn-light-info">Export</button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="customerTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Gender</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>Email ID</th>
                                    <th>Mobile No</th>
                                    <th>Device</th>
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
    </div>

@endsection

@section('scripts')
    <script>
        $('#customerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.customers.list') }}",
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
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        var imagePath = "{{ asset('build/images/user/avatar-1.jpg') }}";
                        if (row.image !== null && row.image !== '') {
                            imagePath = "{{ asset('storage/customer') }}/" + row.image;
                        }

                        return `
                            <div class="d-flex align-items-center gap-2">
                                <a href="${imagePath}" class="glightbox">
                                    <img src="${imagePath}" alt="User Image"
                                        class="img-radius customer-profile-image" />
                                </a>
                                <div>
                                    <div><strong>${row.name ?? ''}</strong></div>
                                    <div><small><strong>Reg. Date:</strong> ${row.created_at}</small></div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    data: 'gender',
                    name: 'gender',
                    render: function(data, type, row) {
                        $gender = '';
                        if (row.gender == 0) {
                            $gender = 'Female';
                        } else if (row.gender == 1) {
                            $gender = 'Male';
                        } else if (row.gender == 2) {
                            $gender = 'Other';
                        }
                        return $gender;
                    }
                },
                {
                    data: 'country_id',
                    name: 'country_id',
                    render: function(data, type, row) {
                        return row.get_country_details ? row.get_country_details.name : '';
                    }
                },
                {
                    data: 'state_id',
                    name: 'state_id',
                    render: function(data, type, row) {
                        return row.get_state_details ? row.get_state_details.name : '';
                    }
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number',
                    render: function(data, type, row) {
                        return (row.phone_number) ? '+91' + row.phone_number : '';
                    }
                },
                {
                    data: 'device',
                    name: 'device'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        return (row.status) ?
                            ` <h5><span class="badge text-bg-success">Active</span></h5>` :
                            ` <h5><span class="badge text-bg-danger">Suspended</span></h5>`;
                    }
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, row) {
                        var changeUrl = '{{ route('admin.customers.changeStatus', ':id') }}'.replace(':id',
                            row.id);
                        var html = (row.status) ?
                            ` <button class="btn btn-sm btn-outline-danger sa-bs-error-ico changeStatus" data-url="${changeUrl}">Suspend</button>` :
                            `<button class="btn btn-sm btn-outline-success sa-bs-unsuspend-ico changeStatus" data-url="${changeUrl}">Un-suspend</button>`;
                        return html;
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
        // delete
        $(document).on('click', '.changeStatus', function(e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't change this status!",
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
                            Swal.fire("Changed!", "Status changed successfully.", "success");
                            location.reload();
                        },
                        error: function() {
                            Swal.fire("Error!", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });
    </script>
@endsection
