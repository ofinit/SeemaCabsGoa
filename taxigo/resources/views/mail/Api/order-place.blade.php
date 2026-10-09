<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Customer Invoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Public Sans", sans-serif;
        }

        .email-body {
            padding: 24px;
            max-width: 740px;
            width: 100%;
            margin: auto;
            border: 1px solid #DBE0E5;
        }

        .email-header {
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }

        .email-header .logo {
            max-width: 85px;
            width: 100%;
        }

        .email-header h5 {
            color: #1D2630;
            text-align: center;
            font-weight: 700;
            font-size: 16px;
            line-height: 110%;
            margin-bottom: 8px;
        }

        .email-header .center-td p {
            color: #39465F;
            font-weight: 400;
            font-size: 14px;
            line-height: 110%;
            text-align: center;
        }

        .email-header .last-td p {
            color: #39465F;
            font-weight: 400;
            font-size: 14px;
            line-height: 110%;
            text-align: right;
            margin-bottom: 8px;
        }

        .email-header div {
            width: 20%;
        }

        .email-header .center-td {
            width: 60%;
        }

        .Customer-detail,
        .booking-detail {
            border: 1px solid #DBE0E5;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .Customer-detail h5,
        .booking-detail h5 {
            color: #1D2630;
            font-weight: 700;
            font-size: 14px;
            line-height: 110%;
            margin-bottom: 16px;
        }

        .Customer-detail p,
        .booking-detail p {
            margin-bottom: 6px;
            color: #39465F;
            font-weight: 400;
            font-size: 14px;
            line-height: 110%;
        }

        .amount-table,
        .amount-table td {
            border-collapse: collapse;
        }

        .amount-table {
            margin-bottom: 20px;
            width: 100%;
        }

        .amount-table thead td {
            padding: 16px 12px;
            color: #1D2630;
            font-weight: 700;
            font-size: 14px;
            line-height: 110%;
            border-top: 1px solid #DBE0E5;
            border-bottom: 1px solid #DBE0E5;
            background: #F4F7FA80;
        }

        .amount-table tbody td {
            padding: 16px 12px;
            font-weight: 400;
            font-size: 14px;
            line-height: 110%;
            color: #39465F;
            border-bottom: 1px solid #DBE0E5;
        }

        .amount-table tbody tr:last-child td {
            font-weight: 700;
        }

        .amount-total {
            max-width: 400px;
            margin-left: auto;
        }

        .amount-total div {
            display: flex;
            align-items: center;
            margin-right: 12px;
        }

        .amount-total div p {
            color: #39465F;
            font-weight: 400;
            font-size: 14px;
            line-height: 110%;
            margin-bottom: 10px;
        }

        .inclusions-exclusions {
            margin-top: 42px;
        }

        .inclusions-exclusions div {
            border: 1px solid #DBE0E5;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .inclusions-exclusions div h5 {
            margin-bottom: 16px;
            color: #1D2630;
            font-weight: 700;
            font-size: 14px;
            line-height: 110%;
        }

        .inclusions-exclusions td:first-child {
            padding-right: 10px;
        }

        .inclusions-exclusions td:last-child {
            padding-left: 10px;
        }

        .inclusions-exclusions div ul li {
            margin-bottom: 8px;
            color: #39465F;
            font-weight: 400;
            font-size: 12px;
            line-height: 130%;
            margin-left: 16px;
        }

        .contact {
            padding: 16px 16px 10px;
            border: 1px solid #DBE0E5;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .contact p {
            margin-bottom: 6px;
            color: #39465F;
            font-weight: 400;
            font-size: 14px;
            line-height: 110%;
            text-align: center;
        }

        .footer span {
            margin: 0 auto 2px;
            text-align: center;
            color: #1A1E23;
            font-family: "Nunito Sans", sans-serif;
            font-weight: 400;
            font-size: 8px;
            display: block;
        }

        .footer .logo {
            width: 100px;
            margin: 0 auto 12px;
            display: block;
        }

        .footer h5 {
            display: flex;
            width: fit-content;
            align-items: center;
            gap: 5px;
            color: #000000;
            font-family: "Nunito Sans", sans-serif;
            font-weight: 700;
            font-size: 16px;
            text-align: center;
            margin: 0 auto 4px;
        }

        .footer p {
            color: #1A1E23;
            font-family: "Nunito Sans", sans-serif;
            font-weight: 400;
            font-size: 14px;
            text-align: center;
        }

        @media (max-width: 576px) {
            .email-header {
                display: block;
            }

            .email-header .center-td {
                margin-bottom: 12px;
            }

            .email-header .center-td {
                margin-bottom: 20px;
                width: 100% !important;
            }

            .email-header div {
                width: 100% !important;
            }

            .email-header .logo {
                margin: auto;
                display: block;
                margin-bottom: 12px;
            }

            .email-body {
                padding: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="email-body">
        <div class="email-header">
            <div>
                <img src="{{ getInvoiceLogo() }}" alt="Logo" class="logo">
            </div>
            <div class="center-td">
                <h5>Booking Confirmation</h5>
                <p>Thank you for booking with Seema Cab!</p>
            </div>
            <div class="last-td">
                <p>Booking Date</p>
                <p>{{ \Carbon\Carbon::parse($bookingDetails->pickup_date)->format('M d, Y') ?? '' }}</p>
            </div>
        </div>
        <div class="email-detail">
            <div class="Customer-detail">
                <h5>{{ $bookingDetails->user ? $bookingDetails->user->name : 'N/A' }}</h5>
                {{-- <p>Address</p> --}}
                <p>Mobile : {{ $bookingDetails->user ? $bookingDetails->user->phone_number : 'N/A' }}</p>
                <p>Email ID : {{ $bookingDetails->user ? $bookingDetails->user->email : 'N/A' }}</p>
            </div>
            <div class="booking-detail">
                @php
                    $pickFrom = '';
                    $drop = '';
                    if ($bookingDetails->air_port_drop == App\Enums\Type::AIRPORT_PICKUP) {
                        if ($bookingDetails->pickup_from == App\Enums\Type::AIRPORT_ONE_ID) {
                            $pickFrom = App\Enums\Type::AIRPORT_ONE;
                        } elseif ($bookingDetails->pickup_from == App\Enums\Type::AIRPORT_TWO_ID) {
                            $pickFrom = App\Enums\Type::AIRPORT_TWO;
                        } else {
                            $pickFrom = $bookingDetails->getPickupFrom ? $bookingDetails->getPickupFrom->name : 'N/A';
                        }
                    } else {
                        $pickFrom = $bookingDetails->getPickupFrom ? $bookingDetails->getPickupFrom->name : 'N/A';
                    }

                    if ($bookingDetails->air_port_drop == App\Enums\Type::AIRPORT_DROP) {
                        if ($bookingDetails->drop_to == App\Enums\Type::AIRPORT_ONE_ID) {
                            $drop = App\Enums\Type::AIRPORT_ONE;
                        } elseif ($bookingDetails->drop_to == App\Enums\Type::AIRPORT_TWO_ID) {
                            $drop = App\Enums\Type::AIRPORT_TWO;
                        } else {
                            $drop = $bookingDetails->getDropTo ? $bookingDetails->getDropTo->name : '--';
                        }
                    } else {
                        $drop = $bookingDetails->getDropTo ? $bookingDetails->getDropTo->name : '--';
                    }
                @endphp
                <h5>Booking Details</h5>
                <p>Booking ID : {{ $bookingDetails->booking_id ?? '' }}</p>
                <p>Type of Taxi : {{ getCabType($bookingDetails->cab_type) }}</p>
                <p>Trip Details : {{ $pickFrom }} to {{ $drop }}</p>
                <p>Pickup Date :{{ \Carbon\Carbon::parse($bookingDetails->pickup_date)->format('d M, Y') ?? '' }}</p>
                <p>Pickup Time : {{ \Carbon\Carbon::parse($bookingDetails->pickup_time)->format('h:i A') ?? '' }}</p>
                <p>Pickup Location : {{ $bookingDetails->pickup_address ?? $pickFrom }}</p>
                <p>Destination Address : {{ $bookingDetails->drop_of_address ?? $drop }}</p>
            </div>
        </div>
        <table class="amount-table">
            <thead>
                <tr>
                    <td>TRIP BREAKUP</td>
                    <td style="text-align: right;">AMOUNT</td>
                </tr>
            </thead>
            <tbody>
                {{-- The internal markup is inside the fare; only real GST is shown separately. --}}
                <tr>
                    <td>Fare (all-inclusive){{ $bookingDetails->cabRate ? ', ' . $bookingDetails->cabRate->base_km . ' Kms' : '' }}</td>
                    <td style="text-align: right;">{{ getCurrencySign() . ((int) $bookingDetails->pricing_version >= 2 ? $bookingDetails->base_fare : $bookingDetails->total_payment) }}</td>
                </tr>
                @if ($bookingDetails->hasGst())
                    <tr>
                        <td>GST @ {{ (float) $bookingDetails->gst_rate }}% (CGST {{ getCurrencySign() . $bookingDetails->cgst_amount }} + SGST {{ getCurrencySign() . $bookingDetails->sgst_amount }})</td>
                        <td style="text-align: right;">{{ getCurrencySign() . $bookingDetails->tax_amount }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Total Amount</td>
                    <td style="text-align: right;">{{ getCurrencySign() . $bookingDetails->total_payment ?? 0.0 }}</td>
                </tr>
            </tbody>
        </table>
        <div class="amount-total">
            <div>
                <p>Part Payment</p>
                <p style="font-weight: 600; padding-left: 12px; margin-left: auto;">
                    {{ getCurrencySign() . $bookingDetails->part_payment ?? 0.0 }}</p>
            </div>
            <div>
                <p>Pay Rest to The Driver</p>
                <p style="font-weight: 600; padding-left: 12px; margin-left: auto;">
                    {{ getCurrencySign() . $bookingDetails->cash_with_driver ?? 0.0 }}</p>
            </div>
        </div>
        @php
            $inclusion = App\Models\CrmPages::where('type', 'inclusion')->first();
            $exclusions = App\Models\CrmPages::where('type', 'exclusions')->first();
        @endphp
        <div class="inclusions-exclusions">
            <div class="first">
                <h5>Inclusions</h5>
                {!! $inclusion->content ?? 'N/A' !!}
            </div>
            <div>
                <h5>Exclusions</h5>
                {!! $exclusions->content ?? 'N/A' !!}
            </div>
        </div>
        <div class="contact">
            <p>{{ App\Enums\CustomerDetailsEnum::FLEET_OPERATOR_NAME }},
                {{ App\Enums\CustomerDetailsEnum::FLEET_OPERATOR_ADDRESS }}</p>
            <p>Tel: {{ App\Enums\CustomerDetailsEnum::FLEET_OPERATOR_PHONE_ONE }},
                {{ App\Enums\CustomerDetailsEnum::FLEET_OPERATOR_PHONE_TWO }} | Web: www.seemacabsgoa.com</p>
        </div>
        <div class="footer">
            <span>Powered by</span>
            <a href="https://taxigo.software/">
                <img src="{{ asset('/assets/mail/footer-logo.png') }}" alt="Logo" class="logo">
            </a>
            <h5>Made with <img src="{{ asset('/assets/mail/hart-icon.png') }}" alt="icon"> in Goa by</h5>
            <p>OfinIT Solutions Private Limited</p>
        </div>
    </div>
</body>

</html>
