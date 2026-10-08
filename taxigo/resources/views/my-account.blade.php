@extends('layouts.main')
@section('title', 'Account')

@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dropzone.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.css">
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

        .imagePreview {
            display: none;
            width: 200px;
            height: 200px;
            margin-top: 10px;
            object-fit: contain;
            border-radius: 12px;
        }

        .logoImage {
            width: 200px;
            height: 200px;
            object-fit: contain;
            border-radius: 12px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            {{-- <div class="card bg-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 me-3">
                            <h3 class="text-white">Password Changed Successfully</h3>
                            <p class="text-white text-opacity-75 text-opa mb-0">Your password has been changed.</p>
                        </div>
                        <div class="flex-shrink-0">
                            <img src="{{ URL::asset('build/images/application/img-accout-alert.png') }}" alt="img"
                                class="img-fluid wid-80">
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <form method="POST" class="profileUpdateImageForm" action="{{ route('admin.updateProfileImage') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Update Profile Image</h5>
                            </div>
                            <div class="card-body">
                                <label class="card-upload-box" for="register-back">
                                    <p>Upload Your Profile</p>
                                </label>
                                <input type="file" name="image" class="d-none" id="register-back" accept="image/*"
                                    required data-parsley-required-message="Please choose profile image.">
                                <img class="imagePreview" id="registerBackPreview" />
                                <span id="registerBackError" class="text-danger"></span>
                                @if ($user->image)
                                    <img class="logoImage" src="{{ $user->user_image }}" alt="">
                                @endif
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body text-end">
                                {{-- <div class="btn btn-outline-secondary me-2">Cancel</div>
                                <div class="btn btn-primary">Update Profile Image</div> --}}
                                <button type="submit" class="btn btn-primary">Update Profile Image</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-12">
                    <form method="POST" class="passwordUpdateForm" action="{{ route('admin.changePassword') }}">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h5>Change Password</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item pt-0 px-0">
                                        <div class=" row mb-0">
                                            <label class="col-form-label col-md-2 col-sm-12 text-md-end">Current Password
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-md-10 col-sm-12">
                                                <input type="password" name="old_password" class="form-control" required
                                                    data-parsley-required-message="Please enter your current password.">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item px-0">
                                        <div class="row mb-0">
                                            <label class="col-form-label col-md-2 col-sm-12 text-md-end">New Password <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-md-10 col-sm-12">
                                                <input type="password" name="new_password" class="form-control" required
                                                    data-parsley-required-message="Please choose a new password."
                                                    data-parsley-minlength="8"
                                                    data-parsley-minlength-message="Password must be at least 8 characters.">
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item pb-0 px-0">
                                        <div class="row mb-0">
                                            <label class="col-form-label col-md-2 col-sm-12 text-md-end">Confirm Password
                                                <span class="text-danger">*</span></label>
                                            <div class="col-md-10 col-sm-12">
                                                <input type="password" name="confirm_password" class="form-control" required
                                                    data-parsley-equalto="[name='new_password']"
                                                    data-parsley-equalto-message="Password do not match."
                                                    data-parsley-required-message="Please enter confirm password.">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body text-end">
                                {{-- <div class="btn btn-outline-secondary me-2">Cancel</div>
                                <div class="btn btn-primary">Change Password</div> --}}
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
    <!-- [ Main Content ] end -->
@endsection


@section('scripts')
    <script src="{{ URL::asset('build/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
    <script>
        $(function() {
            $('.profileUpdateImageForm').parsley();
            $('.passwordUpdateForm').parsley();

            $('#register-back').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#registerBackPreview');
                $('#registerBackError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('#registerBackError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
