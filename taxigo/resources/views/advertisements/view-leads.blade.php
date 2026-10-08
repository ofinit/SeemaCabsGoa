<style>

</style>
<div class="modal fade" id="viewLeadsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="viewLeadsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h4" id="viewLeadsModalLabel" style="color:#1A1E23;">View Leads </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{route('admin.advertisements.activeAdds.exportLeadDetails',['id'=>$advertisement->id])}}" style="text-decoration: none">
                        <button type="button" id="exportLeadsBtn" class="btn btn-light-primary">Export</button>
                    </a>
                </div>
                <input type="hidden" id="ad_id" value="{{$advertisement->id}}">
                <div class="dt-responsive">
                    <table id="viewLeadsTable" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Mobile Number</th>
                                <th>Email ID</th>
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
<script>
    $(document).ready(function () {

        var id = $('#ad_id').val();
        var urlTemplate = '{{ route('admin.advertisements.activeAdds.viewLeadsList', [':id']) }}';
        var viewLeadListUrl = urlTemplate.replace(':id',id);
        $('#viewLeadsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: viewLeadListUrl,
                type: "POST",
                data: function (data) {
                    data.search = $('input[type="search"]').val();
                    data._token = "{{ csrf_token() }}";
                }
            },
            order: [
                [1, 'DESC']
            ],
            searching: false,
            lengthChange: false,
            pageLength: 10,
            language: {
                info: "_START_ to _END_ of _TOTAL_",
                paginate: {
                    previous: "prev",
                    next: "next"
                }
            },
            pagingType: "simple_numbers",
            columns: [
                {
                    data: null,
                    name: '#',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'date',
                    name: 'date',
                    render: function (data, type, row) {
                        return data;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    render: function (data, type, row) {
                        return data;
                    }
                },
                {
                    data: 'mobile',
                    name: 'mobile',
                    render: function (data, type, row) {
                        return data;
                    }
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function (data, type, row) {
                        return data;
                    }
                },
            ]
        });

    });
</script>
