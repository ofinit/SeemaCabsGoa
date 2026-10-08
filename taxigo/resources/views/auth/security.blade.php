@extends('layouts.AuthLayout')

@section('title', 'Security')

@section('content')
    <div class="auth-form">
        <div class="card my-5">
            <div class="card-body">
                <div class="text-center">
                    <div class="form-check form-switch custom-switch-v1 d-flex flex-column ps-0 gap-2">
                        <label class="form-check-label" for="customswitchv2-1">Enable Two Fector Authentication</label>
                        <input type="checkbox" class="form-check-input input-primary mx-auto" id="customswitchv2-1">
                    </div>
                    <img src="{{ asset('build/images/barcode.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection
