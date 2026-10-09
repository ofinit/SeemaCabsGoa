<?php

use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CabController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PageContentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RazorpayWebhookController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SightSeeingPackageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\vendorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth Api
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-forgot-password-mail', [AuthController::class, 'sendMail']);
Route::post('/otp-verify', [AuthController::class, 'otpVerify']);
Route::post('/password-reset', [AuthController::class, 'passwordUpdate']);

// Location
Route::get('/country-list', [LocationController::class, 'countryList']);
Route::get('/state-list/{id}', [LocationController::class, 'getStateList']);
Route::get('/get-city-list', [LocationController::class, 'getCityList']);
Route::post('/booking-register', [AuthController::class, 'bookingRegister']);

// Setting Details
Route::get('/settings', [SettingController::class, 'settingDetails']);

Route::post('/vendor-token-update', [vendorController::class, 'update']);


// Get Content
Route::get('/get-page-content/{slug}', [PageContentController::class, 'getPageContent']);

// Razorpay webhook — called directly by Razorpay's servers, never by a
// browser. No app-level auth; authenticity is verified via the webhook
// signature header instead (see RazorpayWebhookController).
Route::post('/razorpay/webhook', [RazorpayWebhookController::class, 'handle']);

// Cashfree webhook — called directly by Cashfree servers. Authenticity
// is verified via the x-webhook-signature and x-webhook-timestamp headers.
Route::post('/cashfree/webhook', [\App\Http\Controllers\Api\CashfreeWebhookController::class, 'handle']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    // User Details
    Route::get('/user-details', [UserController::class, 'details']);
    Route::post('/user-details', [UserController::class, 'update']);
    Route::post('/delete-account', [UserController::class, 'deleteAccount']);

    // Cabs
    Route::get('/get-cab-list', [CabController::class, 'getCabList']);

    // Booking
    Route::post('/booking-details', [BookingController::class, 'store']);
    Route::get('/bookings', [BookingController::class, 'getMyBooking']);
    Route::get('/current-booking', [BookingController::class, 'getMyCurrentBooking']);
    Route::get('/booking-detail/{id}', [BookingController::class, 'getSingleBooking']);
    Route::post('/update-payment-status', [BookingController::class, 'updatePaymentStatus']);
    Route::post('/cancel-ride', [BookingController::class, 'cancelRide']);
    Route::delete('/delete-ride/{id}', [BookingController::class, 'deleteBooking']);

    //Advertisement
    Route::post('/advertisement-click', [AdvertisementController::class, 'manageClick']);
    // Sponsored-push consent (P11): GET status, POST {on: true|false}.
    Route::get('/ads/push-opt-in', [\App\Http\Controllers\AdTrackingController::class, 'pushOptIn']);
    Route::post('/ads/push-opt-in', [\App\Http\Controllers\AdTrackingController::class, 'pushOptIn']);

    //Notification List
    Route::prefix('notification')->name('notification.')->group(function () {
        Route::get('/list', [NotificationController::class, 'list']);
        Route::delete('/delete/{id}', [NotificationController::class, 'delete']);
    });

    //SightSeeing Packages Cab List
    Route::prefix('sight-seeing-packages')->group(function () {
        Route::get('list',[SightSeeingPackageController::class,'getSightSeeingPackagesList']);
        Route::post('get-cab-list',[SightSeeingPackageController::class,'getCabList']);
        Route::post('store-booking',[BookingController::class,'sightSeeingBooking']);
    });
});

// Advertisement
Route::get('/advertisement-list', [AdvertisementController::class, 'index']);
// Ads for app builds (docs/ADS_APP_INTEGRATION.md): one screen at a time, view beacon, report.
Route::get('/ads', [AdvertisementController::class, 'index'])->middleware('throttle:120,1');
Route::post('/ads/impressions', [\App\Http\Controllers\AdTrackingController::class, 'impressions'])->middleware('throttle:120,1');
Route::post('/ads/report', [\App\Http\Controllers\AdTrackingController::class, 'report'])->middleware('throttle:10,1');
Route::post('/create-order', [PaymentController::class, 'createOrder']);
Route::post('/cashfree/create-order', [PaymentController::class, 'createCashfreeOrder']);
