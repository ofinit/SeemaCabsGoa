<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Api\BookingController as ApiBookingController;
use App\Http\Controllers\Api\CabController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SightSeeingPackageController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentRequest;
use App\Http\Requests\Api\SightSeeingBookingRegisterRequest;
use App\Http\Requests\Api\StoreBookingRequest;
use Illuminate\Http\Request;

/**
 * Thin JSON layer for the customer PWA's booking flow. Every method below
 * delegates to the existing Api\* controllers in-process (same validation,
 * same fare/surge/tax/OTP/booking-id logic the native mobile app relies on)
 * rather than reimplementing it — see the plan's "business-logic reuse" note.
 */
class BookingController extends Controller
{
    public function cabList(Request $request)
    {
        return app(CabController::class)->getCabList($request);
    }

    public function store(StoreBookingRequest $request)
    {
        return app(ApiBookingController::class)->store($request);
    }

    public function index()
    {
        return app(ApiBookingController::class)->getMyBooking();
    }

    public function current()
    {
        return app(ApiBookingController::class)->getMyCurrentBooking();
    }

    public function show(string $id)
    {
        return app(ApiBookingController::class)->getSingleBooking($id);
    }

    public function destroy(string $id)
    {
        return app(ApiBookingController::class)->deleteBooking($id);
    }

    public function cancel(Request $request)
    {
        return app(ApiBookingController::class)->cancelRide($request);
    }

    public function createOrder(PaymentRequest $request)
    {
        return app(PaymentController::class)->createOrder($request);
    }

    public function createCashfreeOrder(Request $request)
    {
        return app(PaymentController::class)->createCashfreeOrder($request);
    }

    public function confirmPayment(Request $request)
    {
        return app(ApiBookingController::class)->updatePaymentStatus($request);
    }

    public function sightseeingList(Request $request)
    {
        return app(SightSeeingPackageController::class)->getSightSeeingPackagesList($request);
    }

    public function sightseeingCabList(Request $request)
    {
        return app(SightSeeingPackageController::class)->getCabList($request);
    }

    public function storeSightseeing(SightSeeingBookingRegisterRequest $request)
    {
        return app(ApiBookingController::class)->sightSeeingBooking($request);
    }
}
