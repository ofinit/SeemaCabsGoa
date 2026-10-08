@extends('layouts.main')

@section('title', 'App Update')
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
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">General Settings</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">General Settings</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form class="appUpdateForm" method="post" action="{{ route('admin.setting.save') }}">
        @csrf
        <div class="row">
            <div class="col-12">
                @include('layouts.message')
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <h5>Google Login</h5>
                            <hr>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label" for="google_client_id">Client ID</label>
                                <input type="text" name="google_client_id" id="google_client_id" class="form-control"
                                    placeholder="Enter Client ID" required value="{{ @$envoirements['google_client_id'] }}"
                                    data-parsley-required-message="App Client ID required.">
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label" for="google_client_secret">Client Secret Key</label>
                                <input type="text" name="google_client_secret" id="google_client_secret"
                                    class="form-control" placeholder="Enter Client Secret" required
                                    value="{{ @$envoirements['google_client_secret'] }}"
                                    data-parsley-required-message="App Client Secret required.">
                            </div>

                            <div class="col-12 text-end">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.appUpdateForm').parsley();
        });
    </script>
@endsection
