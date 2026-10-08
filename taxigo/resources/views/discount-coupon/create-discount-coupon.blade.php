@extends('layouts.main')
@section('title')
{{@$coupon?'Update Coupon':'Create Coupon'}}
@endsection
@section('breadcrumb-item')
{{@$coupon?'Update Coupon':'Create Coupon'}}
@endsection

@section('css')
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

<!-- Date Range CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">
<!-- Parsley CSS (optional for better UI) -->
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
</style>
@endsection

@section('content')

    <div class="row">
        <h2 class="mb-4">{{@$coupon?'Update Coupon':'Create Coupon'}}</h2>
        <div class="col-12">
            @include('layouts.message')
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{@$coupon?'Update Coupon':'Create Coupon'}}</h5>
                </div>
                <div class="card-body">
                    <form class="couponForm" method="post" action="{{route('admin.coupons.storeUpdate')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{@$coupon->id??''}}">
                                    <label class="form-label" for="codeName">Discount Code</label>
                                    <input type="text" name="code" class="form-control" value="{{@$coupon->code??old('code')}}" id="codeName" placeholder="Enter code"
                                    required data-parsley-required-message="Discount code is required."
                                    data-parsley-maxlength="100" 
                                    data-parsley-maxlength-message="Discount code cannot exceed 100 characters.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="discountType">Discount Type</label>
                                    <select name="type" class="form-select" id="discountType" required data-parsley-required-message="Discount type is required.">
                                        <option value="" hidden>Select discount type</option>
                                        <option value="1" {{(@$coupon->type==1)?'selected':''}}>Percent</option>
                                        <option value="2" {{(@$coupon->type==2)?'selected':''}}>Fixed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="discountNumber">Discount Value</label>
                                    <input type="number" name="value" value="{{@$coupon->value??old('value')}}" class="form-control" id="discountNumber" placeholder="Enter discount"
                                    required data-parsley-required-message="Discount value is required.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="form-label" for="expiryDate">Expiry Date</label>
                                        <div class="form-check form-switch custom-switch-v1 mb-2">
                                            <input type="checkbox" name="no_expiry" {{(@$coupon->no_expiry)?'checked':''}} class="form-check-input input-primary" id="switchExpiry"
                                            >
                                            <label class="form-label mb-0" for="switchExpiry">No Expiry</label>
                                        </div>
                                    </div>
                                    <input type="text" name="expiry_date" 
                                    value="{{ isset($coupon) ? \Carbon\Carbon::parse($coupon->expiry_date)->format('Y-m-d') : old('expiry_date') }}"  
                                    class="form-control" id="expiryDate" placeholder="Select expiry date"
                                    required data-parsley-required-message="Expiry date is required.">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="codeDescription">Description</label>
                                    <input type="text" name="description" value="{{@$coupon->description??old('description')}}" class="form-control" id="codeDescription" placeholder="Add Description"
                                    required data-parsley-required-message="Description is required." 
                                    data-parsley-maxlength="100" 
                                    data-parsley-maxlength-message="Description cannot exceed 100 characters." >
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" name="status" {{(@$coupon->status)?'checked':''}} type="radio" name="discountCode" value="1"
                                            id="enableDiscountCode">
                                        <label class="form-check-label" for="enableDiscountCode"> Enable </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" name="status" {{(@$coupon->status==0)?'checked':''}} type="radio" name="discountCode" value="0"
                                            id="disableDiscountCode">
                                        <label class="form-check-label" for="disableDiscountCode"> Disable </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary ">{{@$coupon?'Update Coupon':'Create Coupon'}} </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary create-coupon">Create Coupon</button>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.couponForm').parsley();        
        });
        // Create Button Sweet Alert
        document.querySelectorAll('.create-coupon').forEach(function (element) {
            element.addEventListener('click', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'New Coupon Created Successfully',
                    showCancelButton: false,
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#3085d6',
                })
            });
        });
    </script>
    <script>
        (function () {
            const expiryDatePicker = new Datepicker(document.querySelector('#expiryDate'), {
                buttonClass: 'btn',
                format: 'yyyy-mm-dd', // Ensures correct format
                autohide: true
            });
        })();
    </script>
    
    <!-- [Page Specific JS] end -->
@endsection