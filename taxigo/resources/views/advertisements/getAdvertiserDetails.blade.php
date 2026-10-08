<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<div class="dt-responsive">
    <table id="adViewDetails" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Date</th>
                <th>Advertiser</th>
                <th>Name</th>
                <th>Mobile Number</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($advertiser) && count($advertiser) > 0)
                @foreach ($advertiser as $key => $list)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $list->date ? \Carbon\Carbon::parse($list->date)->format('d-m-Y') : '' }}</td>
                        <td>{{ $list->company_name ?? '' }}</td>
                        <td>{{ $list->name ?? '' }}</td>
                        <td>+{{ $list->phone_number ?? '' }}</td>
                        <td>{{ $list->email ?? '' }}</td>
                    </tr>
                @endforeach
            @else
                <tr class="text-center">
                    <td colspan="6">
                        No Data Available
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        $('#adViewDetails').DataTable();
    });
</script>
