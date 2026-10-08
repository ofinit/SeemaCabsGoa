@extends('layouts.main')

@section('title', 'Taxes')

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
                        <li class="breadcrumb-item" aria-current="page">Taxes</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Taxes</h2>
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
                    <h5>Add Taxes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form method="POST" class="PlateFormTaxForm"
                            action="{{ route('admin.setting.storePlateFormTax') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        @php
                                            $gstExist = 0;
                                        @endphp
                                        <label class="form-label" for="inlineFormInputName">Enter GST % For Customer
                                            Invoice</label>
                                        <div class="input-group ">
                                            <input type="hidden" name="title[]" value="gstTitle" id="">
                                            @if (isset($envoirements) && count($envoirements) > 0)
                                                @foreach ($envoirements as $key => $list)
                                                    @if (isset($list->title) && $list->title == 'gsttitle')
                                                        @php
                                                            $gstExist += 1;
                                                        @endphp
                                                        <input type="text" name="value[]"
                                                            value="{{ $list->value ?? '' }}" class="form-control"
                                                            id="inlineFormInputName"
                                                            placeholder="Enter GST % for customer invoice"
                                                            data-parsley-min="0" data-parsley-max="100"
                                                            data-parsley-required-message="GST is required."
                                                            data-parsley-min-message="Value must be at least 1%."
                                                            data-parsley-max-message="Value cannot exceed 100%."
                                                            data-parsley-errors-container="#gst-error-container"
                                                            data-parsley-type="number"
                                                            data-parsley-type-message="Please enter a valid number.">
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if (isset($gstExist) && $gstExist == 0)
                                                <input type="text" name="value[]" value="" class="form-control"
                                                    id="inlineFormInputName" placeholder="Enter GST % for customer invoice"
                                                    data-parsley-min="0" data-parsley-max="100"
                                                    data-parsley-required-message="GST is required."
                                                    data-parsley-min-message="Value must be at least 1%."
                                                    data-parsley-max-message="Value cannot exceed 100%."
                                                    data-parsley-errors-container="#gst-error-container"
                                                    data-parsley-type="number"
                                                    data-parsley-type-message="Please enter a valid number.">
                                            @endif
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                        <div id="gst-error-container" class="text-danger"></div>
                                        <label id="inlineFormInputName-error" class="error text-danger"
                                            for="inlineFormInputName"></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        @php
                                            $tdsExist = 0;
                                        @endphp
                                        <label class="form-label" for="inlineFormInputName">Enter TDS % For Fleet Operator
                                            Settlement</label>
                                        <div class="input-group mb-3">
                                            <input type="hidden" name="title[]" value="tdsTitle" id="">
                                            @if (isset($envoirements) && count($envoirements) > 0)
                                                @foreach ($envoirements as $key => $list)
                                                    @if (isset($list->title) && $list->title == 'tdstitle')
                                                        @php
                                                            $tdsExist += 1;
                                                        @endphp
                                                        <input type="number" name="value[]"
                                                            value="{{ $list->value ?? '' }}" class="form-control"
                                                            id="tdsTaxes"
                                                            placeholder="Enter TDS % for fleet operator settlement"
                                                            data-parsley-min="0" data-parsley-max="100"
                                                            data-parsley-required-message="TDS is required."
                                                            data-parsley-min-message="Value must be at least 1%."
                                                            data-parsley-max-message="Value cannot exceed 100%."
                                                            data-parsley-errors-container="#TDS-error-container"
                                                            data-parsley-type="number"
                                                            data-parsley-type-message="Please enter a valid number.">
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if (isset($tdsExist) && $tdsExist == 0)
                                                <input type="number" name="value[]" class="form-control" id="tdsTaxes"
                                                    placeholder="Enter TDS % for fleet operator settlement"
                                                    data-parsley-min="0" data-parsley-max="100"
                                                    data-parsley-required-message="TDS is required."
                                                    data-parsley-min-message="Value must be at least 1%."
                                                    data-parsley-max-message="Value cannot exceed 100%."
                                                    data-parsley-errors-container="#TDS-error-container"
                                                    data-parsley-type="number"
                                                    data-parsley-type-message="Please enter a valid number.">
                                            @endif
                                            <span class="input-group-text" id="basic-addon2">%</span>
                                        </div>
                                        <div id="TDS-error-container" class="text-danger"></div>
                                        <label id="tdsTaxes-error" class="error text-danger" for="tdsTaxes"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <div class="my-3">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.PlateFormTaxForm').parsley();
        });
    </script>
@endsection
