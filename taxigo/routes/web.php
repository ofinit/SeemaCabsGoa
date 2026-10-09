<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceDocumentController;
use App\Http\Controllers\AdTrackingController;
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

// The Docker image copies the static marketing site into public/, so the root
// URL serves its homepage. Without it (e.g. local dev) fall back to the legacy
// authenticated dashboard view.
Route::get('/', function () {
    if (file_exists($home = public_path('index.html'))) {
        return response()->file($home);
    }

    return auth()->check() ? view('index') : redirect()->route('admin.login');
});

require __DIR__ . '/admin.php';
require __DIR__ . '/customer.php';
