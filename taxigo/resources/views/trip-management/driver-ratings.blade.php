@extends('layouts.main')

@section('title', 'Driver Ratings')
@section('breadcrumb-item', 'Trip Management')

@section('breadcrumb-item-active', 'Driver Ratings')

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
                <h5>Driver Rating Details</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive upcoming-trips">
                    <table id="feedbackTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Pickup Date/Time</th>
                                <th>Booking ID</th>
                                <th>Pickup Address</th>
                                <th>Drop-off Address</th>
                                <th>Traveller Name</th>
                                <th>Fleet Operator</th>
                                <th>Driver Name</th>
                                <th>Avg. Ratings</th>
                                <th>Ratings</th>
                                <th>Feedbacks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-12-06 10:30 AM</td>
                                <td>BK12350</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Calangute Beach</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        ( 4.5 )
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>Driver Refused to Turn on the AC Despite the Heat</td>
                            </tr>
                            <tr>
                                <td>2024-12-07 02:00 PM</td>
                                <td>BK12351</td>
                                <td>Panjim Bus Stand</td>
                                <td>Vasco Railway Station</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        ( 4.5 )
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings-gray.svg') }}"
                                                class="gray-rating-img" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings-gray.svg') }}"
                                                class="gray-rating-img" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings-gray.svg') }}"
                                                class="gray-rating-img" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>Unexplained Cancellation at the Last Minute</td>
                            </tr>
                            <tr>
                                <td>2024-12-08 09:30 AM</td>
                                <td>BK12352</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>Vagator Beach</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        ( 4.5 )
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings-gray.svg') }}"
                                                class="gray-rating-img" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>Safety Concern: Rash Driving by the Driver</td>
                            </tr>
                            <tr>
                                <td>2024-12-09 11:15 AM</td>
                                <td>BK12353</td>
                                <td>Mapusa Bus Stand</td>
                                <td>Goa Airport, Dabolim</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        ( 4.5 )
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings-gray.svg') }}"
                                                class="gray-rating-img" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings-gray.svg') }}"
                                                class="gray-rating-img" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>Driver Did Not Assist with Luggage</td>
                            </tr>
                            <tr>
                                <td>2024-12-10 04:30 PM</td>
                                <td>BK12354</td>
                                <td>Vasco Railway Station</td>
                                <td>Panjim Bus Stand</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>Operator-1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Charlie White
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        ( 4.5 )
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                        <div class="rating-img">
                                            <img src="{{ URL::asset('build/images/rating/ratings.svg') }}" alt="">
                                        </div>
                                    </div>
                                </td>
                                <td>App Glitch Charged Me for a Ride I Didn’t Take</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- DOM/Jquery table end -->

    <!-- Model -->
    <div class="modal fade" id="complaintDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">View Complaint Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gy-1">
                        <div class="col-12">
                            <h5 class="mt-2">Feedbacks</h5>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-secondary" role="alert">App Glitch Charged Me for a Ride I Didn’t
                                Take</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="cabType">Action</label>
                            <select class="form-select" id="cabType">
                                <option hidden>Select action</option>
                                <option>Pending</option>
                                <option>Resolved</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-12 text-end mt-3">
                        <button class="btn btn-primary">Save</button>
                    </div>
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
    // Data Table
    var table = $('#feedbackTable').DataTable();

    // GLightBox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        width: "90vw",
        height: "90vh"
    });

    // Delete Button Sweet Alert
    document.querySelectorAll('.sa-bs-error-ico').forEach(function(element) {
        element.addEventListener('click', function() {
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