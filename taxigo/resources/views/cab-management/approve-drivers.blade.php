@extends('layouts.main')

@section('title', 'Approve Drivers')
@section('breadcrumb-item', 'Cab Management')

@section('breadcrumb-item-active', 'Approve Drivers')

@section('css')

<!-- GLightBox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <!-- DOM/Jquery table start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Cab Details</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Driver Name</th>
                                <th>Driver Mobile</th>
                                <th>Cab Zone</th>
                                <th>Cab Type</th>
                                <th>Cab Model</th>
                                <th>Cab Number</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Rajesh Naik</td>
                                <td>9876543210</td>
                                <td>Panaji Bus Stand</td>
                                <td>Sedan</td>
                                <td>Honda City</td>
                                <td>GA01X1234</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>John D'Souza</td>
                                <td>9876501234</td>
                                <td>Calangute Beach</td>
                                <td>SUV</td>
                                <td>Toyota Fortuner</td>
                                <td>GA02X5678</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Baga Beach Parking</td>
                                <td>Hatchback</td>
                                <td>Maruti Baleno</td>
                                <td>GA03X9101</td>
                                <td>Rohan Pereira</td>
                                <td>9876523456</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Siddharth Shetty</td>
                                <td>9876543456</td>
                                <td>Mapusa Market</td>
                                <td>Sedan</td>
                                <td>Hyundai Verna</td>
                                <td>GA04X3456</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Prakash Rane</td>
                                <td>9876512345</td>
                                <td>Vasco Railway Station</td>
                                <td>SUV</td>
                                <td>Mahindra XUV500</td>
                                <td>GA05X7890</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Dona Paula Jetty</td>
                                <td>Hatchback</td>
                                <td>Ford Figo</td>
                                <td>GA06X2345</td>
                                <td>Akshay Kamat</td>
                                <td>9876598765</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Nitin Furtado</td>
                                <td>9876532145</td>
                                <td>Margao Market</td>
                                <td>Sedan</td>
                                <td>Skoda Octavia</td>
                                <td>GA07X6789</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Anil Naik</td>
                                <td>9876587654</td>
                                <td>Colva Beach Parking</td>
                                <td>SUV</td>
                                <td>Tata Harrier</td>
                                <td>GA08X9012</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Old Goa Church</td>
                                <td>Hatchback</td>
                                <td>Toyota Etios</td>
                                <td>GA09X4567</td>
                                <td>Francis Almeida</td>
                                <td>9876565432</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>12-12-2024</td>
                                <td>Ravi Chari</td>
                                <td>9876509876</td>
                                <td>Siolim Bridge</td>
                                <td>Sedan</td>
                                <td>Honda Amaze</td>
                                <td>GA10X1234</td>
                                <td>
                                    <h5><span class="badge text-bg-warning">Pending</span></h5>
                                </td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal" data-bs-target="#cabDetails"><i class="feather icon-eye"></i></a>
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- DOM/Jquery table end -->

    <!-- Model -->
    <div class="modal fade" id="cabDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Approve Drivers</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-1">
                        <div class="col-12">
                            <h5>Cab Details</h5>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Cab Zone</h6>
                            <p class="cab-model-location">Panaji</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Cab Number</h6>
                            <p class="cab-model-location">GA01X1234</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Cab Type</h6>
                            <p class="cab-model-location">Sedan</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Cab Model</h6>
                            <p class="cab-model-location">Honda City</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">No. of Seats</h6>
                            <p class="cab-model-location">3</p>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <h6 class="mb-1">Fuel Type</h6>
                            <p class="cab-model-location">Petrol</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="mb-1">Registration Certificate</h6>
                            <div class="document-container">
                                <div class="main-photo img-box">
                                    <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Registration Certificate(Front)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                        <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                    </a>
                                </div>
                                <div class="main-photo img-box">
                                    <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Registration Certificate(Back)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                        <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-1">Insurance</h6>
                            <div class="document-container">
                                <div class="main-photo img-box">
                                    <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Insurance" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                        <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h5 class="my-3">Driver Details</h5>
                        </div>

                        <div class="driver-1">
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-success">Driver <strong>Rajesh Naik</strong> Assigned to Cab</div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Driver Name</h6>
                                    <p class="cab-model-location">Rajesh Naik</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Driver Mobile</h6>
                                    <p class="cab-model-location">9876543210</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Bank Name</h6>
                                    <p class="cab-model-location">HDFC Bank</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Branch Name</h6>
                                    <p class="cab-model-location">Panaji</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Account Holder Name</h6>
                                    <p class="cab-model-location">Rajesh Naik</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Account Number</h6>
                                    <p class="cab-model-location">123456789012</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">IFSC Code</h6>
                                    <p class="cab-model-location">HDFC0001234</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">UPI ID</h6>
                                    <p class="cab-model-location">rajesh.naik@okhdfcbank</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Driving License Number</h6>
                                    <p class="cab-model-location">987456321</p>
                                </div>
                                <div class="col-12">
                                    <h6 class="mb-1">Aadhar Card Number</h6>
                                    <p class="cab-model-location">9874 2563 2154</p>
                                </div>

                                <div class="col-md-6">
                                    <p class="mb-1">Driving License</p>
                                    <div class="document-container">
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Driving License(Front)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Driving License(Back)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">Aadhar Card</p>
                                    <div class="document-container">
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Aadhar Card(Front)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Aadhar Card(Back)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="driver-2">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Driver Name</h6>
                                    <p class="cab-model-location">Rajesh Naik</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Driver Mobile</h6>
                                    <p class="cab-model-location">9876543210</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Bank Name</h6>
                                    <p class="cab-model-location">HDFC Bank</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Branch Name</h6>
                                    <p class="cab-model-location">Panaji</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Account Holder Name</h6>
                                    <p class="cab-model-location">Rajesh Naik</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Account Number</h6>
                                    <p class="cab-model-location">123456789012</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">IFSC Code</h6>
                                    <p class="cab-model-location">HDFC0001234</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">UPI ID</h6>
                                    <p class="cab-model-location">rajesh.naik@okhdfcbank</p>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <h6 class="mb-1">Driving License Number</h6>
                                    <p class="cab-model-location">987456321</p>
                                </div>
                                <div class="col-12">
                                    <h6 class="mb-1">Aadhar Card Number</h6>
                                    <p class="cab-model-location">9874 2563 2154</p>
                                </div>

                                <div class="col-md-6">
                                    <p class="mb-1">Driving License</p>
                                    <div class="document-container">
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Driving License(Front)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Driving License(Back)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">Aadhar Card</p>
                                    <div class="document-container">
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Aadhar Card(Front)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                        <div class="main-photo img-box">
                                            <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Aadhar Card(Back)" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                                <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Type Here -- Model Footer -->
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

<!-- GLightBox -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<script>
    // [ DOM/jquery ]
    var table = $('#dom-jqry').DataTable();
 
    // GLightBox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        width: "90vw",
        height: "90vh"
    });

    // Suspend Button Sweet Alert
    document.querySelectorAll('.sa-bs-error-ico').forEach(function(element) {
        element.addEventListener('click', function() {
            Swal.fire({
                icon: 'error',
                html: 'Are You Sure! You want to suspend Cab Number : <b>GA08X9012</b>',
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            })
        });
    });

    // Unsuspend Button Sweet Alert
    document.querySelectorAll('.sa-bs-unsuspend-ico').forEach(function(element) {
        element.addEventListener('click', function() {
            Swal.fire({
                icon: 'warning',
                html: 'Are You Sure! You want to un-suspend Cab Number : <b>GA08X9012</b>',
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