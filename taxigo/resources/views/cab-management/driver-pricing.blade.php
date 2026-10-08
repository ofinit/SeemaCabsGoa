@extends('layouts.main')

@section('title', 'Driver Pricing')
@section('breadcrumb-item', 'Cab Management')

@section('breadcrumb-item-active', 'Driver Pricing')

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
                <h5>Add Driver Pricing</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabType">Cab Type</label>
                            <select class="form-select" id="cabType">
                                <option hidden>Select cab type</option>
                                <option value="Hatchback">Hatchback</option>
                                <option value="Sedan">Sedan</option>
                                <option value="SUV">SUV</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabModel">Cab Model</label>
                            <select class="form-select" id="cabModel">
                                <option hidden>Select cab model</option>
                                <option value="1">Baleno, Swift or similar</option>
                                <option value="2">Dzire, Etios or similar</option>
                                <option value="3">Xylo, Ertiga or similar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabModel">Model Name</label>
                            <select class="form-select" id="cabModel">
                                <option hidden>Select cab model name</option>
                                <option value="Baleno">Baleno</option>
                                <option value="Swift">Swift</option>
                                <option value="Swift">Swift</option>
                                <option value="Etios">Etios</option>
            pti              <option value="Xylo"></option>
                                <option value="Ertiga">Ertiga</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="cabModel">Color</label>
                            <select class="form-select" id="cabModel">
                                <option hidden>Select cab color</option>
                                <option value="White">White</option>
                                <option value="Black">Black</option>
                                <option value="Gray">Gray</option>
                                <option value="Navy Blue">Navy Blue</option>
                                <option value="Blue">Blue</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="baseFare">Base Fare</label>
                                <input type="text" class="form-control" id="baseFare" placeholder="Enter base fare">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="kmNumber">No. of Kms Included</label>
                            <select class="form-select" id="kmNumber">
                                <option value="30">30 kms</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="addKm">Additional KM Charges</label>
                            <select class="form-select" id="addKm">
                                <option hidden>Select additional km charges</option>
                                <option value="20">Rs. 20</option>
                                <option value="25">Rs. 25</option>
                                <option value="30">Rs. 30</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="waitingCharge">Waiting Charges ( After 30 mins )</label>
                            <select class="form-select" id="waitingCharge">
                                <option value="Rs. 100/30 mins">Rs. 100/30 mins</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <div class="my-3">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>View Driver Pricing</h5>
                </div>
                <div class="card-body">
                    <div class="dt-responsive">
                        <table id="driverPricing" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Cab Type</th>
                                    <th>Cab Model</th>
                                    <th>Model Name</th>
                                    <th>Color</th>
                                    <th>Base Fare</th>
                                    <th>Kms</th>
                                    <th>Addl. KM</th>
                                    <th>Waiting Charges</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Hatchback</td>
                                    <td>Baleno, Swift or similar</td>
                                    <td>Maruti Swift</td>
                                    <td>Black</td>
                                    <td>Rs.150</td>
                                    <td>40</td>
                                    <td>Rs.12</td>
                                    <td>Rs.30</td>
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
                                    <td>Rs.200</td>
                                    <td>40</td>
                                    <td>Rs.15</td>
                                    <td>Rs.60</td>
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
                                    <td>Black</td>
                                    <td>Rs.300</td>
                                    <td>40</td>
                                    <td>Rs.18</td>
                                    <td>Rs.30</td>
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

    <!-- DataTable JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        // Data Table
        var table = $('#driverPricing').DataTable();

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
