@extends('layouts.main')

@section('title', 'Add Advertisers')
@section('breadcrumb-item', 'Advertisements')

@section('breadcrumb-item-active', 'Add Advertisers')

@section('css')
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
                <h5>Add Advertisers</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="companyName">Company Name</label>
                            <input type="text" class="form-control" id="companyName" placeholder="Enter company name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="contactPerson">Contact Person Name</label>
                            <input type="text" class="form-control" id="contactPerson" placeholder="Enter person name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="companyMobile">Mobile Number</label>
                            <input type="number" class="form-control" id="companyMobile"
                                placeholder="Enter mobile number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="companyMail">Email ID</label>
                            <input type="email" class="form-control" id="companyMail" placeholder="Enter email id">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="countryName">Country</label>
                            <select class="form-select" id="countryName">
                                <option hidden>Select country</option>
                                <option>India</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="stateName">State</label>
                            <select class="form-select" id="stateName">
                                <option hidden>Select state</option>
                                <option>Goa</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="panCard">PAN</label>
                            <input type="text" class="form-control" id="panCard" placeholder="Enter pan number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="gstNumber">GST</label>
                            <input type="text" class="form-control" id="gstNumber" placeholder="Enter gst number">
                        </div>
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
                <h5>View Advertisers</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="addAdvertiserTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact Person Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>PAN</th>
                                <th>GST</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ABC Pvt Ltd</td>
                                <td>John Doe</td>
                                <td>9876543210</td>
                                <td>john.doe@example.com</td>
                                <td>India</td>
                                <td>Maharashtra</td>
                                <td>ABCDE1234F</td>
                                <td>27ABCDE1234F1Z5</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>XYZ Ltd</td>
                                <td>Jane Smith</td>
                                <td>8765432109</td>
                                <td>jane.smith@example.com</td>
                                <td>USA</td>
                                <td>California</td>
                                <td>XYZPL5678D</td>
                                <td>22XYZPL5678D1Z2</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Global Inc</td>
                                <td>Alice Johnson</td>
                                <td>7654321098</td>
                                <td>alice.johnson@example.com</td>
                                <td>UK</td>
                                <td>England</td>
                                <td>GHJRT2345L</td>
                                <td>11GHJRT2345L1A3</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Innovate Tech</td>
                                <td>Robert Brown</td>
                                <td>6543210987</td>
                                <td>robert.brown@example.com</td>
                                <td>Canada</td>
                                <td>Ontario</td>
                                <td>MKLPQ6789R</td>
                                <td>09MKLPQ6789R2B4</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Blue Sky Ltd</td>
                                <td>Emily White</td>
                                <td>5432109876</td>
                                <td>emily.white@example.com</td>
                                <td>Australia</td>
                                <td>Victoria</td>
                                <td>FRWNB4567G</td>
                                <td>33FRWNB4567G1T7</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Skyline Corp</td>
                                <td>Chris Green</td>
                                <td>4321098765</td>
                                <td>chris.green@example.com</td>
                                <td>Germany</td>
                                <td>Bavaria</td>
                                <td>ZXYWO1234B</td>
                                <td>44ZXYWO1234B3P8</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>NextGen Systems</td>
                                <td>Laura Black</td>
                                <td>3210987654</td>
                                <td>laura.black@example.com</td>
                                <td>France</td>
                                <td>Île-de-France</td>
                                <td>JKLTY9876H</td>
                                <td>55JKLTY9876H1C9</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Prime Tech</td>
                                <td>Michael King</td>
                                <td>2109876543</td>
                                <td>michael.king@example.com</td>
                                <td>Japan</td>
                                <td>Tokyo</td>
                                <td>VWXOP3456L</td>
                                <td>66VWXOP3456L1D6</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Orbit Solutions</td>
                                <td>Susan Harris</td>
                                <td>1098765432</td>
                                <td>susan.harris@example.com</td>
                                <td>China</td>
                                <td>Beijing</td>
                                <td>UYWEC5432N</td>
                                <td>77UYWEC5432N1M5</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Fusion Ltd</td>
                                <td>Kevin Wright</td>
                                <td>9871236540</td>
                                <td>kevin.wright@example.com</td>
                                <td>Brazil</td>
                                <td>São Paulo</td>
                                <td>HJKQW6789X</td>
                                <td>88HJKQW6789X1Z4</td>
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
    // [ Add Advertisers Table ]
    var table = $('#addAdvertiserTable').DataTable();

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

    window.onload = function () {
        document.getElementById('companyMail').value = '';
    };
</script>
<!-- [Page Specific JS] end -->
@endsection