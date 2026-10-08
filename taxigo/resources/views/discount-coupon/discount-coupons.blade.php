@extends('layouts.main')
@section('title', 'Discount Coupons')
@section('breadcrumb-item', 'Discount Coupons')
@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endsection

@section('content')
<div class="row">
    <h2 class="mb-4">Discount Coupons</h2>
    <div class="col-12">
        @include('layouts.message')
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{route('admin.coupons.create')}}" class="btn btn-sm btn-light-secondary"><i
                        data-feather="plus"></i> Create Coupon</a>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="discountCouponTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Discount Type</th>
                                <th>Description</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(function(){
        $('#discountCouponTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.coupons.list') }}",
                type: "POST",
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data._token = "{{ csrf_token() }}";
                }
            },
            order: [[1, 'DESC']],
            pageLength: 10,
            searching: true,
            columns: [
                { data: 'code', name: 'code' },
                { data: 'value', name: 'value' },
                { data: 'type', name: 'type', render: function(data, type, row) {
                    return (row.type==1)?'Percent':'Fixed';
                }},
                { data: 'description', name: 'description' , render: function(data, type, row) {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }},
                { data: 'expiry_date', name: 'expiry_date' },
                { data: 'status', name: 'status', render: function(data, type, row) {
                    return (row.status)?`<span class="badge bg-success">Enable</span>`:`<span class="badge bg-danger">Disable</span>`;
                }},
                { data: 'id', name: 'id', render: function(data, type, row) {
                    var editUrl = '{{ route("admin.coupons.edit", ":id") }}'.replace(':id', row.id);
                    var deleteUrl = '{{ route("admin.coupons.delete", ":id") }}'.replace(':id', row.id);
                    var changeUrl = '{{ route("admin.coupons.change", ":id") }}'.replace(':id', row.id);
                    var html = ` <a href="${editUrl}" class="btn btn-sm btn-light-success me-1"><i
                                        class="feather icon-edit"></i></a>
                                <span  class="btn btn-sm btn-light-danger sa-bs-error-ico deleteCoupon" data-url="${deleteUrl}"><i
                                        class="feather icon-trash-2"></i></span>
                                        <div class="form-check form-switch custom-switch-v1 mt-3">
                                            Show on App
                                            <input type="checkbox" class="form-check-input input-success appUpdateSwitch" name="appUpdateSwitch" id="appUpdateSwitch" ${(row.show_app==1)?'checked':''} data-url="${changeUrl}">
                                        </div>`;
                    return html;
                }}
            ]
        });

        // delete 
        $(document).on('click', '.deleteCoupon', function (e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't delete this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Proceed!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function (response) {
                            Swal.fire("Deleted!", "Discount coupon has been deleted.", "success");
                            location.reload();
                        },
                        error: function () {
                            Swal.fire("Error!", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });

        $(document).on('change', '.appUpdateSwitch', function (e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't change this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Proceed!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function (response) {
                            Swal.fire("Changed!", "Changed successfully.", "success");
                            location.reload();
                        },
                        error: function () {
                            Swal.fire("Error!", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@endsection