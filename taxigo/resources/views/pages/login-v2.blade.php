@extends('layouts.AuthLayout')

@section('title', 'Login')

@section('auth-v2', '')

@section('css')
<link href="{{ URL::asset('build/css/new-style.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('content')
<div class="auth-form">
  <div class="card my-5 mx-3">
    <div class="card-body">
      <div class="mb-3">
        <img src="{{ URL::asset('build/images/logo.svg') }}" class="img-brand img-fluid logo-img" alt="images">
      </div>
      <h4 class="f-w-500 mb-1">Login with your email</h4>
      <div class="mb-3">
        <input type="email" class="form-control" id="floatingInput" placeholder="Email Address">
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" id="floatingInput1" placeholder="Password">
      </div>
      <div class="d-flex mt-1 justify-content-between align-items-center">
        <div class="form-check">
          <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="">
          <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
        </div>
        <a href="{{ url('pages/forgot-password-v2') }}">
          <h6 class="text-secondary f-w-400 mb-0">Forgot Password?</h6>
        </a>
      </div>
      <div class="d-grid mt-4">
        <button type="button" class="btn btn-primary">Login</button>
      </div>
    </div>
  </div>
</div>
@endsection
