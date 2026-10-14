<?php

use App\Http\Controllers\Fleet\FleetController;
use Illuminate\Support\Facades\Route;

/*
| Seema Cabs Fleet app (fleet-app/ Android shell loads /fleet). Fleet
| operator and admin logins; TwoFa as in the admin panel.
*/
Route::prefix('fleet')->name('fleet.')->group(function () {
    Route::get('login', [FleetController::class, 'loginForm'])->name('login');
    Route::post('login', [FleetController::class, 'login'])->middleware('throttle:10,1')->name('doLogin');

    Route::middleware(['fleet.access', 'TwoFa'])->group(function () {
        Route::get('/', [FleetController::class, 'index'])->name('index');
        Route::get('bookings/{booking}', [FleetController::class, 'show'])->name('show');
        Route::post('device-token', [FleetController::class, 'registerToken'])->middleware('throttle:30,1')->name('token');
        Route::post('logout', [FleetController::class, 'logout'])->name('logout');
    });
});
