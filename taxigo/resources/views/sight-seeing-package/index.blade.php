@extends('layouts.main')

@section('title', 'Sightseeing Packages List')

@section('css')
    <!-- Toastr CSS -->
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" />
    <style>

    </style>
    <!-- DataTables CSS -->
@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Sightseeing Packages List</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Sightseeing Packages List</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin.sightseeingPackages.manage') }}" style="text-decoration: none">
                            <button type="button" class="btn btn-light-primary">Add Sight Seeing Package</button>
                        </a>
                    </div>
                    <div class="dt-responsive">
                        <table id="sightSeeingDataTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Sr No</th>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const table = $('#sightSeeingDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.sightseeingPackages.list') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                searching: true,
                pageLength: 10,
                columns: [
                    {
                        data: null,
                        name: '#',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'default',
                        name: 'default',
                        render: function(data, type, row) {
                            let editUrl = '{{ route('admin.sightseeingPackages.edit', ':id') }}'
                                .replace(':id', row.id);
                            return `
                            <a href="${editUrl}" class="btn btn-sm btn-light-primary me-1" title="Edit">
                                <i class="feather icon-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-light-danger delete-btn"
                                data-id="${row.id}" data-title="${row.title}" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>`;
                        }
                    }
                ]
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const title = $(this).data('title');
                confirmDelete(id, title, table);
            });
        });

        // Confirm delete popup
        function confirmDelete(id, title, table) {
            Swal.fire({
                icon: 'warning',
                html: `Are you sure you want to delete the package: <strong>${title}</strong>?`,
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    let deleteUrl = '{{ route('admin.sightseeingPackages.delete', ':id') }}'.replace(':id', id);
                    $.ajax({
                        url: deleteUrl,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message || 'Package deleted successfully');
                                table.ajax.reload(null, false);
                            } else {
                                toastr.error(response.message || 'Something went wrong');
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to delete the package. Please try again.');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        }
    </script>
@endsection
