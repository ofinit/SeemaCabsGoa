@extends('layouts.main')

@section('title', 'SMTP')

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
                        <li class="breadcrumb-item" aria-current="page">SMTP</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">SMTP</h2>
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
                    <h5>Add SMTP</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form method="POST" class="PlateFormTaxForm" action="{{ route('admin.setting.storeSmtpCred') }}">
                            @csrf
                            @php
                                $sHostExist = 0;
                                $sPortExist = 0;
                                $sPasswordExist = 0;
                                $sUserNameExist = 0;
                                $sAuthExist = 0;
                            @endphp
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="enterHost">Host</label>
                                        <input type="hidden" name="title[]" value="smtpHost" id="">
                                        <input type="text" name="value[]"
                                            value="{{ isset($envoirements) && isset($envoirements['smtphost']) ? $envoirements['smtphost'] : '' }}"
                                            class="form-control" id="enterHost" placeholder="Enter host" required
                                            data-parsley-required-message="Host is required."
                                            data-parsley-errors-container="#Host-error-container">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="enterPost">Port</label>
                                        <input type="hidden" name="title[]" value="smtpPort" id="">
                                        <input type="text" name="value[]" class="form-control" id="enterPort"
                                            placeholder="Enter port" required
                                            value="{{ isset($envoirements) && isset($envoirements['smtpport']) ? $envoirements['smtpport'] : '' }}"
                                            data-parsley-required-message="Port is required.">

                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="enterUsername">Username</label>
                                        <input type="hidden" name="title[]" value="smtpUserName" id="">
                                        <input type="text" name="value[]" class="form-control" id="enterUsername"
                                            placeholder="Enter user name" required
                                            value="{{ isset($envoirements) && isset($envoirements['smtpusername']) ? $envoirements['smtpusername'] : '' }}"
                                            data-parsley-required-message="Username is required.">

                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="enterPassword">Password</label>
                                        <input type="hidden" name="title[]" value="smtpPassword" id="">
                                        <input type="text" name="value[]" class="form-control" id="enterUsername"
                                            placeholder="Enter user name" required
                                            value="{{ isset($envoirements) && isset($envoirements['smtppassword']) ? $envoirements['smtppassword'] : '' }}"
                                            data-parsley-required-message="Username is required.">


                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        @php
                                            $authenticationList = getSmtpAuthentication();
                                        @endphp
                                        <label class="form-label" for="authentication">Authentication</label>
                                        <input type="hidden" name="title[]" value="smtpAuthentication" id="authentication">
                                        <select name="value[]" class="form-select" id="authentication" required
                                            data-parsley-required-message="Authentication is required.">
                                            <option value="">Select authentication</option>
                                            @if (isset($authenticationList) && count($authenticationList) > 0)
                                                @foreach ($authenticationList as $item)
                                                    <option value="{{ $item ?? '' }}"
                                                        {{ isset($envoirements) && isset($envoirements['smtpauthentication']) && $envoirements['smtpauthentication'] == $item ? 'selected' : '' }}>
                                                        {{ $item ?? '' }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="fromName">Form Name</label>
                                        <input type="hidden" name="title[]" value="fromName" id="">
                                        <input type="text" name="value[]" class="form-control" id="fromName"
                                            placeholder="Enter From address" required
                                            value="{{ isset($envoirements) && isset($envoirements['fromname']) ? $envoirements['fromname'] : '' }}"
                                            data-parsley-required-message="From name is required.">
                                    </div>
                                </div>
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
    <script>
        $(document).ready(function() {
            $('.PlateFormTaxForm').parsley();
        });
    </script>
@endsection
