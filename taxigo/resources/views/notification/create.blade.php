@extends('layouts.main')

@section('title', 'Create Notification')

@section('css')
<link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" />
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

    #bannerImagePreviewContainer {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }
</style>
@endsection
@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notifications.index') }}"></a>Notification</li>
                        <li class="breadcrumb-item">Create Notification</li>
                    </ul>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Create Notification</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form id="custom-notifications" action="{{ route('admin.notifications.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required data-parsley-required-message="Please enter title.">
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">Description</label>
                            <textarea class="form-control" rows="5" cols="10" name="text" placeholder="Enter description" required data-parsley-required-message="Please enter description."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label mb-2">Image</label>
                                    <label class="card-upload-box" for="mailLogoImage">
                                        <div id="bannerImagePreviewContainer" class="d-flex flex-wrap gap-2"></div>
                                        <p class="logo-upload-msg">
                                            <svg width="42" height="42" viewBox="0 0 42 42" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.75 18.8125C13.09 18.8125 10.9375 16.66 10.9375 14C10.9375 11.34 13.09 9.1875 15.75 9.1875C18.41 9.1875 20.5625 11.34 20.5625 14C20.5625 16.66 18.41 18.8125 15.75 18.8125ZM15.75 11.8125C15.1698 11.8125 14.6134 12.043 14.2032 12.4532C13.793 12.8634 13.5625 13.4198 13.5625 14C13.5625 14.5802 13.793 15.1366 14.2032 15.5468C14.6134 15.957 15.1698 16.1875 15.75 16.1875C16.3302 16.1875 16.8866 15.957 17.2968 15.5468C17.707 15.1366 17.9375 14.5802 17.9375 14C17.9375 13.4198 17.707 12.8634 17.2968 12.4532C16.8866 12.043 16.3302 11.8125 15.75 11.8125Z"
                                                    fill="#666666" />
                                                <path
                                                    d="M26.25 39.8125H15.75C6.2475 39.8125 2.1875 35.7525 2.1875 26.25V15.75C2.1875 6.2475 6.2475 2.1875 15.75 2.1875H22.75C23.4675 2.1875 24.0625 2.7825 24.0625 3.5C24.0625 4.2175 23.4675 4.8125 22.75 4.8125H15.75C7.6825 4.8125 4.8125 7.6825 4.8125 15.75V26.25C4.8125 34.3175 7.6825 37.1875 15.75 37.1875H26.25C34.3175 37.1875 37.1875 34.3175 37.1875 26.25V17.5C37.1875 16.7825 37.7825 16.1875 38.5 16.1875C39.2175 16.1875 39.8125 16.7825 39.8125 17.5V26.25C39.8125 35.7525 35.7525 39.8125 26.25 39.8125Z"
                                                    fill="#666666" />
                                                <path
                                                    d="M31.5 15.3119C30.7825 15.3119 30.1875 14.7169 30.1875 13.9994V3.49941C30.1875 2.97441 30.5025 2.48441 30.9925 2.29191C31.4825 2.09941 32.0425 2.20441 32.4275 2.57191L35.9275 6.07191C36.435 6.57941 36.435 7.41941 35.9275 7.92691C35.42 8.43441 34.58 8.43441 34.0725 7.92691L32.8125 6.66691V13.9994C32.8125 14.7169 32.2175 15.3119 31.5 15.3119Z"
                                                    fill="#666666" />
                                                <path
                                                    d="M28 8.31203C27.6675 8.31203 27.335 8.18953 27.0725 7.92703C26.8284 7.68004 26.6916 7.34678 26.6916 6.99953C26.6916 6.65228 26.8284 6.31902 27.0725 6.07203L30.5725 2.57203C31.08 2.06453 31.92 2.06453 32.4275 2.57203C32.935 3.07953 32.935 3.91953 32.4275 4.42703L28.9275 7.92703C28.665 8.18953 28.3325 8.31203 28 8.31203ZM4.67254 34.4745C4.39282 34.4726 4.12101 34.3814 3.89667 34.2144C3.67232 34.0473 3.50714 33.813 3.42515 33.5455C3.34316 33.2781 3.34864 32.9915 3.4408 32.7273C3.53295 32.4632 3.70697 32.2354 3.93754 32.077L12.565 26.2845C14.455 25.0245 17.0625 25.1645 18.7775 26.617L19.355 27.1245C20.23 27.877 21.7175 27.877 22.575 27.1245L29.855 20.877C31.71 19.2845 34.6325 19.2845 36.505 20.877L39.3575 23.327C39.9 23.7995 39.97 24.622 39.4975 25.182C39.025 25.7245 38.185 25.7945 37.6425 25.322L34.79 22.872C33.915 22.1195 32.445 22.1195 31.57 22.872L24.29 29.1195C22.435 30.712 19.5125 30.712 17.64 29.1195L17.0625 28.612C16.2575 27.9295 14.9275 27.8595 14.035 28.472L5.42504 34.2645C5.18004 34.4045 4.91754 34.4745 4.67254 34.4745Z"
                                                    fill="#666666" />
                                            </svg>
                                        </p>
                                        <span class="logo-upload-msg">Drag & Drop</span>
                                        <span class="bannerImageError text-danger"></span>
                                    </label>
                                    <input type="file" class="d-none mailLogoImage bannerImage" name="image"
                                        id="mailLogoImage" accept="image/*"
                                        data-parsley-required-message="Please choose banner image."
                                        data-parsley-errors-container="#bannerImage-error-container">
                                    <span id="bannerImage-error-container" class="text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="link" class="form-label">Link</label>
                            <input
                                type="url"
                                name="link"
                                id="link"
                                class="form-control"
                                placeholder="https://example.com"
                                value="{{ old('link') }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="show_in_app"
                                    value="1"
                                >
                                <span class="form-check-label fw-semibold">
                                    Show in App
                                </span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<!-- Toastr JS -->
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#custom-notifications').parsley();
        imagePreview();
    });
    function imagePreview() {
        // Image preview
        $('#mailLogoImage').on('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = $('#bannerImagePreviewContainer');
            const errorMsg = $('.bannerImageError');
            const uploadMsg = $('.logo-upload-msg');
            // Clear previous error & preview
            errorMsg.text('');
            previewContainer.empty();

            if (file) {
                uploadMsg.hide();
                // ✅ Validate file type
                if (!['image/jpeg', 'image/png', 'image/svg+xml'].includes(file.type)) {
                    errorMsg.text('Invalid file type! Only JPG, PNG, and SVG are allowed.');
                    $(this).val(''); // Reset file input
                    return;
                }

                // ✅ File preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = $('<img>', {
                        src: e.target.result,
                        class: 'img-thumbnail',
                        css: {
                            width: '120px',
                            height: '120px',
                            objectFit: 'cover',
                            borderRadius: '8px',
                        },
                    });
                    previewContainer.append(img);
                };
                reader.readAsDataURL(file);
            }
            else
            {
                uploadMsg.show();
            }
        });
    }
</script>
@endsection
