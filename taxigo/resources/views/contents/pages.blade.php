@extends('layouts.main')
@section('title')
    {{ $details->title ?? '' }}
@endsection


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
                        <li class="breadcrumb-item"><a href="#">Content Management</a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ $details->title ?? '' }}</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">{{ $details->title ?? '' }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>
        @if (isset($slug) && $slug == 'inclusion-exclusions')
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Enter Inclusions</h5>
                    </div>
                    <form class="pagesForm" method="post" action="{{ route('admin.pages.storeUpdate') }}">
                        @csrf
                        <div class="card-body">
                            <input type="hidden" name="type" value="{{ $slug ?? '' }}">
                            <input type="hidden" name="inclusion" value="inclusion" id="">
                            <textarea id="readBeforeBook" name="content" class="tox-target" required
                                data-parsley-required-message="Content is required." data-parsley-errors-container="#contentInc-error-container">{!! $inclusionDetails->content ?? '' !!}</textarea>
                            <div id="contentInc-error-container" class="text-danger"></div>
                        </div>
                        <div class="card-footer">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Enter Exclusions</h5>
                    </div>
                    <form class="pagesForm" method="post" action="{{ route('admin.pages.storeUpdate') }}">
                        @csrf
                        <div class="card-body">
                            <input type="hidden" name="type" value="{{ $slug ?? '' }}">
                            <input type="hidden" name="inclusion" value="exclusions" id="">
                            <textarea id="readBeforeBook" name="content" class="tox-target" required
                                data-parsley-required-message="Content is required." data-parsley-errors-container="#contentEx-error-container">{!! $exclusionsDetails->content ?? '' !!}</textarea>
                            <div id="contentEx-error-container" class="text-danger"></div>
                        </div>
                        <div class="card-footer">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $details->title ?? '' }}</h5>
                    </div>
                    <form class="pagesForm" method="post" action="{{ route('admin.pages.storeUpdate') }}">
                        @csrf
                        <div class="card-body">
                            <input type="hidden" name="type" value="{{ $slug ?? '' }}">
                            <textarea id="aboutUs" name="content" class="tox-target" required data-parsley-required-message="Content is required."
                                data-parsley-errors-container="#content-error-container">{!! $details->content ?? '' !!}</textarea>
                            <div id="content-error-container" class="text-danger"></div>
                        </div>
                        <div class="card-footer">
                            <div class="col-12 text-end">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        // Text Editor
        tinymce.init({
            height: '400',
            selector: '#readBeforeBook',
            content_style: 'body { font-family: "Inter", sans-serif; }',
            menubar: false,
            statusbar: false,
            toolbar: 'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent',
            plugins: 'advlist autolink link image lists charmap print preview code'
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.pagesForm').parsley();
        });
        // Text Editor
        tinymce.init({
            height: '400',
            selector: '#aboutUs',
            content_style: 'body { font-family: "Inter", sans-serif; }',
            menubar: false,
            statusbar: false,
            toolbar: 'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent',
            plugins: 'advlist autolink link image lists charmap print preview code'
        });
    </script>
    <!-- [Page Specific JS] end -->
@endsection
