<?php

use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\AuthController;
use App\Http\Controllers\Customer\BookingController;
use App\Http\Controllers\Customer\PageController;
use App\Http\Controllers\InvoiceDocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer PWA Routes
|--------------------------------------------------------------------------
|
| Everything here is namespaced under /app and uses the dedicated
| "customer" session guard (see config/auth.php + app/Http/Middleware/Customer)
| so it never shares session state with the admin panel. Read-only "page"
| routes render Blade shells; "actions" routes are fetch/JSON endpoints
| consumed by Alpine.js and mostly delegate to the existing Api\* controllers
| in-process to reuse fare/OTP/booking business logic without duplicating it.
|
*/

Route::prefix('app')->name('customer.')->group(function () {

    Route::get('/', [PageController::class, 'splash'])->name('splash');
    Route::get('404', fn () => response()->view('errors.404', [], 404))->name('notfound');

    Route::middleware('customer.guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
        Route::get('signup', [AuthController::class, 'showSignup'])->name('signup');
        Route::post('signup', [AuthController::class, 'register'])->name('signup.submit');
        Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('forgot-password', [AuthController::class, 'sendResetOtp'])->name('forgot-password.submit');
        Route::get('forgot-password/otp', [AuthController::class, 'showOtp'])->name('forgot-password.otp');
        Route::post('forgot-password/otp', [AuthController::class, 'verifyOtp'])->name('forgot-password.otp.submit');
        Route::get('forgot-password/reset', [AuthController::class, 'showReset'])->name('forgot-password.reset');
        Route::post('forgot-password/reset', [AuthController::class, 'resetPassword'])->name('forgot-password.reset.submit');
        Route::post('auth/google', [AuthController::class, 'googleLogin'])->name('auth.google');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Public lookup & interaction actions accessible to guests & authenticated customers
    Route::prefix('actions')->name('actions.')->group(function () {
        Route::get('settings', [PageController::class, 'settingsJson'])->name('settings');
        Route::get('countries', [PageController::class, 'countriesJson'])->name('countries');
        Route::get('cities', [PageController::class, 'citiesJson'])->name('cities');
        Route::get('states/{country}', [PageController::class, 'statesJson'])->name('states');
        Route::get('advertisements', [PageController::class, 'advertisementsJson'])->name('advertisements');
        Route::post('advertisement-click', [PageController::class, 'advertisementClick'])->name('advertisement-click');
        Route::post('ad-impressions', [\App\Http\Controllers\AdTrackingController::class, 'impressions'])->middleware('throttle:120,1')->name('ad-impressions');
        Route::get('page-content/{slug}', [PageController::class, 'pageContentJson'])->name('page-content');
    });

    Route::middleware('customer.auth:customer')->group(function () {
        Route::get('home', [PageController::class, 'home'])->name('home');

        Route::get('book', [PageController::class, 'book'])->name('book');
        Route::get('book/results', [PageController::class, 'results'])->name('book.results');
        Route::get('book/finding', [PageController::class, 'finding'])->name('book.finding');
        Route::get('book/review', [PageController::class, 'review'])->name('book.review');

        Route::get('trip/{booking}', [PageController::class, 'trip'])->name('trip');
        Route::get('trip/{booking}/sos', [PageController::class, 'sos'])->name('trip.sos');

        Route::get('rides', [PageController::class, 'rides'])->name('rides');
        Route::get('account', [PageController::class, 'account'])->name('account');
        Route::get('notifications', [PageController::class, 'notifications'])->name('notifications');
        Route::get('discover/{package}', [PageController::class, 'discoverPackage'])->name('discover');
        Route::get('advertise', [PageController::class, 'advertise'])->name('advertise');
        Route::get('invoices/{invoice}', [InvoiceDocumentController::class, 'customer'])->name('invoices.show');

        Route::prefix('actions')->name('actions.')->group(function () {
            Route::get('cab-list', [BookingController::class, 'cabList'])->name('cab-list');
            Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
            Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
            Route::get('bookings/current', [BookingController::class, 'current'])->name('bookings.current');
            Route::get('bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
            Route::delete('bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
            Route::post('cancel-ride', [BookingController::class, 'cancel'])->name('cancel-ride');
            Route::post('create-order', [BookingController::class, 'createOrder'])->name('create-order');
            Route::post('cashfree/create-order', [BookingController::class, 'createCashfreeOrder'])->name('cashfree.create-order');
            Route::post('confirm-payment', [BookingController::class, 'confirmPayment'])->name('confirm-payment');
            // Cashfree return_url — the path is fixed by CashfreeService::createOrder().
            Route::get('cashfree/return', [BookingController::class, 'cashfreeReturn'])->name('cashfree.return');

            Route::get('sightseeing', [BookingController::class, 'sightseeingList'])->name('sightseeing.list');
            Route::post('sightseeing/cab-list', [BookingController::class, 'sightseeingCabList'])->name('sightseeing.cab-list');
            Route::post('sightseeing/book', [BookingController::class, 'storeSightseeing'])->name('sightseeing.book');

            Route::get('notifications', [PageController::class, 'notificationsJson'])->name('notifications.index');
            Route::delete('notifications/{id}', [PageController::class, 'deleteNotification'])->name('notifications.destroy');

            Route::get('account', [AccountController::class, 'show'])->name('account.show');
            Route::post('account', [AccountController::class, 'update'])->name('account.update');
            Route::post('account/delete', [AccountController::class, 'delete'])->name('account.delete');
        });
    });
});
