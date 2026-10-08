@extends('layouts.main')

@section('title', 'Base Fare')

@section('css')
<style>
    .imagePreview {
        height: 100px;
        width: 100px;
        margin-top: 10px;
        border: none;
    }

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
        object-fit: contain;
        border-radius: 12px;
    }

</style>
<!-- DataTables CSS -->
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
                    <li class="breadcrumb-item" aria-current="page">Base Fare</li>
                </ul>
            </div>
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Base Fare</h2>
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
                <h5>{{ @$priceDetails ? 'Update' : 'Add' }} Base Fare</h5>
            </div>
            <div class="card-body">
                <form method="post" id="baseFareForm" action="{{ route('admin.setting.baseFare.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="countryName">Cab Type</label>
                                <input type="hidden" name="id" value="{{ @$priceDetails->id ?? '' }}">
                                <select class="form-select" name="cab_type" id="countryName" required data-parsley-required-message="Please select cab type.">
                                    <option value="" hidden>Select cab type</option>
                                    @php
                                    $typeList = getCabType();
                                    @endphp
                                    @if (count($typeList) > 0)
                                    @foreach ($typeList as $key => $list)
                                    <option value="{{ $key + 1 }}" {{ @$priceDetails->cab_type == $key + 1 ? 'selected' : '' }}>
                                        {{ $list ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="baseFare">Base Fare</label>
                                <div class="input-group ">
                                    <span class="input-group-text" id="basic-addon2">{{ getCurrencySign() }}</span>
                                    <input type="text" class="form-control" id="driver1Name" name="base_fare" value="{{ @$priceDetails->base_fare ?? old('base_fare') }}" placeholder="Enter base fare" required maxlength="50" data-parsley-required-message="Please enter base fare." data-parsley-maxlength-message="Maximum 50 characters allowed." data-parsley-type="number" data-parsley-type-message="Please enter a valid number." data-parsley-errors-container="#baseFare-error-container">
                                </div>
                                <span id="baseFare-error-container" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="kmNumber">No. of Kms Included</label>
                                <input type="text" class="form-control" id="driver1Name" name="no_of_kms" value="{{ @$priceDetails->no_of_kms ?? old('no_of_kms') }}" placeholder="Enter no. of kms" required maxlength="50" data-parsley-required-message="Please enter no of kms." data-parsley-maxlength-message="Maximum 50 characters allowed." data-parsley-type="number" data-parsley-type-message="Please enter a valid number.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="addKm">Additional KM Charges</label>
                                <input type="text" class="form-control" id="driver1Name" name="additional_km_charges" value="{{ @$priceDetails->additional_km_charges ?? old('additional_km_charges') }}" placeholder="Enter additional km charges" required maxlength="50" data-parsley-required-message="Please enter additional km charges." data-parsley-maxlength-message="Maximum 50 characters allowed." data-parsley-type="number" data-parsley-type-message="Please enter a valid number.">
                                <div id="KmCharges-error-container" class="text-danger"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="waitingCharge">Waiting Charges ( After 30 mins
                                    )</label>
                                <div class="input-group ">
                                    <span class="input-group-text" id="basic-addon2">{{ getCurrencySign() }}</span>
                                    <input type="text" class="form-control" id="driver1Name" name="waiting_charges" value="{{ @$priceDetails->waiting_charges ?? old('waiting_charges') }}" placeholder="Enter waiting charge" required maxlength="50" data-parsley-required-message="Please enter waiting charge." data-parsley-maxlength-message="Maximum 50 characters allowed." data-parsley-type="number" data-parsley-type-message="Please enter a valid number." data-parsley-errors-container="#waitingCharges-error-container">
                                </div>
                                <div id="waitingCharges-error-container" class="text-danger"></div>
                            </div>
                        </div>

                    </div>
                    <div class="col-12 text-end">
                        <div class="my-3">
                            <button type="submit" class="btn btn-primary">{{ @$priceDetails ? 'Update' : 'Add' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h5>View Base Fare</h5>
        </div>
        <div class="card-body">
            <div class="dt-responsive">
                <table id="addAdvertiserTable" class="table table-striped fleeterListTable table-bordered nowrap">
                    <thead>
                        <tr>
                            <th>Cab Type</th>
                            <th>Base Fare</th>
                            <th>Kms</th>
                            <th>Addl. KM</th>
                            <th>Waiting Charges</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>


@endsection

@section('scripts')
<script>
    $(function() {
        $('#baseFareForm').parsley();
        // get data table data
        $('.fleeterListTable').DataTable({
            processing: true
            , serverSide: true
            , ajax: {
                url: "{{ route('admin.setting.baseFare.list') }}"
                , type: "POST"
                , data: function(data) {
                    data.search = $('input[type="search"]').val();
                    data._token = "{{ csrf_token() }}";
                }
            }
            , order: [
                [1, 'DESC']
            ]
            , pageLength: 10
            , searching: true
            , columns: [{
                    data: 'cab_type'
                    , name: 'cab_type'
                    , render: function(data, type, row) {
                        $type = 'Hatchback';
                        if (row.cab_type == 2) {
                            $type = 'Sedan';
                        } else if (row.cab_type == 3) {
                            $type = 'SUV';
                        }
                        return $type;
                    }
                }
                , {
                    data: 'base_fare'
                    , name: 'base_fare'
                    , render: function(data, type, row) {
                        return getCurrencySign() + row.base_fare ? ? '--';
                    }
                }
                , {
                    data: 'no_of_kms'
                    , name: 'no_of_kms'
                }
                , {
                    data: 'additional_km_charges'
                    , name: 'additional_km_charges'
                }
                , {
                    data: 'waiting_charges'
                    , name: 'waiting_charges'
                    , render: function(data, type, row) {
                        return getCurrencySign() + row.waiting_charges ? ? '--';
                    }
                }
                , {
                    data: 'id'
                    , name: 'id'
                    , render: function(data, type, row) {
                        var editUrl = '{{ route('
                        admin.setting.baseFare.edit ', ': id ') }}'
                            .replace(':id', row.id);
                        var deleteUrl =
                            '{{ route('
                        admin.setting.baseFare.delete ', ': id ') }}'
                            .replace(':id', row.id);
                        var html = ` <a href="${editUrl}" class="btn btn-sm btn-light-success me-1"><i
                                        class="feather icon-edit"></i></a>
                                <span  class="btn btn-sm btn-light-danger sa-bs-error-ico deleteBaseFare" data-url="${deleteUrl}"><i
                                        class="feather icon-trash-2"></i></span>`;
                        return html;
                    }
                }
            ]
        });

        // preview front_side_aadhar
        $('#front_side_aadhar').on('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = $('#imagePreviewFrontSide');
            $('#imagePreviewFrontSide').addClass('imagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.attr('src', e.target.result);
                    imagePreview.show();
                };

                reader.readAsDataURL(file);
            }
        });

        // preview back_side_aadhar
        $('#back_side_aadhar').on('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = $('#imagePreviewBackSide');
            $('#imagePreviewBackSide').addClass('imagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.attr('src', e.target.result);
                    imagePreview.show();
                };

                reader.readAsDataURL(file);
            }
        });

        // preview company_license
        $('#company_license').on('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = $('#imagePreviewCompanyLicense');
            $('#imagePreviewCompanyLicense').addClass('imagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.attr('src', e.target.result);
                    imagePreview.show();
                };

                reader.readAsDataURL(file);
            }
        });

        // preview aggrement
        $('#aggrement').on('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = $('#imagePreviewAggrement');
            $('#imagePreviewAggrement').addClass('imagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.attr('src', e.target.result);
                    imagePreview.show();
                };

                reader.readAsDataURL(file);
            }
        });
        // delete 
        $(document).on('click', '.deleteBaseFare', function(e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: "Are you sure?"
                , text: "You won't delete this!"
                , icon: "warning"
                , showCancelButton: true
                , confirmButtonColor: "#d33"
                , cancelButtonColor: "#3085d6"
                , confirmButtonText: "Yes, Proceed!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url
                        , type: "GET"
                        , success: function(response) {
                            Swal.fire("Success!"
                                , "Base fare has been deleted.", "success");
                            location.reload();
                        }
                        , error: function() {
                            Swal.fire("Error!", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });


    });

</script>
<!-- [Page Specific JS] end -->
@endsection
