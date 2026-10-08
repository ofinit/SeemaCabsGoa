@extends('layouts.main')

@section('title', 'Ad Pricing')
@section('breadcrumb-item', 'Advertisements')

@section('breadcrumb-item-active', 'Ad Pricing')

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
                <h5>Home Page</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Leaderboard Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
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
                <h5>Search Results Page</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Leaderboard Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Box Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
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
                <h5>Booking Review Page</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Box Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
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
                <h5>Booking Confirmation Page</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Leaderboard Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Box Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
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
                <h5>Signup / Login Page</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Leaderboard Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="homePageAds">Box Ads</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter amount"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="homePageAds">Price Per Day</span>
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
                <h5>View Ad Pricing</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Pages</th>
                                <th>Leaderboard Ads Price</th>
                                <th>Box Ads Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Home Screen</td>
                                <td>999</td>
                                <td>--</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Search Results Page</td>
                                <td>599</td>
                                <td>499</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Booking Review Page</td>
                                <td>--</td>
                                <td>499</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Booking Confirmation Page</td>
                                <td>499</td>
                                <td>399</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Signup / Login Page</td>
                                <td>999</td>
                                <td>--</td>
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
<!-- [Page Specific JS] start -->

<!-- SweetAlert JS -->
<script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

<!-- DataTable JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

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