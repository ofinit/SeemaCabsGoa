@extends('layouts.main')

@section('title', 'Aggregator Commission')

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
                        <li class="breadcrumb-item" aria-current="page">Aggregator Commission</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Aggregator Commission</h2>
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
                    <h5>Commissions &amp; Fees</h5>
                </div>
                <div class="card-body">
                    <form class="aggregatorCommissionForm" method="post"
                        action="{{ route('admin.setting.storePlateFormFee') }}">
                        @csrf
                        @php
                            $aggretorExist = 0;
                            $operatorExist = 0;
                            $totalCommisonExist = 0;
                            $packageaggretorExist = 0;
                            $packageoperatorExist = 0;
                        @endphp
                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="agreegator">OfinIT Platform Fee — rides (%)</label>
                                    <div class="input-group ">
                                        <input type="hidden" name="title[]" value="aggregatorcommission" id="">
                                        @if (isset($envoirements) && count($envoirements) > 0)
                                            @foreach ($envoirements as $key => $list)
                                                @if (isset($list->title) && $list->title == 'aggregatorcommission')
                                                    @php
                                                        $aggretorExist += 1;
                                                    @endphp
                                                    <input type="text" name="value[]" value="{{ $list->value ?? '' }}"
                                                        class="form-control agreegatorCom" id="agreegator"
                                                        onchange="increaseAggreValue(this)"
                                                        placeholder="Enter aggregator commission" required
                                                        data-parsley-min="1" data-parsley-max="100"
                                                        data-parsley-required-message="Aggregator commission is required."
                                                        data-parsley-min-message="Value must be at least 1%."
                                                        data-parsley-max-message="Value cannot exceed 100%."
                                                        data-parsley-errors-container="#agreegator-error-container">
                                                @endif
                                            @endforeach
                                        @endif
                                        @if (isset($aggretorExist) && $aggretorExist == 0)
                                            <input type="text" name="value[]" class="form-control agreegatorCom"
                                                id="agreegator" onchange="increaseAggreValue(this)"
                                                placeholder="Enter aggregator commission" required
                                                data-parsley-type="number" data-parsley-min="1" data-parsley-max="100"
                                                data-parsley-required-message="Aggregator commission is required."
                                                data-parsley-min-message="Value must be at least 1%."
                                                data-parsley-max-message="Value cannot exceed 100%."
                                                data-parsley-errors-container="#agreegator-error-container">
                                        @endif
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                    <span class="text-danger totalCommisonError"></span>
                                    <div id="agreegator-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="fleetOperator">Fleet Operator Commission</label>
                                    <div class="input-group ">
                                        <input type="hidden" name="title[]" value="operatorcommission" id="">
                                        @if (isset($envoirements) && count($envoirements) > 0)
                                            @foreach ($envoirements as $key => $list)
                                                @if (isset($list->title) && $list->title == 'operatorcommission')
                                                    @php
                                                        $operatorExist += 1;
                                                    @endphp
                                                    <input type="text" name="value[]" value="{{ $list->value ?? '' }}"
                                                        class="form-control fleetOperatorCom" id="fleetOperator"
                                                        onchange="increaseFleetValue(this)"
                                                        placeholder="Enter fleet operator commission"required
                                                        data-parsley-type="number" data-parsley-min="1"
                                                        data-parsley-max="100"
                                                        data-parsley-required-message="Fleet Operator commission is required."
                                                        data-parsley-min-message="Value must be at least 1%."
                                                        data-parsley-max-message="Value cannot exceed 100%."
                                                        data-parsley-errors-container="#fleetOperator-error-container">
                                                @endif
                                            @endforeach
                                        @endif
                                        @if (isset($operatorExist) && $operatorExist == 0)
                                            <input type="text" name="value[]" class="form-control fleetOperatorCom"
                                                id="fleetOperator" onchange="increaseFleetValue(this)"
                                                placeholder="Enter fleet operator commission"required
                                                data-parsley-type="number" data-parsley-min="1" data-parsley-max="100"
                                                data-parsley-required-message="Fleet Operator commission is required."
                                                data-parsley-min-message="Value must be at least 1%."
                                                data-parsley-max-message="Value cannot exceed 100%."
                                                data-parsley-errors-container="#fleetOperator-error-container">
                                        @endif

                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                    <span class="text-danger totalCommisonError"></span>
                                    <div id="fleetOperator-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="totalCommission">Advance Paid Online — rides (% of total)</label>
                                    <div class="input-group ">
                                        <input type="hidden" name="title[]" value="totalCommission" id="">
                                        @if (isset($envoirements) && count($envoirements) > 0)
                                            @foreach ($envoirements as $key => $list)
                                                @if (isset($list->title) && $list->title == 'totalcommission')
                                                    @php
                                                        $totalCommisonExist += 1;
                                                    @endphp
                                                    <input type="text" name="value[]" value="{{ $list->value ?? '' }}"
                                                        class="form-control totalCommission" id="totalCommission"
                                                        placeholder="Total Commission" readonly required
                                                        data-parsley-errors-message="Total commission is required."
                                                        data-parsley-errors-container="#totalCo-error-container">
                                                @endif
                                            @endforeach
                                        @endif
                                        @if (isset($totalCommisonExist) && $totalCommisonExist == 0)
                                            <input type="text" name="value[]" class="form-control totalCommission"
                                                id="totalCommission" placeholder="Total Commission" readonly required
                                                data-parsley-errors-container="#totalCo-error-container">
                                        @endif

                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                    <div id="totalCo-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="packageAgreegator">OfinIT Platform Fee — packages (%)</label>
                                    <div class="input-group ">
                                        <input type="hidden" name="title[]" value="packageaggregatorcommission" id="">
                                        @if (isset($envoirements) && count($envoirements) > 0)
                                            @foreach ($envoirements as $key => $list)
                                                @if (isset($list->title) && $list->title == 'packageaggregatorcommission')
                                                    @php
                                                        $packageaggretorExist += 1;
                                                    @endphp
                                                    <input type="text" name="value[]" value="{{ $list->value ?? '' }}"
                                                        class="form-control packageAgreegatorCom" id="packageaggregatorcommission"
                                                        placeholder="Enter package aggregator commission" required
                                                        data-parsley-min="1" data-parsley-max="100"
                                                        data-parsley-required-message="Packages Aggregator commission is required."
                                                        data-parsley-min-message="Value must be at least 1%."
                                                        data-parsley-max-message="Value cannot exceed 100%."
                                                        data-parsley-errors-container="#package-agreegator-error-container">
                                                @endif
                                            @endforeach
                                        @endif
                                        @if (isset($packageaggretorExist) && $packageaggretorExist == 0)
                                            <input type="text" name="value[]" class="form-control packageAgreegatorCom"
                                                id="packageaggregatorcommission" onchange="increaseAggreValue(this)"
                                                placeholder="Enter package aggregator commission" required
                                                data-parsley-type="number" data-parsley-min="1" data-parsley-max="100"
                                                data-parsley-required-message="Packages Aggregator commission is required."
                                                data-parsley-min-message="Value must be at least 1%."
                                                data-parsley-max-message="Value cannot exceed 100%."
                                                data-parsley-errors-container="#package-agreegator-error-container">
                                        @endif
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                    {{-- <span class="text-danger totalCommisonError"></span> --}}
                                    <div id="package-agreegator-error-container" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="agreegator">Packages Fleet Operator Commission</label>
                                    <div class="input-group ">
                                        <input type="hidden" name="title[]" value="packageoperatorcommission" id="">
                                        @if (isset($envoirements) && count($envoirements) > 0)
                                            @foreach ($envoirements as $key => $list)
                                                @if (isset($list->title) && $list->title == 'packageoperatorcommission')
                                                    @php
                                                        $packageoperatorExist += 1;
                                                    @endphp
                                                    <input type="text" name="value[]" value="{{ $list->value ?? '' }}"
                                                        class="form-control packageoperatorCom" id="packageoperatorcommission"
                                                        placeholder="Enter package fleet operator commission" required
                                                        data-parsley-min="1" data-parsley-max="100"
                                                        data-parsley-required-message="Packages operator commission is required."
                                                        data-parsley-min-message="Value must be at least 1%."
                                                        data-parsley-max-message="Value cannot exceed 100%."
                                                        data-parsley-errors-container="#fleet-operator-error-container">
                                                @endif
                                            @endforeach
                                        @endif
                                        @if (isset($packageoperatorExist) && $packageoperatorExist == 0)
                                            <input type="text" name="value[]" class="form-control packageoperatorCom"
                                                id="packageoperatorcommission"
                                                placeholder="Enter package fleet operator commission" required
                                                data-parsley-type="number" data-parsley-min="1" data-parsley-max="100"
                                                data-parsley-required-message="Packages fleet operator commission is required."
                                                data-parsley-min-message="Value must be at least 1%."
                                                data-parsley-max-message="Value cannot exceed 100%."
                                                data-parsley-errors-container="#fleet-operator-error-container">
                                        @endif
                                        <span class="input-group-text" id="basic-addon2">%</span>
                                    </div>
                                    {{-- <span class="text-danger totalCommisonError"></span> --}}
                                    <div id="fleet-operator-error-container" class="text-danger"></div>
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

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.aggregatorCommissionForm').parsley();
        });

        function increaseAggreValue(e) {
            $('.totalCommisonError').empty();
            var val = parseFloat(e.value) || 0;
            var totalC = parseFloat($('.fleetOperatorCom').val()) || 0;
            totalC = totalC + val;
            if (totalC > 100) {
                $('.totalCommisonError').html('Total value cannot be greater than 100.');
                return;
            }
            $('.totalCommission').val(totalC);
        }

        function increaseFleetValue(e) {
            $('.totalCommisonError').empty();
            var val = parseFloat(e.value) || 0;
            var totalC = parseFloat($('.agreegatorCom').val()) || 0;
            totalC = totalC + val;
            if (totalC > 100) {
                $('.totalCommisonError').html('Total value cannot be greater than 100.');
                return;
            }
            $('.totalCommission').val(totalC);
        }
    </script>
@endsection
