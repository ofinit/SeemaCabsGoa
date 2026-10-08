<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
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

Route::get('clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return "Done!";
});

Route::get('migrate', function () {
    Artisan::call('migrate');
    return "Done!";
});

Route::get('in-city-rides', function () {
    Artisan::call('store:in-city-rides');
    return "Done!";
});

Auth::routes();

// Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);


// Legal policy redirects
Route::redirect('privacy-policy', '/privacy-policy.html');
Route::redirect('terms-and-conditions', '/terms-and-conditions.html');
Route::redirect('terms', '/terms-and-conditions.html');
Route::redirect('terms-of-service', '/terms-and-conditions.html');
Route::redirect('cancellation-refund-policy', '/cancellation-refund-policy.html');
Route::redirect('refund-policy', '/cancellation-refund-policy.html');

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
