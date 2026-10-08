@extends('layouts.main')

@section('title', 'View Deleted Drivers')
@section('breadcrumb-item', 'Cab Management')

@section('breadcrumb-item-active', 'View Deleted Drivers')

@section('css')

<!-- GLightBox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

<!-- Date Range CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <!-- Deleted Customers -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Deleted Drivers</h5>
                <button class="btn btn-light-info">Export</button>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="deletedDrivers" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Cab Number</th>
                                <th>Driver Name</th>
                                <th>Email ID</th>
                                <th>Mobile No</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-01-2025</td>
                                <td>GA01X1234</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        John Doe
                                    </div>
                                </td>
                                <td>johndoe@example.com</td>
                                <td>+1-234-567-8901</td>
                                <td>Poor Customer Service</td>
                            </tr>
                            <tr>
                                <td>01-01-2025</td>
                                <td>GA01X1234</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Jane Smith
                                    </div>
                                </td>
                                <td>janesmith@example.co.uk</td>
                                <td>+44-789-123-4567</td>
                                <td>Poor Customer Service</td>
                            </tr>
                            <tr>
                                <td>01-01-2025</td>
                                <td>GA01X1234</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rahul Verma
                                    </div>
                                </td>
                                <td>rahul.verma@example.in</td>
                                <td>+91-98765-43210</td>
                                <td>Poor Customer Service</td>
                            </tr>
                            <tr>
                                <td>01-01-2025</td>
                                <td>GA01X1234</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Maria Gonzalez
                                    </div>
                                </td>
                                <td>maria.g@example.es</td>
                                <td>+34-612-345-678</td>
                                <td>Poor Customer Service</td>
                            </tr>
                            <tr>
                                <td>01-01-2025</td>
                                <td>GA01X1234</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Ahmed Khan
                                    </div>
                                </td>
                                <td>ahmed.khan@example.ae</td>
                                <td>+971-50-123-4567</td>
                                <td>Poor Customer Service</td>
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

<!-- GLightBox -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<!-- Date Range JS -->
<script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>

<script>
    // [ DOM/jquery ]
    var table = $('#deletedDrivers').DataTable();

    // GLightBox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        width: "90vw",
        height: "90vh"
    });

    // Date Range 
    const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
        buttonClass: 'btn'
    });
</script>
<!-- [Page Specific JS] end -->
@endsection