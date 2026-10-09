<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\FleetOperator;
use App\Models\Payment;
use App\Services\Payments\FleetPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Admin → Reports → Fleet Payouts: status of each paid booking's payout to the
 * fleet operator (Razorpay Route / Cashfree split), with retry.
 */
class FleetPayoutController extends Controller
{
    public function __construct(private readonly FleetPayoutService $payouts)
    {
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user() && (int) Auth::user()->type === Type::ADMIN, 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $filter = $request->input('filter', 'failed');

        $query = Payment::with('bookingDetails')->where('status', Type::PAID)->latest('id');
        if ($filter === 'failed') {
            $query->whereNull('transfer_reference');
        } elseif ($filter === 'paid') {
            $query->whereNotNull('transfer_reference');
        }

        $payments = $query->paginate(50)->withQueryString();
        $operator = FleetOperator::orderBy('id')->first();
        $counts = [
            'failed' => Payment::where('status', Type::PAID)->whereNull('transfer_reference')->count(),
            'paid' => Payment::where('status', Type::PAID)->whereNotNull('transfer_reference')->count(),
        ];

        return view('reports.fleet-payouts', compact('payments', 'filter', 'operator', 'counts'));
    }

    public function retry(Payment $payment)
    {
        $this->authorizeAdmin();
        $booking = $payment->bookingDetails;
        abort_unless($booking, 404);

        $result = $this->payouts->payout($booking, $payment, true);

        return back()->with($result['ok'] ? 'success' : 'error', $booking->booking_id . ': ' . $result['message']);
    }

    public function retryFailed()
    {
        $this->authorizeAdmin();
        $ok = 0;
        $failed = 0;
        Payment::with('bookingDetails')->where('status', Type::PAID)->whereNull('transfer_reference')
            ->where('created_at', '>=', now()->subDays(60))->orderBy('id')->limit(100)->get()
            ->each(function (Payment $payment) use (&$ok, &$failed) {
                if (!$payment->bookingDetails) {
                    return;
                }
                $this->payouts->payout($payment->bookingDetails, $payment, true)['ok'] ? $ok++ : $failed++;
            });

        return back()->with($failed ? 'error' : 'success', "Retried payouts from the last 60 days: {$ok} paid, {$failed} still failing (see the reason on each row).");
    }
}
