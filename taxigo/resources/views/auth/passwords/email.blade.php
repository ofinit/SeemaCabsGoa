@extends('layouts.AuthLayout')

@section('title', 'Forgot password')

@section('content')
    <div class="auth-form">
        <div class="card my-5">
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ getLogo() }}" alt="images" class="img-fluid mb-3">
                    <h4 class="f-w-500 mb-1">Forgot Password?</h4>
                    <p class="mb-3">Back to <a href="{{ route('admin.login') }}" class="link-primary ms-1">Log in</a></p>
                </div>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="col-12">
                    @include('layouts.message')
                </div>
                <form method="POST" action="{{ route('admin.reset.password') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" required autocomplete="email" autofocus id="floatingInput"
                            placeholder="Email Address">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
