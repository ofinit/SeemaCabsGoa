@extends('layouts.main')

@section('title', 'App Update')
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
                        <li class="breadcrumb-item" aria-current="page">App Update</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">App Update</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form class="appUpdateForm" method="post" action="{{ route('admin.setting.storeAppUpdate') }}">
        @csrf
        @php
            $androidExist = 0;
            $androidUrlExist = 0;
            $iosExist = 0;
            $appNewVerion = 0;
        @endphp
        <div class="row">
            <div class="col-12">
                @include('layouts.message')
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center gap-2">
                            <h5>Enable App Update</h5>
                            <div class="form-check form-switch custom-switch-v1">
                                <input type="hidden" name="title[]" value="appupdate" id="">
                                @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                        @if (isset($list->title) && $list->title == 'appupdate')
                                            @php
                                                $androidExist += 1;
                                            @endphp
                                            <input type="hidden" name="value[]" value="{{ $list->value ?? '' }}"
                                                class="HiddenUpdate" id="">
                                            <input type="checkbox" value="0" {{ $list->value == 1 ? 'checked' : '' }}
                                                class="form-check-input input-success enableUpdate" id="surgePricingSwitch">
                                        @endif
                                    @endforeach
                                @endif
                                @if (isset($androidExist) && $androidExist == 0)
                                    <input type="hidden" name="value[]" value="0" class="HiddenUpdate" id="">
                                    <input type="checkbox" value="0"
                                        class="form-check-input input-success enableUpdate" id="surgePricingSwitch">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label" for="appNewVersion">App New Version</label>
                                <input type="hidden" name="title[]" value="appnewversion" id="">
                                @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                        @if (isset($list->title) && $list->title == 'appnewversion')
                                            @php
                                                $appNewVerion += 1;
                                            @endphp
                                            <input type="text" name="value[]" value="{{ $list->value ?? '' }}"
                                                id="appNewVersion" class="form-control" placeholder="Enter app new version"
                                                required data-parsley-required-message="App new version required."
                                                data-parsley-pattern="^\d+(\.\d+){1,2}$"
                                                data-parsley-pattern-message="Please enter a valid version (e.g., 1.0.2)">
                                        @endif
                                    @endforeach
                                @endif
                                @if (isset($appNewVerion) && $appNewVerion == 0)
                                    <input type="text" name="value[]" id="appNewVersion" class="form-control"
                                        placeholder="Enter app new version" required
                                        data-parsley-required-message="App new version required."
                                        data-parsley-pattern="^\d+(\.\d+){1,2}$"
                                        data-parsley-pattern-message="Please enter a valid version (e.g., 1.0.2)">
                                @endif
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label" for="androidUrl">Android App URL</label>
                                <input type="hidden" name="title[]" value="androidappurl" id="">
                                @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                        @if (isset($list->title) && $list->title == 'androidappurl')
                                            @php
                                                $androidUrlExist += 1;
                                            @endphp
                                            <input type="url" name="value[]" value="{{ $list->value ?? '' }}" required
                                                data-parsley-required-message="Android app url required."
                                                data-parsley-errors-container="#android-error-container" id="androidUrl"
                                                class="form-control" placeholder="Enter android app url">
                                        @endif
                                    @endforeach
                                @endif
                                @if (isset($androidUrlExist) && $androidUrlExist == 0)
                                    <input type="url" name="value[]" required
                                        data-parsley-required-message="Android app url required."
                                        data-parsley-errors-container="#android-error-container" id="androidUrl"
                                        class="form-control" placeholder="Enter android app url">
                                @endif

                                <div id="android-error-container" class="text-danger"></div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label" for="iosUrl">IOS App URL</label>
                                <input type="hidden" name="title[]" value="iosappurl" id="">
                                @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                        @if (isset($list->title) && $list->title == 'iosappurl')
                                            @php
                                                $iosExist += 1;
                                            @endphp
                                            <input type="url" name="value[]" value="{{ $list->value ?? '' }}"
                                                required data-parsley-required-message="Ios app url required."
                                                data-parsley-errors-container="#ios-error-container" id="iosUrl"
                                                class="form-control" placeholder="Enter ios app url">
                                        @endif
                                    @endforeach
                                @endif
                                @if (isset($iosExist) && $iosExist == 0)
                                    <input type="url" name="value[]" required
                                        data-parsley-required-message="Ios app url required."
                                        data-parsley-errors-container="#ios-error-container" id="iosUrl"
                                        class="form-control" placeholder="Enter ios app url">
                                @endif
                                <div id="ios-error-container" class="text-danger"></div>
                            </div>
                            <div class="col-12 text-end">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.appUpdateForm').parsley();

            $('.enableUpdate').on('change', function(e) {
                e.preventDefault();
                if ($(this).prop('checked')) {
                    $('.HiddenUpdate').val(1);
                } else {
                    $('.HiddenUpdate').val(0);
                }

            });
        });

        document.getElementById("appNewVersion").addEventListener("input", function(e) {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
    </script>
@endsection
