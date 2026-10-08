@extends('layouts.main')

@section('title', 'Customer Pricing')
@section('breadcrumb-item', 'Cab Management')

@section('breadcrumb-item-active', 'Customer Pricing')

@section('css')

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Add Customer Pricing</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabType">Cab Type</label>
                            <select class="form-select" id="cabType">
                                <option hidden>Select cab type</option>
                                <option>Hatchback</option>
                                <option>Sedan</option>
                                <option>SUV</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabModel">Cab Model</label>
                            <select class="form-select" id="cabModel">
                                <option hidden>Select cab model</option>
                                <option>Baleno, Swift or similar</option>
                                <option>Dzire, Etios or similar</option>
                                <option>Xylo, Ertiga or similar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabModel">Model Name</label>
                            <select class="form-select" id="cabModel">
                                <option hidden>Select cab model name</option>
                                <option>Baleno</option>
                                <option>Swift</option>
                                <option>Dzire</option>
                                <option>Etios</option>
                                <option>Xylo</option>
                                <option>Ertiga</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabModel">Color</label>
                            <select class="form-select" id="cabModel">
                                <option hidden>Select cab color</option>
                                <option>White</option>
                                <option>Black</option>
                                <option>Gray</option>
                                <option>Navy Blue</option>
                                <option>Blue</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="baseFare">Base Fare</label>
                            <input type="text" class="form-control" id="baseFare" placeholder="Enter base fare">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="kmNumber">No. of Kms Included</label>
                            <select class="form-select" id="kmNumber">
                                <option>30 kms</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="kmCharge">Additional KM Charges</label>
                            <input type="text" class="form-control" id="kmCharge" placeholder="Enter additional kms">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="waitingCharge">Waiting Charges ( After 30 mins )</label>
                            <input type="text" class="form-control" id="waitingCharge"
                                placeholder="Enter waiting charges">
                        </div>
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

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>View Customer Pricing</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="customerPricing" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Cab Type</th>
                                <th>Cab Model</th>
                                <th>Model Name</th>
                                <th>Color</th>
                                <th>Base Fare</th>
                                <th>No. of Kms Inc.</th>
                                <th>Additional KM Charges</th>
                                <th>Waiting Charges ( After 30 mins )</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Hatchback</td>
                                <td>Baleno, Swift or similar</td>
                                <td>Maruti Swift</td>
                                <td>White</td>
                                <td>150</td>
                                <td>40</td>
                                <td>12</td>
                                <td>30</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Sedan</td>
                                <td>Baleno, Swift or similar</td>
                                <td>Honda City</td>
                                <td>Black</td>
                                <td>200</td>
                                <td>40</td>
                                <td>15</td>
                                <td>60</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>SUV</td>
                                <td>Baleno, Swift or similar</td>
                                <td>Toyota Fortuner</td>
                                <td>Gray</td>
                                <td>300</td>
                                <td>40</td>
                                <td>18</td>
                                <td>30</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


@endsection

@section('scripts')
<!-- [Page Specific JS] start -->

<!-- SweetAlert JS -->
<script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

<!-- Data Table JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

<script>
    // Data Table
    var table = $('#customerPricing').DataTable();

    // Delete Button Sweet Alert
    document.querySelectorAll('.sa-bs-error-ico').forEach(function (element) {
        element.addEventListener('click', function () {
            Swal.fire({
                icon: 'error',
                title: 'Are You Sure!',
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            })
        });
    });
</script>
<!-- [Page Specific JS] end -->
@endsection
