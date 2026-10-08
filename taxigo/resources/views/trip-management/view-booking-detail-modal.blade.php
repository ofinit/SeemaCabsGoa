<style>
    .driver-btn {
        background-color: #c953ff;
        color: #ffffff;
        border: 2px solid #c953ff;
    }

    .driver-btn:hover,
    .driver-btn:active {
        background-color: #c953ff;
        color: #ffffff;
        border: 2px solid #c953ff;
    }

    .customer-btn {
        background-color: #ff9b28;
        color: #ffffff;
        border: 2px solid #ff9b28;
    }

    .customer-btn:hover,
    .customer-btn:active {
        background-color: #ff9b28;
        color: #ffffff;
        border: 2px solid #ff9b28;
    }
    .label-main-heading{
        font-weight: 700;
        font-size: 18px;
        color:#1A1E23;
    }
    .label-heading
    {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 2px;
        color:#1A1E23;
    }
    .label-data{
        font-weight: 400;
        font-size: 16px;
        color: #666666;
    }
</style>
@php
    $base_fare = isset($bookingDetail->base_fare) ? getCurrencySign(). number_format(($bookingDetail->base_fare),2) : '--';
    $balance_amount = isset($bookingDetail->part_payment) && isset($bookingDetail->base_fare) ? getCurrencySign(). number_format((($bookingDetail->total_payment + $bookingDetail->tax_amount) - $bookingDetail->part_payment),2) : '--';
    $total_amount = isset($bookingDetail->total_payment) ? getCurrencySign(). number_format($bookingDetail->total_payment + $bookingDetail->tax_amount,2 ) : '--' ;
    $part_payment = isset($bookingDetail->part_payment) ? getCurrencySign().$bookingDetail->part_payment : '--' ;

@endphp
<div class="modal fade" id="bookingDetailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h4" id="bookingDetailModalLabel" style="color:#1A1E23;">View Trip Details</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row d-flex justify-content-between align-items-center">
                    <div class="col-md-5">
                        <h2 style="color:#1A1E23;">OTP : {{ isset($bookingDetail->trip_otp) ? $bookingDetail->trip_otp : '-' }}</h2>
                    </div>
                    <div class="col-md-7 d-flex justify-content-end">
                        <div class="me-3">
                            <button type="button" class="btn driver-btn" id="copy-driver-btn">
                                Send to Driver <i class="ti ti-copy"></i>
                            </button>
                        </div>
                        <div class="me-3">
                            <button type="button" class="btn driver-btn" id="copy-customer-btn">
                                Send to Confirmed Driver <i class="ti ti-copy"></i>
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn customer-btn" id="copy-confirmed-driver-btn">
                                Send to Customer <i class="ti ti-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-1">
                    <p class="label-main-heading">Trip Details
                    <span class="label-data">
                        @isset($bookingDetail->status)
                            @php
                                $status = '--';

                                if (!empty($bookingDetail->assigned_driver_id)) {
                                    // Assuming this means driver assigned if assigned_driver_id exists and status is something else
                                    $status = '<span class="badge rounded-pill bg-light-dark text-decoration-none">Driver Assigned</span>';
                                }
                                elseif ($bookingDetail->status == 3) {
                                    $status = '<span class="badge rounded-pill bg-light-danger text-decoration-none">Cancelled</span> <span class="badge rounded-pill bg-light-warning text-decoration-none">Refund</span>';
                                } elseif ($bookingDetail->status == 4 && $bookingDetail->refund) {
                                    $status = '<span class="badge rounded-pill bg-light-info text-decoration-none">Refunded</span>';
                                } elseif ($bookingDetail->status == 4) {
                                    $status = '<span class="badge rounded-pill bg-light-success text-decoration-none">Completed</span>';
                                }
                            @endphp

                            {!! $status !!}
                        @endisset
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <p class="label-heading">Booking Date</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->booking_date) ? $bookingDetail->booking_date : '--' }}</p>
                        </div>
                        <div>
                            <p class="label-heading">Trip Type</p>
                            <p class="label-data">{{ isset($bookingDetail->trip_type) ? $bookingDetail->trip_type : '--' }}<br>
                                {{ isset($bookingDetail->sightSeeingPackage) ? $bookingDetail->sightSeeingPackage->title : '' }}</p>
                        </div>
                        <div>
                            <p class="label-heading">Pickup Address</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->pickup_address) ? $bookingDetail->pickup_address : '--' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Pickup Date/Time</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->pickup_date) && isset($bookingDetail->pickup_time) ? "$bookingDetail->pickup_date $bookingDetail->pickup_time" : '--' }}
                            </p>
                        </div>
                        <div>
                            <p class="label-heading">Pickup From</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->pickup_from) ? $bookingDetail->pickup_from : '--' }}</p>
                        </div>
                        <div>
                            <p class="label-heading">Drop-off Address</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->drop_of_address) ? $bookingDetail->drop_of_address : '--' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Booking ID</p>
                            <p class="label-data">{{ isset($bookingDetail->booking_id) ? $bookingDetail->booking_id : '--' }}
                            </p>
                        </div>
                        <div>
                            <p class="label-heading">Drop To</p>
                            <p class="label-data">{{ isset($bookingDetail->drop_to) ? $bookingDetail->drop_to : '--' }}</p>
                        </div>
                        <div>
                            <p class="label-heading">Cab Type</p>
                            <p class="label-data">{{ isset($bookingDetail->cab_type) ? $bookingDetail->cab_type : '--' }}</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mt-1">
                    <p class="label-main-heading">Customer Details
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <p class="label-heading">Traveller Name</p>
                            <p class="label-data">{{ isset($bookingDetail->user) ? $bookingDetail->user->name : '--' }}</p>
                        </div>
                        <div>
                            <p class="label-heading">State</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->user) &&  $bookingDetail->user->getStateDetails ? $bookingDetail->user->getStateDetails->name : '--' }}
                            </p>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Gender</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->user)
                                    ? (isset($bookingDetail->user->gender) && $bookingDetail->user->gender !== null
                                        ? ($bookingDetail->user->gender == 1
                                            ? 'Male'
                                            : ($bookingDetail->user->gender == 0
                                                ? 'Female'
                                                : '--'))
                                        : '--')
                                    : '--' }}
                            </p>
                        </div>
                        <div>
                            <p class="label-heading">Country</p>
                            <p class="label-data">
                                {{ isset($bookingDetail->user) ? $bookingDetail->user->getCountryDetails->name : '--' }}</p>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Mobile</p>
                            <p class="label-data">{{ isset($bookingDetail->user) ? "+91".$bookingDetail->user->phone_number : '--' }}
                            </p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mt-1">
                    <p class="label-main-heading">Device Details
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <p class="label-heading">OS</p>
                            <p class="label-data">{{ isset($bookingDetail->user) && $bookingDetail->user->device != null? $bookingDetail->user->device : '--' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Version</p>
                            <p class="label-data">--</p>
                        </div>

                    </div>
                </div>
                <hr>
                <div class="mt-1">
                    <p class="label-main-heading">Driver Details
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <p class="label-heading">Driver Name</p>
                            <p class="label-data">{{ isset($bookingDetail->assignDriver) ? $bookingDetail->assignDriver->name : '--' }}</p>
                        </div>
                        <div>
                            <p class="label-heading">Cab Color</p>
                            <p class="label-data">{{ isset($bookingDetail->assignDriver->getCabDetails) ? $bookingDetail->assignDriver->getCabDetails->getColorDetails->name : '--' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Driver Mobile Number</p>
                            <p class="label-data">{{ isset($bookingDetail->assignDriver) ? "+91".$bookingDetail->assignDriver->mobile : '--' }}</p>
                        </div>
                        <div>
                            <p class="label-heading">Cab Number</p>
                            <p class="label-data">{{ isset($bookingDetail->assignDriver->getCabDetails) ? $bookingDetail->assignDriver->getCabDetails->number : '--'  }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Cab Type</p>
                            <p class="label-data">{{ isset($bookingDetail->assignDriver) && isset($bookingDetail->cab_type) ? $bookingDetail->cab_type : '--' }}</p>
                            {{-- <p class="label-data">
                                @isset($bookingDetail->assignDriver->getCabDetails)
                                    @php
                                        $model = '--';
                                        switch ($bookingDetail->assignDriver->getCabDetails->model) {
                                            case 1:
                                                $model = 'Baleno, Swift or similar';
                                                break;
                                            case 3:
                                                $model = 'Dzire, Etios or similar';
                                                break;
                                            case 4:
                                                $model = 'Xylo, Ertiga or similar';
                                                break;
                                            default:
                                                $model = '--';
                                                break;
                                        }
                                    @endphp
                                    {{ $model }}
                                @endisset
                            </p> --}}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mt-1">
                    <p class="label-main-heading">Payment Details
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <p class="label-heading">Part Payment</p>
                            <p class="label-data">{{ $part_payment}}</p>
                        </div>
                        <div>
                            <p class="label-heading">Balance Amount</p>
                            <p class="label-data">{{ $balance_amount }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Base Fare</p>
                            <p class="label-data">{{ $base_fare }}</p>
                        </div>
                        <div>
                            <p class="label-heading">Total Amount</p>
                            <p class="label-data">{{ $total_amount }}</p>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div>
                            <p class="label-heading">Other Charges</p>
                            <p class="label-data">{{ isset($bookingDetail->tax_amount) ? getCurrencySign(). $bookingDetail->tax_amount : '--' }}</p>
                        </div>
                         <div>
                            <p class="label-heading">Refund</p>
                            <p class="label-data">{{ isset($bookingDetail->refund) ? getCurrencySign().$bookingDetail->refund : '--' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var booking_date = "{{ isset($bookingDetail->booking_date) ? $bookingDetail->booking_date : '-' }}";
    var cab_type = "{{ isset($bookingDetail->cab_type) ? $bookingDetail->cab_type : '-' }}";
    var pickup_time = "{{isset($bookingDetail->pickup_time) ? $bookingDetail->pickup_time : '' }}";
    var pickup_address = "{{ isset($bookingDetail->pickup_address) ? $bookingDetail->pickup_address : '-' }}";
    var drop_off_address = "{{ isset($bookingDetail->drop_of_address) ? $bookingDetail->drop_of_address : '-' }}";

    var booking_id = "{{ isset($bookingDetail->booking_id) ? $bookingDetail->booking_id : '-' }}";
    var customer_name = "{{ isset($bookingDetail->user) ? $bookingDetail->user->name : '-' }}";
    var customer_mobile = "{{ isset($bookingDetail->user) ? '+91'.$bookingDetail->user->phone_number : '-' }}";
    var balance_payment = "{{ $balance_amount}}";

    var base_fare = "{{ $base_fare }}";
    var part_payment = "{{ $part_payment}}";
    var total_amount = "{{ $total_amount}}";

    var otp = "{{ isset($bookingDetail->trip_otp) ? $bookingDetail->trip_otp : '-' }}";

    $(document).ready(function () {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "timeOut": "3000",
        };

        $('#copy-driver-btn').on('click', function () {
            copyCustomerDetails();
            toastr.success('Copied Driver Details!');
        });

        $('#copy-customer-btn').on('click', function () {
            copyDriverDetails();
            toastr.success('Copied Customer Details!');
        });

        $('#copy-confirmed-driver-btn').on('click', function () {
            copyConfirmedDriver();
            toastr.success('Copied Confirmed Driver!');
        });
    });

    function copyDriverDetails() {
        //copy this details Booking Date,Cab Type,Pickup Time,Pickup Address,Dropoff Address
        var dataCopy =
        "Booking Date: " + booking_date +
        "\nCab Type: " + cab_type +
        "\nPickup Time: " + pickup_time +
        "\nPickup Address: " + pickup_address +
        "\nDropoff Address: " + drop_off_address;

        // Use modern Clipboard API
        navigator.clipboard.writeText(dataCopy).then(function() {
            console.log("Driver details copied to clipboard!");
        }).catch(function(error) {
            console.error("Failed to copy text: ", error);
        });
    }

    function copyCustomerDetails() {
        //copy this details Booking ID, Booking Date , Cab Type ,Pickup Time,Pickup Address,Dropoff Address, Customer name,Customer mobile number,Balance payment

        var dataCopy2 =
        "Booking ID: " + booking_id +
        "\nBooking Date: " + booking_date +
        "\nCab Type: " + cab_type +
        "\nPickup Time: " + pickup_time +
        "\nPickup Address: " + pickup_address +
        "\nDropoff Address: " + drop_off_address +
        "\nCustomer Name: " + customer_name +
        "\nCustomer Mobile: " + customer_mobile +
        "\nBalance Payment: " + balance_payment;

        // Use modern Clipboard API
        navigator.clipboard.writeText(dataCopy2).then(function() {
            console.log("Customer details copied to clipboard!");
        }).catch(function(error) {
            console.error("Failed to copy text: ", error);
        });

    }

    function copyConfirmedDriver()
    {
        var dataCopy =
        "Dear Traveller,\nYour cab booking has been successfully confirmed.\nDate: " + booking_date +
        "\nBooking ID: " + booking_id +
        "\nCar Type: "  + cab_type +
        "\nPickup Time: " + pickup_time +
        "\nPickup Location: " + pickup_address +
        "\nDrop-off Location: " + drop_off_address +
        "\nTotal Fare: " + total_amount + " (After 30 mins, ₹200/hour) " +
        "\nAdvance Paid: " + part_payment +
        "\nBalance Payable to Driver: " + balance_payment +
        "\nYour Trip OTP: " + otp + " (Please share this with your driver at the time of pickup)"+
        "\nYou will receive your driver’s details 1 hour before the trip. \nWarm regards, \nSeema Cabs Goa \nwww.seemacabsgoa.com";

        navigator.clipboard.writeText(dataCopy).then(function() {
            console.log("Confirmed Driver details copied to clipboard!");
        }).catch(function(error) {
            console.error("Failed to copy text: ", error);
        });
    }
</script>
