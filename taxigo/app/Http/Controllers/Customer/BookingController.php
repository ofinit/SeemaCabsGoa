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
use App\Enums\Type;
use App\Models\BookingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Cashfree `return_url` (…/cashfree/return?order_id={order_id}). Reached
     * when a payment method leaves the checkout modal (some netbanking / 3-D
     * Secure flows). The browser's word is never trusted: confirmation goes
     * through the same server-side Get Order verification as the in-page
     * flow, and the webhook remains the backup.
     */
    public function cashfreeReturn(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');
        $booking = null;
        if (preg_match('/^CF_([A-Za-z0-9-]+)_\d+$/', $orderId, $m)) {
            $booking = BookingDetail::where('booking_id', $m[1])
                ->where('customer_id', Auth::guard('customer')->id())
                ->first();
        }
        if (!$booking) {
            return view('customer.book.payment-result', ['state' => 'unknown', 'booking' => null, 'message' => 'We could not find this booking.']);
        }

        if ((int) $booking->payment_status !== Type::ACTIVE) {
            $result = app(ApiBookingController::class)->updatePaymentStatus(new Request([
                'booking_id' => $booking->booking_id,
                'status' => 1,
                'transaction_id' => $orderId,
                'pg_order_id' => $orderId,
                'payment_gateway' => 'cashfree',
            ]))->getData(true);

            if (empty($result['status'])) {
                return view('customer.book.payment-result', [
                    'state' => 'failed',
                    'booking' => $booking,
                    'message' => $result['message'] ?? 'Payment was not completed.',
                ]);
            }
            $booking->refresh();
        }

        return view('customer.book.payment-result', ['state' => 'paid', 'booking' => $booking, 'message' => null]);
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
