<form class="assignDriverForm" method="POST" action="{{ route('admin.trips.assignDriver') }}">
    @csrf
    <div class="modal-body p-0">
        <div class="row p-4">
            <div class="col-12">
                <div class="mb-3 verticle-line">
                    <input type="hidden" name="booking_id" value="{{ $bookingId }}" id="">
                    <label class="choices-single-default">Search Driver / Cab Number / Mobile Number / Cab Model Name</label>
                    <select name="driverId" class="form-control" data-trigger id="choices-single-default" required
                        data-parsley-required-message="Please select driver.">
                        <option value="" hidden>Select Drivers</option>
                        @if (isset($driverList) && $driverList != null && count($driverList)>0)
                            @foreach ($driverList as $key=>$list)
                            
                                <option value="{{ $list->id }}" {{ $bookingDetails->assigned_driver_id==$list->id?'selected':'' }} {{ $bookingDetails->assigned_driver_id==$list->id?'disabled':'' }}>{{ $list->name ?? '' }}/{{ $list->getCabDetails ? $list->getCabDetails->number : '' }}/{{ $list->mobile ?? '' }}/{{ $list->getCabDetails ? ($list->getCabDetails->getCabModelDetails ? $list->getCabDetails->getCabModelDetails->name : '') : '' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary bs-success-ico">Assign Driver</button>
    </div>
</form>


<script>
    $('.assignDriverForm').parsley();
    // Select with Search
    document.addEventListener('DOMContentLoaded', function () {
        var genericExamples = document.querySelectorAll('[data-trigger]');
        for (i = 0; i < genericExamples.length; ++i) {
            var element = genericExamples[i];
            new Choices(element, {
                placeholderValue: 'Search driver',
                searchPlaceholderValue: 'Search Driver / Cab Number / Mobile Number / Cab Model Name',
            });
        }
    })
</script>