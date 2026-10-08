@extends('layouts.main')

@section('title', 'Notification')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Notification</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- View CUstomers -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    <a href="{{ route('admin.notifications.create') }}" class="text-decoration-none">
                        <button type="button" class="btn btn-light-primary">Send Notification</button>
                    </a>
                </div>
                <div class="card-body">

                    <div class="dt-responsive">
                        <table id="customerTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Text</th>
                                    <th>Status</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $('#customerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.notifications.list') }}",
                type: "POST",
                data: function(data) {
                    data.search = $('input[type="search"]').val();
                    data._token = "{{ csrf_token() }}";
                }
            },
            order: [
                [1, 'DESC']
            ],
            pageLength: 10,
            searching: true,
            columns: [
                {
                    data: 'title',
                    name: 'title',
                    render: function(data, type, row) {
                       return data;
                    }
                },
                {
                    data: 'text',
                    name: 'text',
                    render: function(data, type, row) {
                       return data;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        var status = ``;
                        if(data==1){
                           status =  `<h5><span class="badge text-bg-success">Read</span></h5>`;
                        }else{
                            status =  `<h5><span class="badge text-bg-danger">Unread</span></h5>`;
                        }
                        return status;
                    }
                },
                // {
                //     data: 'id',
                //     name: 'id',
                //     render: function(data, type, row) {
                //         var url = '{{ route('admin.notifications.delete', ':id') }}'.replace(':id',
                //             row.id);
                //         var html =  `<button class="btn btn-sm btn-outline-danger sa-bs-error-ico deleteNotification" data-url="${url}"><i class="ph-duotone ph-trash"></i></button>`;
                //         return html;
                //     }
                // }
            ],
            drawCallback: function() {
                GLightbox({
                    touchNavigation: true,
                    loop: true,
                    width: "90vw",
                    height: "90vh"
                });
            }
        });
        // delete
        $(document).on('click', '.deleteNotification', function(e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this!",
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
                        success: function(response) {
                           if(response.status){
                                 Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                           }else{
                                 Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    confirmButtonText: 'OK',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                           }
                        },
                        error: function() {
                            Swal.fire("Error!", "Something went wrong.", "error");
                        }
                    });
                }
            });
        });
    </script>
@endsection
