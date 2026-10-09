<?php

namespace App\Http\Controllers\Customer;

use App\Enums\Type;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\BookingController as ApiBookingController;
use App\Http\Controllers\Api\CabController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SightSeeingPackageController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\BookingDetail;
use App\Models\City;
use App\Models\Country;
use App\Models\Environment;
use App\Models\SightSeeingPackages;
use App\Models\State;
use App\Services\Ads\AdServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function splash()
    {
        $next = Auth::guard('customer')->check()
            ? route('customer.home')
            : route('customer.login');

        return view('customer.splash', ['next' => $next]);
    }

    public function home(Request $request)
    {
        // Home carousel: ads booked on the Home screen (screen 1) only.
        $ads = AdServer::forScreen(AdServer::SCREEN_HOME)
            ->map(fn ($ad) => AdServer::payload($ad, AdServer::SCREEN_HOME, 'pwa'))
            ->values();

        $packages =SightSeeingPackages::with('packageImages')->get()->map(function ($p) {
            $images = [];
            foreach ($p->packageImages as $val) {
                $images[] = getFileUrl($val->image);
            }
            return [
                'id' => $p->id,
                'title' => $p->title,
                'start_time' => $p->start_time ? \Carbon\Carbon::parse($p->start_time)->format('h:i A') : '',
                'end_time' => $p->end_time ? \Carbon\Carbon::parse($p->end_time)->format('h:i A') : '',
                'location' => $p->location,
                'description' => $p->description,
                'terms_and_condition' => $p->terms_and_condition,
                'images' => $images,
            ];
        });

        $currentBooking = null;
        try {
            $user = Auth::guard('customer')->user();
            if ($user) {
                $b = BookingDetail::without(['getSosDetails', 'user', 'cabRate', 'getPickupFrom', 'getDropTo'])
                    ->with([
                        'assignDriver' => fn($q) => $q->select('id', 'driver_name', 'driver_mobile'),
                        'getCabDetails' => fn($q) => $q->without([
                            'getCabModelDetails', 'getDriverDetails', 'getColorDetails',
                            'getCityDetails', 'getFleetOperatorDetails', 'getWaitingChargeDetails',
                            'getNoOfKMsDetails', 'getBaseFareDetails', 'getAdditionalChargeDetails',
                            'getAssignedDriverDetails'
                        ])->select('id', 'number', 'model')
                    ])
                    ->where('customer_id', $user->id)
                    ->whereNull('customer_deleted_at')
                    ->whereNotNull('assigned_driver_id')
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($b) {
                    $cab_type = match((int) $b->cab_type) {
                        Type::CAB_SEDAN_ID => Type::CAB_SEDAN,
                        Type::CAB_SUV_ID => Type::CAB_SUV,
                        default => Type::CAB_HATCHBACK,
                    };
                    $b->cab_name = getCabType($cab_type);
                    $currentBooking = $b;
                }
            }
        } catch (\Throwable $e) {}

        return view('customer.home', compact('ads', 'packages', 'currentBooking'));
    }

    public function book(Request $request)
    {
        $cities = City::select('id', 'name')->orderBy('name')->get();
        $airports = [
            ['id' => '1', 'name' => 'Dabolim Goa Airport (GOI)'],
            ['id' => '2', 'name' => 'Manohar International Airport (GOX)'],
        ];

        $packages = SightSeeingPackages::with('packageImages')->get()->map(function ($p) {
            $images = [];
            foreach ($p->packageImages as $val) {
                $images[] = '/storage/' . ltrim($val->image, '/');
            }
            return [
                'id' => $p->id,
                'title' => $p->title,
                'start_time' => $p->start_time ? \Carbon\Carbon::parse($p->start_time)->format('h:i A') : '',
                'end_time' => $p->end_time ? \Carbon\Carbon::parse($p->end_time)->format('h:i A') : '',
                'location' => $p->location,
                'description' => $p->description,
                'terms_and_condition' => $p->terms_and_condition,
                'images' => $images,
            ];
        });

        return view('customer.book.form', [
            'editBookingId' => $request->query('edit'),
            'cities' => $cities,
            'airports' => $airports,
            'packages' => $packages,
        ]);
    }

    public function results(Request $request)
    {
        $cabs = [];
        $surge = false;
        $error = '';
        try {
            $cabRes = app(CabController::class)->getCabList($request)->getData(true);
            $cabs = $cabRes['data'] ?? [];
            $surge = collect($cabs)->some(fn ($c) => (float) ($c['surge_price'] ?? 0) > 0);
            if (empty($cabs)) {
                $error = 'No cabs available for this route right now.';
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        $airports = [
            '1' => 'Dabolim Goa Airport (GOI)',
            '2' => 'Manohar International Airport (GOX)',
        ];
        $cities = City::select('id', 'name')->get();

        $fromId = (string) $request->query('from', '');
        $toId = (string) $request->query('to', '');
        $isAirport = $request->query('trip') === 'airport';
        $airportDirection = $request->query('air_port_drop');

        $fromName = ($isAirport && $airportDirection == 1)
            ? ($airports[$fromId] ?? $fromId)
            : ($cities->firstWhere('id', $fromId)->name ?? $fromId);

        $toName = ($isAirport && $airportDirection == 2)
            ? ($airports[$toId] ?? $toId)
            : ($cities->firstWhere('id', $toId)->name ?? $toId);

        return view('customer.book.results', compact('cabs', 'surge', 'error', 'fromName', 'toName'));
    }

    public function finding(Request $request)
    {
        // Finding-a-taxi: top (3) or the large card (10) that replaces it, bottom (8),
        // and an optional full-screen takeover (11). Rotation picks one of each at random.
        $pick = fn (int $screen) => AdServer::onScreen(AdServer::live(), $screen)->inRandomOrder()->first();
        $large = $pick(AdServer::SCREEN_FINDING_LARGE);
        $top = $large ? null : AdServer::forScreen(AdServer::SCREEN_FINDING_TOP, 5)->shuffle()->first();
        $bottom = AdServer::forScreen(AdServer::SCREEN_FINDING_BOTTOM, 5)->shuffle()->first();
        $full = $pick(AdServer::SCREEN_FINDING_FULL);

        $topAd = $large ? AdServer::payload($large, AdServer::SCREEN_FINDING_LARGE, 'pwa')
            : ($top ? AdServer::payload($top, AdServer::SCREEN_FINDING_TOP, 'pwa') : null);
        $bottomAd = $bottom && $bottom->id !== ($large ?? $top)?->id ? AdServer::payload($bottom, AdServer::SCREEN_FINDING_BOTTOM, 'pwa') : null;
        $fullAd = $full ? AdServer::payload($full, AdServer::SCREEN_FINDING_FULL, 'pwa') : null;

        return view('customer.book.finding', compact('topAd', 'bottomAd', 'fullAd'));
    }

    public function review()
    {
        $user = Auth::guard('customer')->user();
        $userData = $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'gender' => $user->gender,
            'country_id' => $user->country_id,
            'state_id' => $user->state_id,
            'gstin' => $user->gstin,
            'gst_legal_name' => $user->gst_legal_name,
            'gst_billing_address' => $user->gst_billing_address,
        ] :[];

        $countries = Country::select('id', 'name')->orderBy('name')->get();
        $states = !empty($user->country_id)
            ? State::where('country_id', $user->country_id)->select('id', 'name')->orderBy('name')->get()
            : [];

        $paymentKey = Environment::where('title', 'paymentkey')->value('value') ?? '';

        return view('customer.book.review', compact('userData', 'countries', 'states', 'paymentKey'));
    }

    public function trip(string $booking)
    {
        $details = null;
        $error = '';
        try {
            $b = BookingDetail::without(['getSosDetails', 'user', 'cabRate'])
                ->with([
                    'assignDriver' => fn($q) => $q->select('id', 'driver_name', 'driver_mobile'),
                    'getCabDetails' => fn($q) => $q->without([
                        'getCabModelDetails', 'getDriverDetails', 'getColorDetails',
                        'getCityDetails', 'getFleetOperatorDetails', 'getWaitingChargeDetails',
                        'getNoOfKMsDetails', 'getBaseFareDetails', 'getAdditionalChargeDetails',
                        'getAssignedDriverDetails'
                    ])->select('id', 'number', 'model'),
                    'getPickupFrom' => fn($q) => $q->select('id', 'name'),
                    'getDropTo' => fn($q) => $q->select('id', 'name'),
                ])
                ->where('id', $booking)
                ->where('customer_id', Auth::guard('customer')->id())
                ->first();

            if ($b) {
                $b->ac = 'AC';
                $cab_type = match((int) $b->cab_type) {
                    Type::CAB_SEDAN_ID => Type::CAB_SEDAN,
                    Type::CAB_SUV_ID => Type::CAB_SUV,
                    default => Type::CAB_HATCHBACK,
                };
                $b->cab_type = $cab_type;
                $b->cab_name = getCabType($cab_type);
                if ($b->cab_name === 'Sedan') {
                    $b->image = asset('cabs/sedan.png');
                    $b->model = 'Dzire, Etios or similar';
                    $b->baggage = '3 Baggage';
                    $b->seat = 4;
                } elseif ($b->cab_name === 'SUV') {
                    $b->image = asset('cabs/suv.png');
                    $b->model = 'Xylo, Ertiga or similar';
                    $b->baggage = '3 Baggage';
                    $b->seat = 6;
                } else {
                    $b->image = asset('cabs/hatchback.png');
                    $b->model = 'Baleno, Swift or similar';
                    $b->baggage = '2 Baggage';
                    $b->seat = 4;
                }
                $details = $b;
            } else {
                $error = 'Booking not found.';
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        $invoices = $details
            ? \App\Models\Invoice::where('booking_id', $details->id)->where('status', \App\Models\Invoice::ISSUED)->orderBy('id')->get()
            : collect();

        return view('customer.trip.show', [
            'bookingId' => $booking,
            'booking' => $details,
            'error' => $error,
            'invoices' => $invoices,
        ]);
    }

    public function sos(string $booking)
    {
        $fleetPhone = Environment::where('title', 'fleet_operator_phone_one')->value('value') ?? '';

        return view('customer.trip.sos', [
            'bookingId' => $booking,
            'fleetPhone' => $fleetPhone,
        ]);
    }

    public function rides()
    {
        $rides = [];
        try {
            $user = Auth::guard('customer')->user();
            if ($user) {
                $bookings = BookingDetail::without(['getSosDetails', 'user', 'cabRate'])
                    ->with([
                        'assignDriver' => fn($q) => $q->select('id', 'driver_name', 'driver_mobile'),
                        'getCabDetails' => fn($q) => $q->without([
                            'getCabModelDetails', 'getDriverDetails', 'getColorDetails',
                            'getCityDetails', 'getFleetOperatorDetails', 'getWaitingChargeDetails',
                            'getNoOfKMsDetails', 'getBaseFareDetails', 'getAdditionalChargeDetails',
                            'getAssignedDriverDetails'
                        ])->select('id', 'number', 'model'),
                        'getPickupFrom' => fn($q) => $q->select('id', 'name'),
                        'getDropTo' => fn($q) => $q->select('id', 'name'),
                    ])
                    ->where('customer_id', $user->id)
                    ->whereNull('customer_deleted_at')
                    ->orderBy('id', 'desc')
                    ->get();

                foreach ($bookings as $booking) {
                    $booking->ac = 'AC';
                    $cab_type = match((int) $booking->cab_type) {
                        Type::CAB_SEDAN_ID => Type::CAB_SEDAN,
                        Type::CAB_SUV_ID => Type::CAB_SUV,
                        default => Type::CAB_HATCHBACK,
                    };
                    $booking->cab_type = $cab_type;
                    $booking->cab_name = getCabType($cab_type);
                    if ($booking->cab_name === 'Sedan') {
                        $booking->image = asset('cabs/sedan.png');
                        $booking->model = 'Dzire, Etios or similar';
                        $booking->baggage = '3 Baggage';
                        $booking->seat = 4;
                    } elseif ($booking->cab_name === 'SUV') {
                        $booking->image = asset('cabs/suv.png');
                        $booking->model = 'Xylo, Ertiga or similar';
                        $booking->baggage = '3 Baggage';
                        $booking->seat = 6;
                    } else {
                        $booking->image = asset('cabs/hatchback.png');
                        $booking->model = 'Baleno, Swift or similar';
                        $booking->baggage = '2 Baggage';
                        $booking->seat = 4;
                    }
                    $booking->driver = $booking->assignDriver;
                }
                $rides = $bookings;
            }
        } catch (\Throwable $e) {}

        return view('customer.rides.index', compact('rides'));
    }

    public function account()
    {
        $user = Auth::guard('customer')->user();
        $userData = $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'gender' => $user->gender,
            'country_id' => $user->country_id,
            'state_id' => $user->state_id,
            'country_name' => optional($user->getCountryDetails)->name ?? '',
            'state_name' => optional($user->getStateDetails)->name ?? '',
        ] : [];

        $countries = Country::select('id', 'name')->orderBy('name')->get();
        $states = !empty($user->country_id)
            ? State::where('country_id', $user->country_id)->select('id', 'name')->orderBy('name')->get()
            : [];

        return view('customer.account.show', compact('userData', 'countries', 'states'));
    }

    public function notifications(Request $request)
    {
        $items = [];
        try {
            $user = Auth::guard('customer')->user();
            if ($user) {
                $items = \App\Models\Notification::where('user_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->get(['id', 'title', 'text', 'created_at']);
            }
        } catch (\Throwable $e) {}

        return view('customer.notifications.index', compact('items'));
    }

    public function discoverPackage(string $package, Request $request)
    {
        $p = SightSeeingPackages::with('packageImages')->where('id', (int) $package)->first();
        $pkg = null;
        if ($p) {
            $images = [];
            foreach ($p->packageImages as $val) {
                $images[] = getFileUrl($val->image);
            }
            $pkg = [
                'id' => $p->id,
                'title' => $p->title,
                'start_time' => $p->start_time ? \Carbon\Carbon::parse($p->start_time)->format('h:i A') : '',
                'end_time' => $p->end_time ? \Carbon\Carbon::parse($p->end_time)->format('h:i A') : '',
                'location' => $p->location,
                'description' => $p->description,
                'terms_and_condition' => $p->terms_and_condition,
                'images' => $images,
            ];
        }

        $cities = City::select('id', 'name')->orderBy('name')->get();

        $user = Auth::guard('customer')->user();
        $accountData = $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'gender' => $user->gender,
            'country_id' => $user->country_id,
            'state_id' => $user->state_id,
            'gstin' => $user->gstin,
            'gst_legal_name' => $user->gst_legal_name,
            'gst_billing_address' => $user->gst_billing_address,
        ] :null;

        return view('customer.discover.show', [
            'packageId' => $package,
            'pkg' => $pkg,
            'cities' => $cities,
            'accountData' => $accountData,
        ]);
    }

    /*
    |----------------------------------------------------------------------
    | JSON passthroughs — proxy the existing Api\* controllers in-process
    | so business logic (settings, locations, ads, notifications) is never
    | duplicated. Each of these already returns a JsonResponse matching the
    | {status, data, message} envelope the Alpine components expect.
    |----------------------------------------------------------------------
    */

    public function settingsJson()
    {
        return app(SettingController::class)->settingDetails();
    }

    public function countriesJson()
    {
        return app(LocationController::class)->countryList();
    }

    public function citiesJson()
    {
        return app(LocationController::class)->getCityList();
    }

    public function statesJson(int $country)
    {
        return app(LocationController::class)->getStateList($country);
    }

    public function advertisementsJson(Request $request)
    {
        return app(AdvertisementController::class)->index($request);
    }

    public function advertisementClick(Request $request)
    {
        return app(AdvertisementController::class)->manageClick($request);
    }

    public function notificationsJson(Request $request)
    {
        return app(NotificationController::class)->list($request);
    }

    public function deleteNotification(int $id)
    {
        return app(NotificationController::class)->delete($id);
    }

    public function pageContentJson(string $slug)
    {
        return app(\App\Http\Controllers\Api\PageContentController::class)->getPageContent($slug);
    }
}
