<!-- Dashboard -->
<li class="pc-item pc-hasmenu">
    <a href="{{ route('admin.dashboard') }}" class="pc-link"><span class="pc-micon"><i
                class="ph-duotone ph-squares-four"></i></span><span class="pc-mtext">Dashboard</span></a>
</li>

<!-- Cab Management -->
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-car"></i></span>
        <span class="pc-mtext">Cab Management</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.cabs.create') }}">Add Cab Details</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.cabs.index') }}">View Cab Details</a></li>
        {{-- <li class="pc-item"><a class="pc-link" href="{{asset('/cab-management/approve-drivers')}}">Driver Details</a></li> --}}
        {{-- <li class="pc-item"><a class="pc-link" href="{{ route('admin.cabs.surgePrice') }}">Surge Pricing</a></li> --}}
        {{-- <li class="pc-item"><a class="pc-link" href="{{asset('/cab-management/deleted-drivers')}}">Deleted Drivers</a></li> --}}
    </ul>
</li>
@if (Auth()->user()->type == App\Enums\Type::ADMIN)
    <!-- Customer Management -->
    <li class="pc-item pc-hasmenu">
        <a href="#!" class="pc-link">
            <span class="pc-micon"><i class="ph-duotone ph-address-book"></i></span>
            <span class="pc-mtext">Customer Management</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.customers.index') }}">View Customers</a>
            </li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.customers.deleteUserDetails') }}">Deleted
                    Customers</a></li>
            <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/customer-management/customer-sos') }}">SOS</a></li> -->
        </ul>
    </li>
@endif

<!-- Trip Management -->
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-map-trifold"></i></span>
        <span class="pc-mtext">Trip Management</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item {{ request()->query('status') ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.trips.index') }}">Booking Details</a></li>
        <li class="pc-item {{ request()->query('status') === 'completed' ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.trips.index',['status'=>'completed']) }}">Completed trips</a></li>
        <li class="pc-item {{ request()->query('status') === 'cancel' ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.trips.index',['status'=> 'cancel']) }}">Cancelled Trips</a></li>
        <li class="pc-item {{ request()->query('status') === 'refund' ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.trips.index',['status'=> 'refund']) }}">Cancel & Refund</a></li>
        <li class="pc-item {{ request()->query('status') === 'no_show' ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.trips.index',['status'=> 'no_show']) }}">No-show Trips</a></li>
        {{-- <li class="pc-item"><a class="pc-link" href="{{asset('/trip-management/driver-ratings')}}">Driver Ratings</a>
    </li> --}}
        <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/trip-management/driver-status') }}">Driver Status</a></li> -->
        <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/trip-management/in-transit-trip') }}">In-Transit</a></li> -->
        <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/trip-management/cancelled-trip') }}">Cancelled</a></li> -->
        <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/trip-management/completed-trip') }}">Completed</a></li> -->
    </ul>
</li>
<!-- Accounting -->
{{-- <li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-table"></i></span>
        <span class="pc-mtext">Accounting</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{asset('/accounting/customer-invoices')}}">Customer Invoices</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ asset('/accounting/ad-invoices') }}">Ad Invoices</a></li>
        <li class="pc-item"><a class="pc-link" href="{{asset('/accounting/tds-invoices')}}">TDS Invoices</a></li>
    </ul>
</li> --}}
@if (Auth()->user()->type == App\Enums\Type::ADMIN)
    <li class="pc-item {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"><a class="pc-link" href="{{ route('admin.invoices.index') }}"><span class="pc-micon"><i class="ph-duotone ph-receipt"></i></span><span class="pc-mtext">Invoices</span></a></li>
@endif
@if (Auth()->user()->type == App\Enums\Type::ADMIN)
    <!-- Advertisements -->
    <li class="pc-item pc-hasmenu">
        <a href="#!" class="pc-link">
            <span class="pc-micon"><i class="ph-duotone ph-megaphone"></i></span>
            <span class="pc-mtext">Advertisements</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/add-advertisers') }}">Add Advertisers</a></li> -->
            @php($adReviewCount = \App\Models\AdCampaign::where('status', \App\Models\AdCampaign::IN_REVIEW)->count())
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.advertisements.selfServe.index') }}">Ad Review Queue
                    @if ($adReviewCount)<span class="badge bg-danger ms-1">{{ $adReviewCount }}</span>@endif</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.advertisements.selfServe.placements') }}">Placements &amp; Pricing</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.advertisements.selfServe.advertisers') }}">Advertisers</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.advertisements.index') }}">Submit Ads</a></li>
            {{-- <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/pending-ads') }}">Pending Ads</a>
            </li> --}}
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.advertisements.activeAdds.index') }}">Active
                    Ads</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.advertisements.expiredAdds.index') }}">Expired
                    Ads</a>
            </li>
            <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/ad-invoice') }}">Ad Invoices</a></li> -->
            <!-- <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/home-screen-ad') }}">Home Screen Ads</a></li>
    <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/book-ride-screen-ad') }}">Book Ride Screen
        Ads</a></li>
    <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/finding-taxi-screen-ad') }}">Finding Taxi
        Screen Ads</a></li>
    <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/booking-conf-screen-ad') }}">Booking Conf.
        Screen Ads</a></li>
    <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/driver-screen-ad') }}">Driver Screen Ads</a>
    </li>
    <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/rating-screen-ad') }}">Rating Screen Ads</a>
    </li>
    <li class="pc-item"><a class="pc-link" href="{{ asset('/advertisements/account-ad') }}">Account Ads</a></li> -->
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.advertisements.screenPrice.index') }}">Ad
                    Pricing</a>
            </li>
        </ul>
    </li>
@endif
<!-- Advertisements -->
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-notepad"></i></span>
        <span class="pc-mtext">Content Management</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'about-us') }}">About Us</a></li>
        <li class="pc-item"><a class="pc-link"
                href="{{ route('admin.pages.index', 'inclusion-exclusions') }}">Inclusions &
                Exclusions</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'read-before-you-book') }}">Read
                Before You Book</a>
        </li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'privacy-policy') }}">Privacy
                Policy</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'user-agreement') }}">User
                Agreement</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'terms-of-service') }}">Terms of
                Service</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'driver-agreement') }}">Driver
                Agreement</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'advertiser-agreement') }}">Advertiser Agreement</a>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.pages.index', 'cancel-refund') }}">Cancel &
                Refund Policy</a>
        </li>
    </ul>
</li>

<!-- Discount Coupon -->
{{-- <li class="pc-item pc-hasmenu">
    <a href="{{ route('admin.coupons.index') }}" class="pc-link"><span class="pc-micon"><i
                class="ph-duotone ph-ticket"></i></span><span class="pc-mtext">Discount Coupons</span></a>
</li> --}}

<!-- Marketing -->
{{-- <li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-chat-text"></i></span>
        <span class="pc-mtext">Marketing</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="{{ asset('/marketing/push-notifications') }}">Push
                Notifications</a>
        </li>
</li>
</ul>
</li> --}}

<!-- Settings -->
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-gear-six"></i></span>
        <span class="pc-mtext">Settings</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @if (Auth()->user()->type == App\Enums\Type::ADMIN)
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.appUpdate') }}">App Update</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.logos.logo') }}">Logo</a></li>
        @endif
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.city.index') }}">Manage Cities/Cab
                Zone</a></li>
        {{-- <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.baseFare.index') }}">Base Fare</a></li> --}}
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.cabRate.index') }}">Cab Rates</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.cabs.surgePrice') }}">Surge Pricing</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.sightseeingPackages.index') }}">Sight Seeing Packages</a></li>
        @if (Auth()->user()->type == App\Enums\Type::ADMIN)
            <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.fleetOperators.index') }}">Fleet
                Operator</a></li>
        @endif
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.cab.index') }}">Cab Models</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.advanceBooking') }}">Booking &
                Cancellation</a></li>
        @if (Auth()->user()->type == App\Enums\Type::ADMIN)
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.companyDetails.index') }}">SaaS Company
                Details</a></li>
        {{-- <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.sosNumbers') }}">SOS</a></li> --}}
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.pricing') }}">Pricing</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.gst') }}">GST</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.businessProfiles') }}">Business Profiles</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.plateFormFee') }}">Commissions</a>
        </li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.paymentGateway.index') }}">Payment
                Gateway
                Keys</a>
        </li>
        @endif
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.smtpCred') }}">SMTP</a></li>
        {{-- @if (Auth()->user()->type == App\Enums\Type::ADMIN) --}}
        {{-- <li class="pc-item"><a class="pc-link" href="{{ route('admin.setting.index') }}">General Settings</a></li> --}}
        {{-- @endif --}}
    </ul>
</li>

<!-- Reports -->
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon"><i class="ph-duotone ph-newspaper"></i></span>
        <span class="pc-mtext">Reports</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">

        {{-- <li class="pc-item"><a class="pc-link" href="{{ asset('/reports/aggregator-commission') }}">Aggregator
                Commission</a></li> --}}
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.report.index') }}">Fleet Operator
                Payments</a></li>
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.report.reconcileCancelledPayments') }}">Reconcile
                Cancelled Payments</a></li>
        @if (Auth()->user()->type == App\Enums\Type::ADMIN)
        <li class="pc-item"><a class="pc-link" href="{{ route('admin.report.feeTransfers') }}">OfinIT Fee Transfers</a></li>
        @endif
        {{-- <li class="pc-item"><a class="pc-link" href="{{ asset('/reports/driver-payments') }}">Driver Payments</a>
        </li> --}}
        <!-- <li class="pc-item"><a class="pc-link" href="#">Trip Payments</a></li> -->
    </ul>
</li>

<!-- Notifications -->
 <li class="pc-item"><a class="pc-link" href="{{ route('admin.notifications.index') }}"><span class="pc-micon"><i class="ph-duotone ph-bell"></i></span>Notifications</a></li>
