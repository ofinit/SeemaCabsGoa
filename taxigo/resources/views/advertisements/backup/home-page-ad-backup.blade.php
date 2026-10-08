@extends('layouts.main')

@section('title', 'Customer Rates')
@section('breadcrumb-item', 'Advertisements')

@section('breadcrumb-item-active', 'Homepage Ads')

@section('css')

<!-- GLightBox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

<!-- File Upload CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dropzone.min.css') }}">

<!-- Date Range CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Advertisements Upload</h5>
            </div>
            <div class="card-body">
                <div class="select-advertiser-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="selectAdvertisers">Select Advertisers</label>
                                <select data-trigger class="form-select" name="selectAdvertisers" id="selectAdvertisers">
                                    <option value="">Select</option>
                                    <option>John Doe</option>
                                    <option>Jane Smith</option>
                                    <option>Alice Johnson</option>
                                    <option>Robert Brown</option>
                                    <option>Emily White</option>
                                    <option>Chris Green</option>
                                    <option>Laura Black</option>
                                    <option>Michael King</option>
                                    <option>Susan Harris</option>
                                    <option>Kevin Wright</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="datepicker_range">Select Date Range</label>
                            <div class="input-daterange input-group" id="datepicker_range">
                                <input type="text" class="form-control text-left" placeholder="Start date" name="range-start">
                                <input type="text" class="form-control text-end end-ad-date" placeholder="End date" name="range-end">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="file-upload-section">
                    <label class="form-label mb-2">Upload Banner Image</label>
                    <form action="/build/json/file-upload.php" class="dropzone">
                        <div class="fallback">
                            <input name="file" type="file" accept="images/*">
                        </div>
                    </form>
                </div>
                <div class="url-section mt-3">
                    <label class="form-label mb-2" for="adsUrl">Banner URL</label>
                    <input class="form-control" type="url" name="adsUrl" id="adsUrl" placeholder="Enter your url">
                </div>
                <div class="text-end m-t-20">
                    <button class="btn btn-primary">Upload Now</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>View Ad Details</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Advertiser</th>
                                <th>Validity</th>
                                <th>Ad Image</th>
                                <th>Banner URL</th>
                                <th>No. of Clicks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Company Name</td>
                                <td>02-12-2024 to 15-12-2024</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel1.com" target="_blank">https://www.examplehotel1.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>10-12-2024 to 25-12-2024</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel2.com" target="_blank">https://www.examplehotel2.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>20-12-2024 to 05-01-2025</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel3.com" target="_blank">https://www.examplehotel3.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>01-01-2025 to 15-01-2025</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel4.com" target="_blank">https://www.examplehotel4.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>12-01-2025 to 28-01-2025</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel5.com" target="_blank">https://www.examplehotel5.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>18-12-2024 to 02-01-2025</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel6.com" target="_blank">https://www.examplehotel6.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>05-01-2025 to 20-01-2025</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel7.com" target="_blank">https://www.examplehotel7.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>10-01-2025 to 25-01-2025</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel8.com" target="_blank">https://www.examplehotel8.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>28-11-2024 to 12-12-2024</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel9.com" target="_blank">https://www.examplehotel9.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>15-02-2025 to 28-02-2025</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel10.com" target="_blank">https://www.examplehotel10.com</a></td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
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

<!-- Dropdown with Search -->
<script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

<script>
    // [ DOM/jquery ]
    var total, pageTotal;
    var table = $('#dom-jqry').DataTable();
    // [ column Rendering ]
    $('#colum-render').DataTable({
        columnDefs: [{
                render: function(data, type, row) {
                    return data + ' (' + row[3] + ')';
                },
                targets: 0
            },
            {
                visible: false,
                targets: [3]
            }
        ]
    });
    // [ Multiple Table Control Elements ]
    $('#multi-table').DataTable({
        dom: '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>'
    });
    // [ Complex Headers With Column Visibility ]
    $('#complex-header').DataTable({
        columnDefs: [{
            visible: false,
            targets: -1
        }]
    });
    // [ Language file ]
    $('#lang-file').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'
        }
    });
    // [ Setting Defaults ]
    $('#setting-default').DataTable();
    // [ Row Grouping ]
    var table1 = $('#row-grouping').DataTable({
        columnDefs: [{
            visible: false,
            targets: 2
        }],
        order: [
            [2, 'asc']
        ],
        displayLength: 25,
        drawCallback: function(settings) {
            var api = this.api();
            var rows = api
                .rows({
                    page: 'current'
                })
                .nodes();
            var last = null;

            api
                .column(2, {
                    page: 'current'
                })
                .data()
                .each(function(group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before('<tr class="group"><td colspan="5">' + group + '</td></tr>');

                        last = group;
                    }
                });
        }
    });
    // [ Order by the grouping ]
    $('#row-grouping tbody').on('click', 'tr.group', function() {
        var currentOrder = table.order()[0];
        if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
            table.order([2, 'desc']).draw();
        } else {
            table.order([2, 'asc']).draw();
        }
    });
    // [ Footer callback ]
    $('#footer-callback').DataTable({
        footerCallback: function(row, data, start, end, display) {
            var api = this.api(),
                data;

            // Remove the formatting to get integer data for summation
            var intVal = function(i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            // Total over all pages
            total = api
                .column(4)
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Total over this page
            pageTotal = api
                .column(4, {
                    page: 'current'
                })
                .data()
                .reduce(function(a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Update footer
            $(api.column(4).footer()).html('$' + pageTotal + ' ( $' + total + ' total)');
        }
    });
    // [ Custom Toolbar Elements ]
    $('#c-tool-ele').DataTable({
        dom: '<"toolbar">frtip'
    });
    // [ Custom Toolbar Elements ]
    $('div.toolbar').html('<b>Custom tool bar! Text/images etc.</b>');
    // [ custom callback ]
    $('#row-callback').DataTable({
        createdRow: function(row, data, index) {
            if (data[5].replace(/[\$,]/g, '') * 1 > 150000) {
                $('td', row).eq(5).addClass('highlight');
            }
        }
    });

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

    // Select with Search
    document.addEventListener('DOMContentLoaded', function() {
        var element = document.querySelector('#selectAdvertisers');
        new Choices(element, {
            searchPlaceholderValue: 'Search Advertisers'
        });

        var element = document.querySelector('#selectPages');
        new Choices(element, {
            searchPlaceholderValue: 'Search Page'
        });
    })

    // Date Range 
    const datepicker_range = new DateRangePicker(document.querySelector('#datepicker_range'), {
        buttonClass: 'btn'
    });
</script>
<!-- [Page Specific JS] end -->
@endsection