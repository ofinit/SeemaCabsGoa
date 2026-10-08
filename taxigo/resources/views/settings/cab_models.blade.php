@extends('layouts.main')

@section('title', 'Cab Models')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Cab Models</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Cab Models</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @include('layouts.message')
        <div class="col-md-6">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Add Cab Model</h5>
                    </div>
                    <div class="card-body">
                        <form id="model-form" action="{{ route('admin.setting.cab.model.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="modelName">Cab Model Name</label>
                                        <input type="text" class="form-control" id="modelName" name="model_name"
                                            placeholder="Enter cab model name">
                                    </div>
                                    <input type="hidden" class="form-control" id="model_id" name="model_id">
                                </div>
                                <div class="col-12 text-end">
                                    <div class="my-3">
                                        <button type="submit" class="btn btn-primary">Add</button>
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
                        <h5>View Cab Models</h5>
                    </div>
                    <div class="card-body">
                        <div class="dt-responsive">
                            <table id="modelDataTable" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Cab Model Name</th>
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
        <div class="col-md-6">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Add Cab Color</h5>
                    </div>
                    <div class="card-body">
                        <form id="color-form" action="{{ route('admin.setting.cab.color.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="color-name">Cab Color Name</label>
                                        <input type="text" class="form-control" id="color-name" name="color_name"
                                            placeholder="Enter cab color name">
                                    </div>
                                    <input type="hidden" class="form-control" id="color_id" name="color_id">
                                </div>
                                <div class="col-12 text-end">
                                    <div class="my-3">
                                        <button type="submit" class="btn btn-primary">Add</button>
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
                        <h5>View Cab Colors</h5>
                    </div>
                    <div class="card-body">
                        <div class="dt-responsive">
                            <table id="colorDataTable" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Cab Color Name</th>
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
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        //Model
        $(document).ready(function() {
            $("#model-form").validate({
                rules: {
                    model_name: {
                        required: true,
                        maxlength: 100,
                        minlength: 2 // Minimum length of 2 characters
                    }
                },
                messages: {
                    model_name: {
                        required: "Please enter a model name",
                        minlength: "Model name must be at least 2 characters long"
                    }
                },
                submitHandler: function(form) {
                    form.submit(); // Submit the form if valid
                }
            });

            var table = $('#modelDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.setting.cab.model.list') }}",
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

        function confirmModelDelete(modelId, modelName, deleteUrl) {
            Swal.fire({
                icon: 'warning',
                html: 'Are you sure you want to delete the Model?',
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


        function modelEdit(modelId, name) {
            $('html, body').animate({
                scrollTop: $('#model-form').offset().top
            }, 500);

            $('#model_id').removeClass('d-none').val(modelId);
            $('#modelName').val(name);
        }
        //Color
        $(document).ready(function() {
            $("#color-form").validate({
                rules: {
                    color_name: {
                        required: true,
                        maxlength: 100,
                        minlength: 2 // Minimum length of 2 characters
                    }
                },
                messages: {
                    color_name: {
                        required: "Please enter a color name",
                        minlength: "Color name must be at least 2 characters long"
                    }
                },
                submitHandler: function(form) {
                    form.submit(); // Submit the form if valid
                }
            });
            var table = $('#colorDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.setting.cab.color.list') }}",
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

        function confirmColorDelete(colorId, colorName, deleteUrl) {
            Swal.fire({
                icon: 'warning',
                html: 'Are you sure you want to delete the Color?',
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

        function colorEdit(colorId, name) {
            $('html, body').animate({
                scrollTop: $('#color-form').offset().top
            }, 500);

            $('#color_id').removeClass('d-none').val(colorId);
            $('#color-name').val(name);
        }
    </script>
    <script>
        // [ DOM/jquery ]


        // Delete Button Sweet Alert
    </script>
    <!-- [Page Specific JS] end -->
@endsection
