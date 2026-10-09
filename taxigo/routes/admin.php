<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ActiveAddController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BaseFareController;
use App\Http\Controllers\Admin\CabManagementController;
use App\Http\Controllers\Admin\CabRateController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\ContentManagementController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpiredController;
use App\Http\Controllers\Admin\Feature\ColorController;
use App\Http\Controllers\Admin\Feature\ModelController;
use App\Http\Controllers\Admin\FleetOperatorController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RazorpayController;
use App\Http\Controllers\Admin\BillingSettingsController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\Reports\FleetOperatorPaymentController;
use App\Http\Controllers\Admin\Reports\PaymentReconciliationController;
use App\Http\Controllers\Admin\ScreenPriceController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TripManagementController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SightSeeingPackageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Auth
Route::get('login', [AuthController::class, 'login'])->name('admin.login');
Route::post('login', [AuthController::class, 'doLogin'])->name('admin.doLogin');
Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::get('security-check', [AuthController::class, 'twoFaSecurityCheck'])->name('admin.twoFaSecurityCheck');
Route::post('authentication-check', [AuthController::class, 'authentication'])->name('admin.security.authentication');

// Password Reset
Route::get('reset', [AuthController::class, 'reset'])->name('admin.reset');
Route::post('reset', [AuthController::class, 'resetPassword'])->name('admin.reset.password');
Route::get('password/{id}', [AuthController::class, 'password'])->name('admin.password');
Route::post('password-update', [AuthController::class, 'passwordUpdate'])->name('admin.password.update');


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'TwoFa'], 'as' => 'admin.'], function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('store-token', [DashboardController::class, 'storeToken'])->name('storeToken');
    Route::get('my-account', [AccountController::class, 'index'])->name('account');
    Route::get('security', [SecurityController::class, 'index'])->name('security');
    Route::post('/security-otp-verify', [SecurityController::class, 'otpVerify'])->name('security.otpVerify');
    Route::get('/security-qr-code-generate', [SecurityController::class, 'qrCodeGenerate'])->name('security.qrCodeGenerate');
    Route::post('update-profile', [AccountController::class, 'updateProfileImage'])->name('updateProfileImage');
    Route::post('change-password', [AccountController::class, 'changePassword'])->name('changePassword');
    Route::post('change-email', [AccountController::class, 'changeEmail'])->name('changeEmail');

    Route::prefix('setting')->name('setting.')->group(function () {
        Route::prefix('city')->name('city.')->group(function () {
            Route::get('/', [CityController::class, 'index'])->name('index');
            Route::get('list', [CityController::class, 'list'])->name('list');
            Route::get('delete/{id}', [CityController::class, 'delete'])->name('delete');
            Route::post('/store', [CityController::class, 'save'])->name('store');
        });

        Route::prefix('cab')->name('cab.')->group(function () {
            Route::get('/', [ModelController::class, 'index'])->name('index');

            Route::prefix('model')->name('model.')->group(function () {
                Route::get('list', [ModelController::class, 'list'])->name('list');
                Route::post('/store', [ModelController::class, 'store'])->name('store');
                Route::get('delete/{id}', [ModelController::class, 'destroy'])->name('delete');
            });

            Route::prefix('color')->name('color.')->group(function () {
                Route::get('list', [ColorController::class, 'list'])->name('list');
                Route::post('/store', [ColorController::class, 'store'])->name('store');
                Route::get('delete/{id}', [ColorController::class, 'destroy'])->name('delete');
            });
        });

        // Settings route
        Route::prefix('logo')->name('logos.')->group(function () {
            // Logo
            Route::get('/', [SettingController::class, 'logo'])->name('logo');
            Route::post('/store-splash-screen-logo', [SettingController::class, 'splashScreenLogo'])->name('splashScreenLogo');
            Route::post('/app-logo', [SettingController::class, 'appLogo'])->name('appLogo');
            Route::post('/driver-splash-logo', [SettingController::class, 'driverSplashLogo'])->name('driverSplashLogo');
            Route::post('/driver-app-logo', [SettingController::class, 'driverAppLogo'])->name('driverAppLogo');
            Route::post('/admin-panel-logo', [SettingController::class, 'adminPanelLogo'])->name('adminPanelLogo');
            Route::post('/invoice-logo', [SettingController::class, 'invoiceLogo'])->name('invoiceLogo');
            Route::post('/mail-logo', [SettingController::class, 'mailLogo'])->name('mailLogo');
        });

        // Plate form fee
        Route::get('/aggregator-commission', [SettingController::class, 'plateFormFee'])->name('plateFormFee');
        Route::post('/plate-form-fee', [SettingController::class, 'storePlateFormFee'])->name('storePlateFormFee');

        // TDS And Gst
        Route::get('/plat-form-tax', [SettingController::class, 'plateFormTax'])->name('plateFormTax');
        Route::post('/store-plat-form-tax', [SettingController::class, 'storePlateFormTax'])->name('storePlateFormTax');

        // SMTP
        // Pricing (internal markup, platform fee, advance), GST, invoicing business profiles
        Route::get('/pricing', [BillingSettingsController::class, 'pricing'])->name('pricing');
        Route::post('/pricing', [BillingSettingsController::class, 'savePricing'])->name('savePricing');
        Route::get('/gst', [BillingSettingsController::class, 'gst'])->name('gst');
        Route::post('/gst', [BillingSettingsController::class, 'saveGst'])->name('saveGst');
        Route::get('/business-profiles', [BillingSettingsController::class, 'profiles'])->name('businessProfiles');
        Route::post('/business-profiles/{role}', [BillingSettingsController::class, 'saveProfile'])->name('saveBusinessProfile');

        Route::get('/smtp-cred', [SettingController::class, 'smtpCred'])->name('smtpCred');
        Route::post('/store-cred', [SettingController::class, 'storeSmtpCred'])->name('storeSmtpCred');

        // Setting
        Route::get('/settings', [SettingController::class, 'settings'])->name('index');
        Route::post('/settings', [SettingController::class, 'saveSettings'])->name('save');

        // fleet operators
        Route::prefix('fleet-operators')->name('fleetOperators.')->group(function () {
            Route::get('/', [FleetOperatorController::class, 'index'])->name('index');
            Route::post('/', [FleetOperatorController::class, 'list'])->name('list');
            Route::post('/store', [FleetOperatorController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [FleetOperatorController::class, 'edit'])->name('edit');
            Route::post('/update', [FleetOperatorController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [FleetOperatorController::class, 'delete'])->name('delete');
        });

        // fleet operators
        Route::prefix('sas-company-details')->name('companyDetails.')->group(function () {
            Route::get('/', [CompanyDetailsController::class, 'index'])->name('index');
            Route::post('/store', [CompanyDetailsController::class, 'storeUpdate'])->name('store');
        });

        // Base Fare
        Route::prefix('base-fare')->name('baseFare.')->group(function () {
            Route::get('/', [BaseFareController::class, 'index'])->name('index');
            Route::post('/', [BaseFareController::class, 'list'])->name('list');
            Route::post('/store-update', [BaseFareController::class, 'StoreUpdate'])->name('store');
            Route::get('/edit/{priceDetails}', [BaseFareController::class, 'edit'])->name('edit');
            Route::get('/delete/{priceDetails}', [BaseFareController::class, 'delete'])->name('delete');
        });
        Route::prefix('cab-rates')->name('cabRate.')->group(function () {
            Route::get('/', [CabRateController::class, 'index'])->name('index');
            Route::post('/', [CabRateController::class, 'list'])->name('list');
            Route::post('/store-update', [CabRateController::class, 'StoreUpdate'])->name('store');
            Route::get('/edit/{priceDetails}', [CabRateController::class, 'edit'])->name('edit');
            Route::get('/delete/{cabRate}', [CabRateController::class, 'delete'])->name('delete');
            Route::post('airport-pickup-percentage-calculation', [CabRateController::class, 'airportPickupPercentageCalculation'])->name('airportPickupPercentageCalculation');
        });

        // Payment keys
        Route::prefix('payment-gateway-keys')->name('paymentGateway.')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index');
            Route::post('/store-update', [PaymentController::class, 'StoreUpdate'])->name('store');
        });

        // SOS Number
        Route::get('/sos-numbers', [SettingController::class, 'sosNumbers'])->name('sosNumbers');
        Route::post('/store-sos-numbers', [SettingController::class, 'storeSosNumbers'])->name('storeSosNumbers');

        // Advance Booking
        Route::get('/booking-and-cancellation', [SettingController::class, 'advanceBooking'])->name('advanceBooking');
        Route::post('/store-advance-bookings', [SettingController::class, 'storeAdvanceBooking'])->name('storeAdvanceBooking');
        Route::post('/store-advance-bookings-cancellation', [SettingController::class, 'storeAdvanceBookingCancellationForm'])->name('storeAdvanceBookingCancellationForm');

        // app update
        Route::get('app-update-details', [SettingController::class, 'appUpdate'])->name('appUpdate');
        Route::post('/store-app-update-details', [SettingController::class, 'storeAppUpdate'])->name('storeAppUpdate');

    });

    // Cab Management
    Route::prefix('cab-management')->name('cabs.')->group(function () {
        Route::get('/', [CabManagementController::class, 'index'])->name('index');
        Route::post('/', [CabManagementController::class, 'list'])->name('list');
        Route::get('create', [CabManagementController::class, 'create'])->name('create');
        Route::post('store', [CabManagementController::class, 'store'])->name('store');
        Route::get('edit/{id}', [CabManagementController::class, 'edit'])->name('edit');
        Route::post('update', [CabManagementController::class, 'update'])->name('update');
        Route::get('view/{id}', [CabManagementController::class, 'view'])->name('view');
        Route::get('/suspend/{id}', [CabManagementController::class, 'suspend'])->name('suspend');
        Route::get('/export-cab-list', [CabManagementController::class, 'exportCabList'])->name('exportCabList');
        Route::get('/surge-price', [CabManagementController::class, 'surgePrice'])->name('surgePrice');
        Route::post('/store-surge-price', [CabManagementController::class, 'storeSurgePrice'])->name('storeSurgePrice');
    });

    // Location
    Route::get('get-states/{id}', [LocationController::class, 'getStates'])->name('getStates');
    Route::get('get-city/{id}', [LocationController::class, 'getCity'])->name('getCity');

    // Coupon Management
    Route::prefix('discount-coupons')->name('coupons.')->group(function () {
        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::post('/', [CouponController::class, 'list'])->name('list');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::post('/storeUpdate', [CouponController::class, 'storeUpdate'])->name('storeUpdate');
        Route::get('/edit/{id}', [CouponController::class, 'edit'])->name('edit');
        Route::get('/delete/{id}', [CouponController::class, 'delete'])->name('delete');
        Route::get('/change/{id}', [CouponController::class, 'change'])->name('change');
    });

    // Content Management
    Route::prefix('content-management')->name('pages.')->group(function () {
        Route::get('/{slug}', [ContentManagementController::class, 'index'])->name('index');
        Route::post('/storeUpdate', [ContentManagementController::class, 'storeUpdate'])->name('storeUpdate');
    });

    // Customer Management
    Route::prefix('customer-management')->name('customers.')->group(function () {
        Route::get('/', [CustomerManagementController::class, 'index'])->name('index');
        Route::post('/', [CustomerManagementController::class, 'list'])->name('list');
        Route::get('/changeStatus/{id}', [CustomerManagementController::class, 'changeStatus'])->name('changeStatus');
        Route::get('/delete-user-details', [CustomerManagementController::class, 'deleteUserDetails'])->name('deleteUserDetails');
        Route::post('/delete-user-details', [CustomerManagementController::class, 'deleteUserDetailsList'])->name('deleteUserDetailsList');
        Route::get('/export-user', [CustomerManagementController::class, 'exportUserList'])->name('exportUserList');
        Route::get('/export-deleted-user', [CustomerManagementController::class, 'exportDeletedUserList'])->name('exportDeletedUserList');
    });

    // Trip Management
    Route::prefix('trip-management')->name('trips.')->group(function () {
        Route::get('/', [TripManagementController::class, 'index'])->name('index');
        Route::post('/', [TripManagementController::class, 'list'])->name('list');
        // Route::get('/get-driver-list', [TripManagementController::class, 'getDriverList'])->name('getDriverList');
        Route::post('/assign-driver', [TripManagementController::class, 'assignDriver'])->name('assignDriver');
        Route::get('/get-booking-details', [TripManagementController::class, 'getBookingDetails'])->name('getBookingDetails');
        Route::get('/get-driver-list', [TripManagementController::class, 'getDriverList'])->name('getDriverList');
        Route::get('/change-driver', [TripManagementController::class, 'changeDriver'])->name('changeDriver');
        Route::get('/cancel-booking/{id}/{type}', [TripManagementController::class, 'cancelBooking'])->name('cancelBooking');
        Route::post('/refund-amount', [RazorpayController::class, 'refundAmount'])->name('refundAmount');
        Route::post('/no-show/{id}', [TripManagementController::class, 'markNoShow'])->name('markNoShow');
        Route::post('/no-show/{id}/undo', [TripManagementController::class, 'undoNoShow'])->name('undoNoShow');
        Route::post('/booking-detail', [TripManagementController::class, 'bookingDetail'])->name('bookingDetail');
        Route::post('/update-call-status', [TripManagementController::class, 'updateCallStatus'])->name('updateCallStatus');
    });

    // Advertisement
    Route::prefix('advertisements')->name('advertisements.')->group(function () {
        Route::get('/', [AdvertisementController::class, 'index'])->name('index');
        Route::post('/', [AdvertisementController::class, 'list'])->name('list');
        Route::post('/storeUpdate', [AdvertisementController::class, 'storeUpdate'])->name('storeUpdate');
        Route::get('/edit/{advertisement}', [AdvertisementController::class, 'edit'])->name('edit');
        Route::get('/delete/{advertisement}', [AdvertisementController::class, 'delete'])->name('delete');
        Route::get('/get-advertiser-details', [AdvertisementController::class, 'getAdvertiserDetails'])->name('getAdvertiserDetails');
        Route::get('/get-advertiser-details-amount', [AdvertisementController::class, 'getAdvertiserDetailsAmount'])->name('getAdvertiserDetailsAmount');
        Route::get('/export-advertiser-list', [AdvertisementController::class, 'exportAdvertiserList'])->name('exportAdvertiserList');
        Route::get('/get-total-customer-count', [AdvertisementController::class, 'getTotalCustomerCount'])->name('getTotalCustomerCount');
        Route::get('/get-screen-amount', [AdvertisementController::class, 'getScreenAmount'])->name('getScreenAmount');

        Route::prefix('active-ads')->name('activeAdds.')->group(function () {
            Route::get('/', [ActiveAddController::class, 'index'])->name('index');
            Route::post('/', [ActiveAddController::class, 'list'])->name('list');
            Route::post('/renew-ad/{advertisement}', [ActiveAddController::class, 'renew'])->name('renew');
            Route::post('view-leads', [ActiveAddController::class, 'viewLeads'])->name('viewLeads');
            Route::post('view-leads-list/{id}', [ActiveAddController::class, 'viewLeadsList'])->name('viewLeadsList');
            Route::get('/export-lead-details', [ActiveAddController::class, 'exportLeadDetails'])->name('exportLeadDetails');
        });
        Route::prefix('expired-ads')->name('expiredAdds.')->group(function () {
            Route::get('/', [ExpiredController::class, 'index'])->name('index');
            Route::post('/', [ExpiredController::class, 'list'])->name('list');
            Route::post('/renew-ad/{advertisement}', [ExpiredController::class, 'renew'])->name('renew');
        });

        // Screen Price
        Route::prefix('screen-price')->name('screenPrice.')->group(function () {
            Route::get('/', [ScreenPriceController::class, 'index'])->name('index');
            Route::post('/storeUpdate', [ScreenPriceController::class, 'storeUpdate'])->name('storeUpdate');
        });
    });

    // report
    Route::prefix('reports')->name('report.')->group(function () {
        Route::get('/', [FleetOperatorPaymentController::class, 'index'])->name('index');
        Route::post('/list', [FleetOperatorPaymentController::class, 'list'])->name('list');
        Route::post('/list-paid', [FleetOperatorPaymentController::class, 'listPaid'])->name('list.paid');
        Route::post('/list-unpaid', [FleetOperatorPaymentController::class, 'listUnpaid'])->name('list.unpaid');
        Route::post('/list-refund', [FleetOperatorPaymentController::class, 'listRefund'])->name('list.refund');
        Route::get('/export', [FleetOperatorPaymentController::class, 'export'])->name('export');
        Route::get('/reconcile-cancelled-payments', [PaymentReconciliationController::class, 'index'])->name('reconcileCancelledPayments');
    });

    // Notification
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/list', [NotificationController::class, 'list'])->name('list');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        Route::get('/delete/{id}', [NotificationController::class, 'delete'])->name('delete');

        //Custom Notifications
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/store', [NotificationController::class, 'store'])->name('store');
    });

    Route::prefix('sightseeing-packages')->name('sightseeingPackages.')->group(function () {
        Route::get('/', [SightSeeingPackageController::class, 'index'])->name('index');
        Route::post('/list', [SightSeeingPackageController::class, 'list'])->name('list');
        Route::get('/manage', [SightSeeingPackageController::class, 'manage'])->name('manage');
        Route::post('/save', [SightSeeingPackageController::class, 'save'])->name('save');
        Route::get('/edit/{sightSeeingPackages}', [SightSeeingPackageController::class, 'edit'])->name('edit');
        Route::delete('/delete/{sightSeeingPackages}', [SightSeeingPackageController::class, 'delete'])->name('delete');
        Route::delete('/delete-package-image',[SightSeeingPackageController::class,'deletePackageImage'])->name('deletePackageImage');
        Route::delete('/delete-package-cab-price',[SightSeeingPackageController::class,'deletePackageCabPrice'])->name('deletePackageCabPrice');
    });

    // Accounting: GST documents, OfinIT platform-fee invoices, GSTR-1 export
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/gstr1', [InvoiceController::class, 'exportGstr1'])->name('gstr1');
        Route::post('/platform-fee', [InvoiceController::class, 'generatePlatformFee'])->name('platformFee');
        Route::post('/issue-drafts', [InvoiceController::class, 'issueDrafts'])->name('issueDrafts');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::post('/{invoice}/issue', [InvoiceController::class, 'issue'])->name('issue');
        Route::post('/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('markPaid');
    });
});
