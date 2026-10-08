@extends('layouts.main')

@section('title', 'Security')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.css">
    <style>
        .imagePreview {
            height: 100px;
            width: 100px;
            margin-top: 10px;
            border: none;
        }

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

        .imagePreview {
            display: none;
            width: 200px;
            height: 200px;
            object-fit: contain;
            border-radius: 12px;
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
                        <li class="breadcrumb-item">Security</li>
                        <li class="breadcrumb-item" aria-current="page">2FA Security</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">2FA Security</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
            <div class="alert alert-success alert-dismissible fade show d-none enabledMessage" role="alert">
                <strong>Success!</strong> <span class="message"></span>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    @php
                        $user = auth()->user();
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <h5>Enable 2FA Security</h5>
                        <div class="form-check form-switch custom-switch-v1">
                            <input type="checkbox" value="1" {{ $user->security ? 'checked' : '' }}
                                class="form-check-input input-success enableSecurity" id="surgePricingSwitch">
                        </div>
                    </div>
                </div>
                <div class="card-body qrContainer d-none">
                    <div class="container">
                        <div class="row">
                            <div class="col-6">
                                <h2>QR Code</h2>
                                <span class="qrCode"></span>
                            </div>
                            <div class="col-6">
                                <h2></h2>
                                <form class="otpForm" method="POST" action="{{ route('admin.security.otpVerify') }}">
                                    @csrf
                                    <label for="">OTP</label>
                                    <input type="number" class="form-control" name="otp" id=""
                                        placeholder="Enter otp here..." required
                                        data-parsley-required-message="Otp is required.">
                                    <button type="submit" class="btn btn-primary mt-2">Verify</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <!-- DataTables JS -->
    <script type="module">
        $(function() {
            $('.otpForm').parsley();

            $('.enableSecurity').on('change', function(e) {
                e.preventDefault();
                $('.enabledMessage').addClass('d-none');
                $('.qrContainer').addClass('d-none');
                var enable = false;
                if ($(this).prop('checked')) {
                    enable = true;

                }

                $.ajax({
                    url: "{{ route('admin.security.qrCodeGenerate') }}",
                    type: 'GET',
                    data: {
                        enable: enable
                    },
                    success: function(success) {
                        if (success.status) {
                            if (success.qr) {
                                $('.qrContainer').removeClass('d-none');
                                $('.qrCode').html(html);
                                var html = `<img src="${success.qr}" alt="QR Code">`;
                                $('.qrCode').html(html);
                            }
                            $('.enabledMessage').removeClass('d-none');
                            $('.message').html(success.message);
                        }
                    }
                });
            });
        });
    </script>
@endsection
