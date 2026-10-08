@extends('layouts.main')

@section('title', 'Logo')

@section('css')
    <style>
        .imagePreview {
            display: none;
            width: 200px;
            height: 200px;
            object-fit: contain;
            border-radius: 12px;
        }

        .LogoImage {
            width: 200px;
            height: 200px;
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
                        <li class="breadcrumb-item" aria-current="page">Logo</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Logo</h2>
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
                    <h5>Splash Screen Logo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="splashScreenLogoForm"
                        action="{{ route('admin.setting.logos.splashScreenLogo') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="title" value="splashScreenLogo" id="">
                        <div>
                            <label class="card-upload-box" for="splashScreenLogo">
                                <img class="imagePreview" id="imagePreview" />
                                @if (isset($envirements) && count($envirements) > 0)
                                    @foreach ($envirements as $list)
                                        @if (!empty($list->splash_logo_image))
                                            <img class="LogoImage splashScreenLogoLogoImage" id=""
                                                src="{{ $list->splash_logo_image ?? '' }}" />
                                        @endif
                                    @endforeach
                                @endif
                                <div class="error-message" style="color: red; display: none;"></div>
                                <p class="logo-upload-msg">Upload Splash Screen Logo</p>
                                <span class="logo-upload-msg">Please upload a logo with dimensions 200×200 pixels.</span>
                                <span class="splashScreenLogoError text-danger"></span>
                            </label>
                            <input type="file" class="d-none" name="splashScreenLogo" id="splashScreenLogo"
                                accept="image/png, image/gif, image/jpeg, image/svg">
                        </div>
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary splashScreenLogoSubmit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>App Logo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="AppLogoForm" action="{{ route('admin.setting.logos.appLogo') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="title" value="AppLogo" id="">
                        <div>
                            <label class="card-upload-box" for="appLogo">
                                <img class="imagePreview" id="appLogoImagePreview" />
                                @if (isset($envirements) && count($envirements) > 0)
                                    @foreach ($envirements as $list)
                                        @if (!empty($list->app_logo_image))
                                            <img class="appLogoLogoImage LogoImage" id=""
                                                src="{{ $list->app_logo_image ?? '' }}" />
                                        @endif
                                    @endforeach
                                @endif
                                <div class="error-message" style="color: red; display: none;"></div>
                                <p class="logo-upload-msg">Upload App Logo</p>
                                <span class="logo-upload-msg">Please upload a logo with dimensions 200×200 pixels.</span>
                                <span class="appLogoError text-danger"></span>
                            </label>
                            <input type="file" class="d-none" name="appLogo" id="appLogo" accept="image/*">
                        </div>
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Driver App Splash Screen Logo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="DriverSplashLogoForm"
                        action="{{ route('admin.setting.logos.driverSplashLogo') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="title" value="DriverSplashLogo" id="">
                        <div>
                            <label class="card-upload-box" for="driverSplashLogo">
                                <img class="imagePreview" id="driverSplashLogoImagePreview" />
                                @if (isset($envirements) && count($envirements) > 0)
                                    @foreach ($envirements as $list)
                                        @if (!empty($list->driver_splash_logo_image))
                                            <img class="driverSplashLogoLogoImage LogoImage" id=""
                                                src="{{ $list->driver_splash_logo_image ?? '' }}" />
                                        @endif
                                    @endforeach
                                @endif
                                <p class="logo-upload-msg">Upload Driver App Splash Screen Logo</p>
                                <span class="logo-upload-msg">Please upload a logo with dimensions 200×200 pixels.</span>
                                <span class="driverAppError text-danger"></span>
                            </label>
                            <input type="file" class="d-none" name="driverSplashLogo" id="driverSplashLogo"
                                accept="image/*">
                        </div>
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Driver App Logo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="DriverAppLogoForm"
                        action="{{ route('admin.setting.logos.driverAppLogo') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="title" value="driverAppLogo" id="">
                        <div>
                            <label class="card-upload-box" for="driverAppLogo">
                                <img class="imagePreview" id="driverAppLogoImagePreview" />
                                @if (isset($envirements) && count($envirements) > 0)
                                    @foreach ($envirements as $list)
                                        @if (!empty($list->driver_app_logo_image))
                                            <img class="driverAppLogoLogoImage LogoImage" id=""
                                                src="{{ $list->driver_app_logo_image ?? '' }}" />
                                        @endif
                                    @endforeach
                                @endif
                                <p class="logo-upload-msg">Upload Driver App Logo</p>
                                <span class="logo-upload-msg">Please upload a logo with dimensions 200×200 pixels.</span>
                                <span class="driverAppLogoError text-danger"></span>
                            </label>
                            <input type="file" class="d-none" name="driverAppLogo" id="driverAppLogo"
                                accept="image/*">
                        </div>
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Admin Panel Logo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="AdminPanelLogoForm"
                        action="{{ route('admin.setting.logos.adminPanelLogo') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="title" value="adminPanelLogo" id="">
                        <div>
                            <label class="card-upload-box" for="adminPanelLogo">
                                <img class="imagePreview" id="adminPanelLogoImagePreview" />
                                @if (isset($envirements) && count($envirements) > 0)
                                    @foreach ($envirements as $list)
                                        @if (!empty($list->admin_panel_logo_image))
                                            <img class="adminPanelLogoLogoImage LogoImage" id=""
                                                src="{{ $list->admin_panel_logo_image ?? '' }}" />
                                        @endif
                                    @endforeach
                                @endif
                                <p class="logo-upload-msg">Upload Admin Panel Logo</p>
                                <span class="logo-upload-msg">Please upload a logo with dimensions 200×200 pixels.</span>
                                <span class="adminPanelLogoError text-danger"></span>
                            </label>
                            <input type="file" class="d-none" name="adminPanelLogo" id="adminPanelLogo"
                                accept="image/*">
                        </div>
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Invoice Logo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="InvoiceLogoForm" action="{{ route('admin.setting.logos.invoiceLogo') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="title" value="invoiceLogo" id="">
                        <div>
                            <label class="card-upload-box" for="invoiceLogo">
                                <img class="imagePreview" id="invoiceLogoImagePreview" />
                                @if (isset($envirements) && count($envirements) > 0)
                                    @foreach ($envirements as $list)
                                        @if (!empty($list->invoice_logo_image))
                                            <img class="invoiceLogoLogoImage LogoImage" id=""
                                                src="{{ $list->invoice_logo_image ?? '' }}" />
                                        @endif
                                    @endforeach
                                @endif
                                <p class="logo-upload-msg">Upload Invoice Logo</p>
                                <span class="logo-upload-msg">Please upload a logo with dimensions 200×200 pixels.</span>
                                <span class="invoiceLogoError text-danger"></span>
                            </label>
                            <input type="file" class="d-none" name="invoiceLogo" id="invoiceLogo" accept="image/*">
                        </div>
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Mail Logo</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="MailLogoForm" action="{{ route('admin.setting.logos.mailLogo') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="title" value="mailLogo" id="">
                        <div>
                            <label class="card-upload-box" for="mailLogoImage">
                                <img class="imagePreview" id="mailLogoImagePreview" />
                                @if (isset($envirements) && count($envirements) > 0)
                                    @foreach ($envirements as $list)
                                        @if (!empty($list->mail_logo_image))
                                            <img class="mailLogoImageLogoImage LogoImage" id=""
                                                src="{{ $list->mail_logo_image ?? '' }}" />
                                        @endif
                                    @endforeach
                                @endif
                                <p class="logo-upload-msg">Upload Mail Logo</p>
                                <span class="logo-upload-msg">Please upload a logo with dimensions 200×200 pixels.</span>
                                <span class="mailLogoError text-danger"></span>
                            </label>
                            <input type="file" class="d-none mailLogoImage" name="mailLogo" id="mailLogoImage"
                                accept="image/*">
                        </div>
                        <div class="text-end m-t-20">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
        $(function() {
            // preview splashScreenLogo
            $('#splashScreenLogo').on('change', function(event) {
                $('.logo-upload-msg').hide();
                const file = event.target.files[0];
                const imagePreview = $('#imagePreview');
                $('.splashScreenLogoError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.splashScreenLogoError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };
                    $('.splashScreenLogoLogoImage').hide();
                    reader.readAsDataURL(file);
                }
            });
            // preview app logo
            $('#appLogo').on('change', function(event) {
                $('.logo-upload-msg').hide();
                const file = event.target.files[0];
                const imagePreview = $('#appLogoImagePreview');

                $('.appLogoError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.appLogoError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };
                    $('.appLogoLogoImage').hide();
                    reader.readAsDataURL(file);
                }
            });

            // preview driver app logo
            $('#driverAppLogo').on('change', function(event) {
                $('.logo-upload-msg').hide();
                const file = event.target.files[0];
                const imagePreview = $('#driverAppLogoImagePreview');

                $('.driverAppLogoError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.driverAppLogoError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };
                    $('.driverAppLogoLogoImage').hide();
                    reader.readAsDataURL(file);
                }
            });

            // preview driverSplashLogo
            $('#driverSplashLogo').on('change', function(event) {
                $('.logo-upload-msg').hide();
                const file = event.target.files[0];
                const imagePreview = $('#driverSplashLogoImagePreview');

                $('.driverAppError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.driverAppError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };
                    $('.driverSplashLogoLogoImage').hide();
                    reader.readAsDataURL(file);
                }
            });

            // preview driver app logo
            $('#adminPanelLogo').on('change', function(event) {
                $('.logo-upload-msg').hide();
                const file = event.target.files[0];
                const imagePreview = $('#adminPanelLogoImagePreview');

                $('.adminPanelLogoError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.adminPanelLogoError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };
                    $('.adminPanelLogoLogoImage').hide();
                    reader.readAsDataURL(file);
                }
            });

            // preview driver app logo
            $('#invoiceLogo').on('change', function(event) {
                $('.logo-upload-msg').hide();
                const file = event.target.files[0];
                const imagePreview = $('#invoiceLogoImagePreview');

                $('.invoiceLogoError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.invoiceLogoError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };
                    $('.invoiceLogoLogoImage').hide();
                    reader.readAsDataURL(file);
                }
            });

            // // preview driver app logo
            $('#mailLogoImage').on('change', function(event) {
                $('.logo-upload-msg').hide();
                const file = event.target.files[0];
                const imagePreview = $('#mailLogoImagePreview');

                $('.mailLogoError').text('');
                if (file) {
                    if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                        $('.mailLogoError').text(
                            'Invalid file type! Only JPG, PNG, and SVG are allowed.');
                        this.value = '';
                        imagePreview.hide();
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };
                    $('.mailLogoImageLogoImage').hide();
                    reader.readAsDataURL(file);
                }
            });

            // Submit 
            $('.splashScreenLogoForm').validate({
                rules: {
                    splashScreenLogo: {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    splashScreenLogo: {
                        required: "Please upload a splash screen logo.",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed."
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Submit 
            $('.AppLogoForm').validate({
                rules: {
                    appLogo: {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    appLogo: {
                        required: "Please upload a app logo.",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed."
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });


            // Submit 
            $('.DriverSplashLogoForm').validate({
                rules: {
                    driverSplashLogo: {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    driverSplashLogo: {
                        required: "Please upload a driver splash logo.",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed."
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Submit 
            $('.DriverAppLogoForm').validate({
                rules: {
                    driverAppLogo: {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    driverAppLogo: {
                        required: "Please upload a driver app logo.",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed."
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Submit 
            $('.AdminPanelLogoForm').validate({
                rules: {
                    adminPanelLogo: {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    adminPanelLogo: {
                        required: "Please upload a admin panel logo.",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed."
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Submit 
            $('.InvoiceLogoForm').validate({
                rules: {
                    invoiceLogo: {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    invoiceLogo: {
                        required: "Please upload a driver invoice logo.",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed."
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Submit 
            $('.MailLogoForm').validate({
                rules: {
                    mailLogo: {
                        required: true,
                        extension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    mailLogo: {
                        required: "Please upload a mail logo.",
                        extension: "Only image files (jpg, jpeg, png, gif) are allowed."
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
