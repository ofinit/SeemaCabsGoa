@extends('layouts.main')

@section('title', 'Booking & Cancellation')

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
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item" aria-current="page">Booking & Cancellation</li>
                </ul>
            </div>
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Booking & Cancellation</h2>
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
                <h5>Advance Booking Time</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="AdvanceBookingForm" action="{{ route('admin.setting.storeAdvanceBooking') }}">
                    @csrf
                    @php
                    $hourExist = 0;
                    $minuteExist = 0;
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="minTime">Minimum Advance Booking Time</label>
                                <p class="sos-action mb-0">Set the minimum advance booking time before the pickup time.
                                </p>
                                <div class="input-group ">
                                    <input type="hidden" name="title[]" value="bookinghour" id="">
                                    @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                    @if (isset($list->title) && $list->title == 'bookinghour')
                                    @php
                                    $hourExist += 1;
                                    @endphp
                                    <input type="number" name="value[]" id="minTime" value="{{ $list->value ?? '' }}" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="0" data-parsley-max="100" data-parsley-required-message="Minimum advance booking time is required." data-parsley-min-message="Value must be at least 0." data-parsley-max-message="Value cannot exceed 100." data-parsley-errors-container="#bookingHour-error-container">
                                    @endif
                                    @endforeach
                                    @endif
                                    @if (isset($hourExist) && $hourExist == 0)
                                    <input type="number" name="value[]" id="minTime" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="1" data-parsley-max="100" data-parsley-required-message="Minimum advance booking time is required." data-parsley-min-message="Value must be at least 1." data-parsley-max-message="Value cannot exceed 100." data-parsley-errors-container="#bookingHour-error-container">
                                    @endif

                                    <span class="input-group-text" id="basic-addon2">Hours</span>
                                </div>
                                <div id="bookingHour-error-container" class="text-danger"></div>
                                <label id="minTime-error" class="error text-danger" for="minTime"></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="maxTime">Maximum Advance Booking Time</label>
                                <p class="sos-action mb-0">Set the maximum advance booking time before the pickup time.
                                </p>
                                <div class="input-group ">
                                    <input type="hidden" name="title[]" value="bookingmaxhour" id="">
                                    @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                    @if (isset($list->title) && $list->title == 'bookingmaxhour')
                                    @php
                                    $minuteExist += 1;
                                    @endphp
                                    <input type="number" name="value[]" value="{{ $list->value ?? '' }}" id="maxTime" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="1" data-parsley-required-message="Maximum advance booking time is required." data-parsley-min-message="Value must be at least 1." data-parsley-max-message="Value cannot exceed 1000000." data-parsley-errors-container="#MaxBookingHour-error-container">
                                    @endif
                                    @endforeach
                                    @endif
                                    @if (isset($minuteExist) && $minuteExist == 0)
                                    <input type="number" name="value[]" id="maxTime" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="1" data-parsley-required-message="Maximum advance booking time is required." data-parsley-min-message="Value must be at least 1." data-parsley-max-message="Value cannot exceed 1000000." data-parsley-errors-container="#MaxBookingHour-error-container">
                                    @endif

                                    <span class="input-group-text" id="basic-addon2">Hours</span>
                                </div>
                                <div id="MaxBookingHour-error-container" class="text-danger"></div>
                                <label id="maxTime-error" class="error text-danger" for="maxTime"></label>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <div class="my-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Advance Cancellation Time -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Advance Cancellation Time</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="AdvanceBookingCancellationForm" action="{{ route('admin.setting.storeAdvanceBookingCancellationForm') }}">
                    @csrf
                    @php
                    $hourExist = 0;
                    $minuteExist = 0;
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="maxCancelTime">Maximum Advance Cancellation
                                    Time</label>
                                <p class="sos-action mb-0">Set the maximum advance cancellation time before the pickup
                                    time.</p>
                                <div class="input-group ">
                                    <input type="hidden" name="title[]" value="bookingCancellationhour" id="">
                                    @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                    @if (isset($list->title) && $list->title == 'bookingcancellationhour')
                                    @php
                                    $hourExist += 1;
                                    @endphp
                                    <input type="number" name="value[]" id="maxCancelTime" value="{{ $list->value ?? '' }}" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="1" data-parsley-required-message="Maximum advance cancellation time is required." data-parsley-min-message="Value must be at least 1." data-parsley-max-message="Value cannot exceed 100." data-parsley-errors-container="#MaxCancelHour-error-container">
                                    @endif
                                    @endforeach
                                    @endif
                                    @if (isset($hourExist) && $hourExist == 0)
                                    <input type="number" name="value[]" id="maxCancelTime" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="1" data-parsley-required-message="Maximum advance cancellation time is required." data-parsley-min-message="Value must be at least 1." data-parsley-max-message="Value cannot exceed 100." data-parsley-errors-container="#MaxCancelHour-error-container">
                                    @endif

                                    <span class="input-group-text" id="basic-addon2">Hours</span>
                                </div>
                                <div id="MaxCancelHour-error-container" class="text-danger"></div>
                                <label id="maxCancelTime-error" class="error text-danger" for="maxCancelTime"></label>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="minCancelTime">Minimum Advance Cancellation
                                    Time</label>
                                <p class="sos-action mb-0">Set the minimum advance cancellation time before the pickup
                                    time.</p>
                                <div class="input-group ">
                                    <input type="hidden" name="title[]" value="bookingCancellationminhour" id="">
                                    @if (isset($envoirements) && count($envoirements) > 0)
                                    @foreach ($envoirements as $key => $list)
                                    @if (isset($list->title) && $list->title == 'bookingcancellationminhour')
                                    @php
                                    $hourExist += 1;
                                    @endphp
                                    <input type="text" name="value[]" id="minCancelTime" value="{{ $list->value ?? '' }}" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="1" data-parsley-max="100" data-parsley-required-message="Minimum advance cancellation time is required." data-parsley-min-message="Value must be at least 1." data-parsley-max-message="Value cannot exceed 100%." data-parsley-errors-container="#MinCancelHour-error-container">
                        @endif
                        @endforeach
                        @endif
                        @if (isset($hourExist) && $hourExist == 0)
                        <input type="text" name="value[]" id="minCancelTime" class="form-control" placeholder="Enter time" aria-label="Recipient's username" aria-describedby="basic-addon2" required data-parsley-min="1" data-parsley-max="100" data-parsley-required-message="Minimum advance cancellation time is required." data-parsley-min-message="Value must be at least 1." data-parsley-max-message="Value cannot exceed 100%." data-parsley-errors-container="#MinCancelHour-error-container">
                        @endif

                        <span class="input-group-text" id="basic-addon2">Hours</span>
                    </div>
                    <div id="MinCancelHour-error-container" class="text-danger"></div>
                    <label id="minCancelTime-error" class="error text-danger" for="minCancelTime"></label>
            </div>
        </div> --}}
        <div class="col-12 text-end">
            <div class="my-3">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
    </form>
</div>
</div>
</div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.AdvanceBookingForm').parsley();
        $('.AdvanceBookingCancellationForm').parsley();
    });

</script>
@endsection
