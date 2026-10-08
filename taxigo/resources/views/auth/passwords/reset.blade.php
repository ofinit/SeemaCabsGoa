@extends('layouts.AuthLayout')

@section('title', 'Reset Password')
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
    <div class="auth-form">
        <div class="card my-5">
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ getLogo() }}" alt="images" class="img-fluid mb-3">
                    <h4 class="f-w-500 mb-1">Reset password</h4>
                    <p class="mb-3">Back to <a href="{{ route('admin.login') }}" class="link-primary ms-1">Log in</a></p>
                </div>
                <div class="col-12">
                    @include('layouts.message')
                </div>
                <form class="updatePasswordForm" method="POST" action="{{ route('admin.password.update') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label">Password</label>
                        <input type="hidden" name="id" value="{{ $userId ?? '' }}" id="">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                            id="password" placeholder="Password" required data-parsley-required="true"
                            data-parsley-required-message="The password field is required." data-parsley-minlength="8"
                            data-parsley-minlength-message="Password must be at least 8 characters long."
                            data-parsley-errors-container="#password-error">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" id="confirm_password"
                            placeholder="Confirm Password" required data-parsley-required="true"
                            data-parsley-required-message="The confirm password field is required."
                            data-parsley-equalto="#password" data-parsley-equalto-message="Passwords do not match."
                            data-parsley-errors-container="#confirm-password-error">
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>

                </form>
                <div class="saprator my-3">
                    <span>Or continue with</span>
                </div>
                <div class="text-center">
                    <ul class="list-inline mx-auto mt-3 mb-0">
                        <li class="list-inline-item">
                            <a href="https://www.facebook.com/" class="avtar avtar-s rounded-circle bg-facebook"
                                target="_blank">
                                <i class="fab fa-facebook-f text-white"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://twitter.com/" class="avtar avtar-s rounded-circle bg-twitter" target="_blank">
                                <i class="fab fa-twitter text-white"></i>
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://myaccount.google.com/" class="avtar avtar-s rounded-circle bg-googleplus"
                                target="_blank">
                                <i class="fab fa-google text-white"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.updatePasswordForm').parsley();
        });
    </script>
@endsection
