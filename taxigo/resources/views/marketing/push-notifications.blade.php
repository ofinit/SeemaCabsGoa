@extends('layouts.main')

@section('title', 'Push Notifications')
@section('breadcrumb-item', 'Marketing')

@section('breadcrumb-item-active', 'Push Notifications')

@section('css')

<!-- GLightBox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

<!-- File Upload CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dropzone.min.css') }}">

<!-- Date Range CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">

<!-- Time Picker -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/flatpickr.min.css') }}">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Send Push Notifications</h5>
            </div>
            <div class="card-body">
                <div class="select-advertiser-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="notiUser">Select User</label>
                                <select data-trigger class="form-select" name="notiUser" id="notiUser">
                                    <option>All</option>
                                    <option>Advertisers</option>
                                    <option>Customers</option>
                                    <option>Drivers</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="notiUser">Live Location</label>
                                <select data-trigger class="form-select" name="notiUser" id="notiUser">
                                    <option value="AF">All</option>
                                    <option value="AP">Andhra Pradesh</option>
                                    <option value="AR">Arunachal Pradesh</option>
                                    <option value="AS">Assam</option>
                                    <option value="BR">Bihar</option>
                                    <option value="CT">Chhattisgarh</option>
                                    <option value="GA">Goa</option>
                                    <option value="GJ">Gujarat</option>
                                    <option value="HR">Haryana</option>
                                    <option value="HP">Himachal Pradesh</option>
                                    <option value="JH">Jharkhand</option>
                                    <option value="KA">Karnataka</option>
                                    <option value="KL">Kerala</option>
                                    <option value="MP">Madhya Pradesh</option>
                                    <option value="MH">Maharashtra</option>
                                    <option value="MN">Manipur</option>
                                    <option value="ML">Meghalaya</option>
                                    <option value="MZ">Mizoram</option>
                                    <option value="NL">Nagaland</option>
                                    <option value="OR">Odisha</option>
                                    <option value="PB">Punjab</option>
                                    <option value="RJ">Rajasthan</option>
                                    <option value="SK">Sikkim</option>
                                    <option value="TN">Tamil Nadu</option>
                                    <option value="TG">Telangana</option>
                                    <option value="TR">Tripura</option>
                                    <option value="UP">Uttar Pradesh</option>
                                    <option value="UT">Uttarakhand</option>
                                    <option value="WB">West Bengal</option>
                                    <option value="AN">Andaman and Nicobar Islands</option>
                                    <option value="CH">Chandigarh</option>
                                    <option value="DH">Dadra and Nagar Haveli and Daman and Diu</option>
                                    <option value="DL">Delhi</option>
                                    <option value="JK">Jammu and Kashmir</option>
                                    <option value="LA">Ladakh</option>
                                    <option value="LD">Lakshadweep</option>
                                    <option value="PY">Puducherry</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="dt-responsive">
                    <table id="customerDetails" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Gender</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>Email ID</th>
                                <th>Mobile No</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        John Doe
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>USA</td>
                                <td>California</td>
                                <td>johndoe@example.com</td>
                                <td>+1-234-567-8901</td>
                                <td>iOS</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Jane Smith
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>UK</td>
                                <td>London</td>
                                <td>janesmith@example.co.uk</td>
                                <td>+44-789-123-4567</td>
                                <td>iOS</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Rahul Verma
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Delhi</td>
                                <td>rahul.verma@example.in</td>
                                <td>+91-98765-43210</td>
                                <td>Android</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Maria Gonzalez
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>Spain</td>
                                <td>Madrid</td>
                                <td>maria.g@example.es</td>
                                <td>+34-612-345-678</td>
                                <td>Android</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Ahmed Khan
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>UAE</td>
                                <td>Dubai</td>
                                <td>ahmed.khan@example.ae</td>
                                <td>+971-50-123-4567</td>
                                <td>iOS</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Lisa Brown
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>Australia</td>
                                <td>Sydney</td>
                                <td>lisa.brown@example.au</td>
                                <td>+61-411-234-567</td>
                                <td>Android</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Kenji Takahashi
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>Japan</td>
                                <td>Tokyo</td>
                                <td>kenji.t@example.jp</td>
                                <td>+81-90-1234-5678</td>
                                <td>Android</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Aisha Mohammed
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>Egypt</td>
                                <td>Cairo</td>
                                <td>aisha.m@example.eg</td>
                                <td>+20-10-123-4567</td>
                                <td>Android</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        William Carter
                                    </div>
                                </td>
                                <td>Male</td>
                                <td>Canada</td>
                                <td>Ontario</td>
                                <td>william.carter@example.ca</td>
                                <td>+1-647-123-4567</td>
                                <td>iOS</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ URL::asset('build/images/user/avatar-1.jpg') }}" class="glightbox">
                                            <img src="{{ URL::asset('build/images/user/avatar-1.jpg') }}" alt="image"
                                                class="img-radius customer-profile-image" />
                                        </a>
                                        Priya Sharma
                                    </div>
                                </td>
                                <td>Female</td>
                                <td>India</td>
                                <td>Mumbai</td>
                                <td>priya.sharma@example.in</td>
                                <td>+91-99876-54321</td>
                                <td>iOS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="url-section mb-3">
                    <label class="form-label mb-2" for="notiTitle">Notification Title</label>
                    <input class="form-control" type="text" name="notiTitle" id="notiTitle"
                        placeholder="Enter title"></input>
                </div>
                <div class="file-upload-section">
                    <label class="form-label mb-2">Upload Notification Image</label>
                    <form action="/build/json/file-upload.php" class="dropzone">
                        <div class="fallback">
                            <input name="file" type="file" accept="images/*">
                        </div>
                    </form>
                </div>
                <div class="mt-3">
                    <label class="form-label mb-2">Notification Message</label>
                    <textarea id="notiMessage" name="notiMessage" class="tox-target"></textarea>
                </div>

                <div class="text-end m-t-20">
                    <button class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- Text Editor -->
<script src="{{ URL::asset('build/js/plugins/tinymce/tinymce.min.js') }}"></script>

<!-- SweetAlert JS -->
<script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

<!-- DataTable JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

<!-- GLightBox -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<!-- File Upload JS -->
<script src="{{ URL::asset('build/js/plugins/dropzone-amd-module.min.js') }}"></script>

<!-- Date Range JS -->
<script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>

<!-- Time Picker -->
<script src="{{ URL::asset('build/js/plugins/flatpickr.min.js') }}"></script>

<!-- Dropdown with Search -->
<script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

<script>

    // Data Table
    var table = $('#customerDetails').DataTable();

    // Text Editor
    tinymce.init({
        height: '400',
        selector: '#notiMessage',
        content_style: 'body { font-family: "Inter", sans-serif; }',
        menubar: false,
        statusbar: false,
        toolbar: 'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent',
        plugins: 'advlist autolink link image lists charmap print preview code'
    });

    // GLightBox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        width: "90vw",
        height: "90vh"
    });
</script>
<!-- [Page Specific JS] end -->
@endsection