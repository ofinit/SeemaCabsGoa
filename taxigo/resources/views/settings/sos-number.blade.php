@extends('layouts.main')

@section('title', 'SOS Number')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">SOS Number</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">SOS Number</h2>
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
                <div class="card-header">
                    <h5>SOS Number</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form method="POST" class="SosNumberForm" action="{{ route('admin.setting.storeSosNumbers') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="title" value="sosNumber" id="">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="sosNumber">Enter SOS Number</label>
                                    <input type="number" name="sosNumber" value="{{ $sosNumber ? $sosNumber->value : '' }}"
                                        id="sosNumber" class="form-control" placeholder="Enter sos number">
                                </div>
                                <label id="sosNumber-error" class="error text-danger" for="sosNumber"></label>
                            </div>
                            <div class="col-12 text-end">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        $(function() {
            // Submit 
            $('.SosNumberForm').validate({
                rules: {
                    sosNumber: {
                        required: true,
                        max: 200,
                    }
                },
                messages: {
                    sosNumber: {
                        required: "Please enter sos number.",
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
    </script>
@endsection
