<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceDocumentController;
use App\Http\Controllers\AdTrackingController;
use App\Http\Controllers\MarketingPageController;
use Illuminate\Support\Facades\Auth;

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

// Maintenance commands (cache:clear, migrate, store:in-city-rides) are run with
// `php artisan` from the server terminal, never exposed as public URLs.

Auth::routes();

// Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);


// Legal policy redirects
Route::redirect('privacy-policy', '/privacy-policy.html');
Route::redirect('terms-and-conditions', '/terms-and-conditions.html');
Route::redirect('terms', '/terms-and-conditions.html');
Route::redirect('terms-of-service', '/terms-and-conditions.html');
Route::redirect('cancellation-refund-policy', '/cancellation-refund-policy.html');
Route::redirect('refund-policy', '/cancellation-refund-policy.html');

// GST documents via signed, login-free links (emails and the mobile apps).
Route::get('invoices/{invoice}', [InvoiceDocumentController::class, 'signed'])->middleware('signed')->name('invoices.public');

// Ad click tracking: signed link → click counted → advertiser URL with UTM tags.
Route::get('ads/c/{advertisement}', [AdTrackingController::class, 'click'])->middleware(['signed', 'throttle:120,1'])->name('ads.click');

// Website ads (P9) for the static marketing pages: fetch, view beacon, report. No session needed.
Route::get('ads/web', [AdTrackingController::class, 'web'])->middleware('throttle:120,1')->name('ads.web');
Route::post('ads/i', [AdTrackingController::class, 'impressions'])->middleware('throttle:120,1')->name('ads.impressions');
Route::post('ads/report', [AdTrackingController::class, 'report'])->middleware('throttle:10,1')->name('ads.report');

// In-cab QR cards (P18) and advertisers' conversion tag.
Route::get('q/{code}', [AdTrackingController::class, 'qr'])->where('code', '[A-Za-z0-9]{6,12}')->middleware('throttle:60,1')->name('ads.qr');
Route::get('ads/conv/{token}.gif', [AdTrackingController::class, 'conversion'])->where('token', '[A-Za-z0-9]{20,32}')->middleware('throttle:120,1')->name('ads.conversion');

// Static marketing pages, served through Laravel (not from public/) so the
// suspension gate covers them instantly. No session: no cookies, cacheable.
$noSession = [
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \App\Http\Middleware\VerifyCsrfToken::class,
];
Route::get('/', function () {
    if (MarketingPageController::available()) {
        return app(MarketingPageController::class)->show();
    }

    // No marketing site (bare app checkout): the legacy dashboard / admin login.
    return redirect()->route('admin.login');
})->withoutMiddleware($noSession);
Route::get('{page}.html', [MarketingPageController::class, 'show'])->where('page', '[a-z0-9][a-z0-9-]*')
    ->withoutMiddleware($noSession)->name('marketing.page');

require __DIR__ . '/admin.php';
require __DIR__ . '/customer.php';

// Unknown paths: a normal 404 — or, while the service is suspended, the
// "temporarily unavailable" page (PlatformSuspensionGate).
Route::fallback(fn () => abort(404));
