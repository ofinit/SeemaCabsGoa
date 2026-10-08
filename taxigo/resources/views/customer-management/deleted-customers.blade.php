@extends('layouts.main')

@section('title', 'View Deleted Customers')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Customer Management</li>
                        <li class="breadcrumb-item" aria-current="page">View Deleted Customers</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">View Deleted Customers</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>Deleted Customers</h5>
                    <a href="{{ route('admin.customers.exportDeletedUserList') }}">
                        <button class="btn btn-sm btn-light-info">Export</button>
                    </a>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="deleteCustomerTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer Name</th>
                                    <th>Gender</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>Email ID</th>
                                    <th>Mobile No</th>
                                    <th>Reason</th>
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
        $('#deleteCustomerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.customers.deleteUserDetailsList') }}",
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
                    data: 'deleted_at',
                    name: 'deleted_at',

                },
                {
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        $imagePath = "{{ asset('build/images/user/avatar-1.jpg') }}";
                        if (row.image != null) {
                            $imagePath = "{{ asset('storage/customer/') }}" + "/" + row.image;
                        }
                        return `<div class="d-flex align-items-center gap-2">
                    <a href="${$imagePath}" class="glightbox">
                        <img src="${$imagePath}" alt="image"
                            class="img-radius customer-profile-image" />
                    </a>
                    ${row.name}
                </div>`;
                    }
                },
                {
                    data: 'gender',
                    name: 'gender',
                    render: function(data, type, row) {
                        $gender = 'Male';
                        if (row.gender == 0) {
                            $gender = 'Female';
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
                        return (row.get_country_details) ? row.get_country_details.name : '';
                    }
                },
                {
                    data: 'state_id',
                    name: 'state_id',
                    render: function(data, type, row) {
                        return (row.get_state_details) ? row.get_state_details.name : '';
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
                    data: 'delete_reason',
                    name: 'delete_reason',
                    render: function(data, type, row) {
                        return data && data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                },
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
    </script>
@endsection
