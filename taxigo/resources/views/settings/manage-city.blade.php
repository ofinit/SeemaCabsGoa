@extends('layouts.main')

@section('title', 'Manage Cities/Cab Zones')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Manage Cities/Cab Zones</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Manage Cities/Cab Zones</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row">
            @include('layouts.message')
            <div class="card">
                <div class="card-header">
                    <h5>Add City</h5>
                </div>
                <div class="card-body">
                    <form id="city-form" action="{{ route('admin.setting.city.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="city-name">City Name</label>
                                    <input type="text" class="form-control" id="city_name" name="city_name"
                                        placeholder="Enter city name">
                                </div>

                            </div>
                            <input type="hidden" class="d-none" id="city_id" name="city_id">
                            <div class="col-12 text-end">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>View Cities</h5>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="cityDataTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>City Name</th>
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

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#city-form").validate({
                rules: {
                    city_name: {
                        required: true,
                        maxlength: 100,
                        minlength: 2 // Minimum length of 2 characters
                    }
                },
                messages: {
                    city_name: {
                        required: "Please enter a city name",
                        minlength: "City name must be at least 2 characters long"
                    }
                },
                submitHandler: function(form) {
                    form.submit(); // Submit the form if valid
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#cityDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.setting.city.list') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
    <script>
        function confirmDelete(cityId, cityName, deleteUrl) {
            Swal.fire({
                icon: 'warning',
                html: 'Are you sure you want to delete the city: <strong>' + cityName + '</strong>?',
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the delete URL
                    window.location.href = deleteUrl; // Redirect to the delete URL
                }
            });
        }


        function edit(cityId, name) {
            // Scroll to the city form using jQuery
            $('html, body').animate({
                scrollTop: $('#city-form').offset().top
            }, 500);

            $('#city_id').removeClass('d-none').val(cityId);
            $('#city_name').val(name);
        }
    </script>
    <!-- [Page Specific JS] end -->
@endsection
