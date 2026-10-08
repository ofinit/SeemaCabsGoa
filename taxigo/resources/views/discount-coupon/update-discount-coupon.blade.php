@extends('layouts.main')

@section('css')
    <link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">
@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

    <div class="row">
        <h2 class="mb-4">Update Coupon</h2>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Update Coupon</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="codeName">Discount Code</label>
                                <input type="text" class="form-control" id="codeName" placeholder="Enter code">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="discountType">Discount Type</label>
                                <select class="form-select" id="discountType">
                                    <option hidden>Select discount type</option>
                                    <option>Percent</option>
                                    <option>Fixed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="discountNumber">Discount Value</label>
                                <input type="number" class="form-control" id="discountNumber" placeholder="Enter discount">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label" for="expiryDate">Expiry Date</label>
                                    <div class="form-check form-switch custom-switch-v1 mb-2">
                                        <input type="checkbox" class="form-check-input input-primary" id="switchExpiry">
                                        <label class="form-label mb-0" for="switchExpiry">No Expiry</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" id="expiryDate" placeholder="Select expiry date">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label" for="codeDescription">Description</label>
                                <input type="text" class="form-control" id="codeDescription" placeholder="Add Description">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="discountCode" value=""
                                        id="enableDiscountCode">
                                    <label class="form-check-label" for="enableDiscountCode"> Enable </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="discountCode" value=""
                                        id="disableDiscountCode">
                                    <label class="form-check-label" for="disableDiscountCode"> Disable </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary updated-success">Update Coupon</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- [Page Specific JS] start -->

    <!-- Date Picker -->
    <script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>

    <!-- SweetAlert JS -->
    <script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script>
        // Update Button Sweet Alert
        document.querySelectorAll('.updated-success').forEach(function (element) {
            element.addEventListener('click', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Coupon Updated Successfully',
                    showCancelButton: false,
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#3085d6',
                })
            });
        });

        (function () {
            const d_week = new Datepicker(document.querySelector('#expiryDate'), {
                buttonClass: 'btn'
            });
        })();
    </script>
    <!-- [Page Specific JS] end -->
@endsection