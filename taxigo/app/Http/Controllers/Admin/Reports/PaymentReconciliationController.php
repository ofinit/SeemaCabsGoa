<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\Environment;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\Error;

/**
 * Cross-checks bookings that were paid then cancelled — but have no refund
 * recorded in our own `booking_details.refund` column — against Razorpay's
 * own records. Answers "did we actually keep this money, or was it refunded
 * through Razorpay directly without our DB being updated?" for each one
 * individually, rather than guessing from our own (possibly stale) data alone.
 *
 * Razorpay's direct payment.fetch() only works for payments within the last
 * 180 days; for anything older it errors "Payment older than 180 days,
 * please use reports". There's no per-payment-ID API for older data, so the
 * fallback here scans the Settlement Recon API (queried by settlement
 * month, not payment ID) across every month from the earliest relevant
 * payment date through the current month, and builds a payment_id -> total
 * refunded lookup from every `type=refund` entry found. Direct fetch is
 * tried first (cheap, single call, works for recent cancellations); the
 * month-scan only runs once per page load and is shared across all rows.
 *
 * Read-only: never writes back to booking_details/payments. The admin
 * decides what to do with each finding (e.g. via the existing Cancel &
 * Refund flow) — this just surfaces the truth from Razorpay's side.
 */
class PaymentReconciliationController extends Controller
{
    /** Hard cap on how many months back the settlement recon scan will go, regardless of data. */
    private const MAX_MONTHS_SCANNED = 24;

    /** Hard cap on pages (of up to 1000 items) fetched per month, so one huge month can't hang the page. */
    private const MAX_PAGES_PER_MONTH = 20;

    public function index()
    {
        $paymentKeyRow = Environment::where('title', Type::PAYMENT_KEY)->first();
        $paymentSecretRow = Environment::where('title', Type::PAYMENT_SECRETE)->first();

        $rows = DB::table('booking_details as bd')
            ->join('payments as p', 'p.booking_id', '=', 'bd.id')
            ->whereNull('bd.deleted_at')
            ->whereNull('p.deleted_at')
            ->where('bd.status', Type::BOOKING_CANCELLATION_STATUS)
            ->where('bd.payment_status', Type::ACTIVE)
            ->whereNull('bd.refund')
            ->select('bd.id', 'bd.booking_id', 'bd.pickup_date', 'bd.total_payment', 'p.transaction_id', 'p.date')
            ->orderByDesc('bd.id')
            ->get();

        $results = [];
        $configError = null;
        $monthsScanned = [];
        $monthsFailed = [];

        if (!$paymentKeyRow || !$paymentSecretRow) {
            $configError = 'Razorpay key/secret are not configured (Settings → Payment Gateway).';
        } else {
            $razorpay = new Api($paymentKeyRow->value, $paymentSecretRow->value);

            // Build the refund lookup once, covering every month from the
            // earliest relevant payment date through the current month.
            $refundsByPaymentId = [];
            $paymentAmountsById = [];
            if ($rows->isNotEmpty()) {
                [$refundsByPaymentId, $paymentAmountsById, $monthsScanned, $monthsFailed] =
                    $this->scanSettlementRecon($razorpay, $rows->min('date'));
            }

            foreach ($rows as $row) {
                $entry = [
                    'booking_id' => $row->booking_id,
                    'pickup_date' => $row->pickup_date,
                    'our_amount' => (float) $row->total_payment,
                    'transaction_id' => $row->transaction_id,
                    'razorpay_status' => null,
                    'charged_amount' => null,
                    'amount_refunded' => null,
                    'verdict' => null,
                    'error' => null,
                    'source' => null,
                ];

                if (empty($row->transaction_id)) {
                    $entry['error'] = 'No transaction_id recorded on this booking\'s payment.';
                    $entry['verdict'] = 'Could not verify';
                    $results[] = $entry;
                    continue;
                }

                try {
                    $payment = $razorpay->payment->fetch($row->transaction_id);
                    $amountRefunded = ($payment->amount_refunded ?? 0) / 100;
                    $amount = ($payment->amount ?? 0) / 100;

                    $entry['razorpay_status'] = $payment->status;
                    $entry['charged_amount'] = $amount;
                    $entry['amount_refunded'] = $amountRefunded;
                    $entry['source'] = 'Direct lookup';
                    $entry['verdict'] = $this->verdictFor($amountRefunded, $amount);
                } catch (Error $e) {
                    if (str_contains($e->getMessage(), '180 days') && isset($refundsByPaymentId[$row->transaction_id])) {
                        $amountRefunded = $refundsByPaymentId[$row->transaction_id] / 100;
                        $amount = ($paymentAmountsById[$row->transaction_id] ?? 0) / 100;
                        $entry['charged_amount'] = $amount ?: null;
                        $entry['amount_refunded'] = $amountRefunded;
                        $entry['source'] = 'Settlement recon scan';
                        $entry['verdict'] = $this->verdictFor($amountRefunded, $amount ?: $row->total_payment);
                    } elseif (str_contains($e->getMessage(), '180 days')) {
                        // Older than 180 days, but no refund entry turned up anywhere
                        // in the months we scanned — treat as "not refunded", noting
                        // the source so the admin knows this is an absence-of-evidence
                        // read, not a direct confirmation.
                        $entry['charged_amount'] = ($paymentAmountsById[$row->transaction_id] ?? 0) / 100 ?: null;
                        $entry['amount_refunded'] = 0;
                        $entry['source'] = 'Settlement recon scan';
                        $entry['verdict'] = 'Not refunded — kept as revenue';
                    } else {
                        $entry['error'] = $e->getMessage();
                        $entry['verdict'] = 'Could not verify';
                    }
                } catch (\Throwable $e) {
                    $entry['error'] = $e->getMessage();
                    $entry['verdict'] = 'Could not verify';
                }

                $results[] = $entry;
            }
        }

        return view('reports.payment-reconciliation', [
            'results' => $results,
            'configError' => $configError,
            'monthsScanned' => $monthsScanned,
            'monthsFailed' => $monthsFailed,
        ]);
    }

    private function verdictFor(float $amountRefunded, float $amount): string
    {
        if ($amountRefunded <= 0) {
            return 'Not refunded — kept as revenue';
        }
        if ($amount > 0 && $amountRefunded >= $amount) {
            return 'Fully refunded via Razorpay (not reflected in our DB)';
        }
        return 'Partially refunded via Razorpay (not reflected in our DB)';
    }

    /**
     * @return array{0: array<string,int>, 1: array<string,int>, 2: string[], 3: string[]}
     */
    private function scanSettlementRecon(Api $razorpay, string $earliestDate): array
    {
        $refundsByPaymentId = [];
        $paymentAmountsById = [];
        $monthsScanned = [];
        $monthsFailed = [];

        $cursor = new \DateTime($earliestDate);
        $cursor->modify('first day of this month');
        $end = new \DateTime('first day of this month');

        $monthsToScan = [];
        while ($cursor <= $end && count($monthsToScan) < self::MAX_MONTHS_SCANNED) {
            $monthsToScan[] = [(int) $cursor->format('Y'), (int) $cursor->format('n')];
            $cursor->modify('+1 month');
        }

        foreach ($monthsToScan as [$year, $month]) {
            $label = sprintf('%04d-%02d', $year, $month);
            try {
                $skip = 0;
                $count = 1000;
                $pages = 0;
                do {
                    $page = $razorpay->settlement->settlementRecon([
                        'year' => $year,
                        'month' => $month,
                        'count' => $count,
                        'skip' => $skip,
                    ]);
                    $items = $page->toArray()['items'] ?? [];

                    foreach ($items as $item) {
                        if (($item['type'] ?? null) === 'refund' && !empty($item['payment_id'])) {
                            $refundsByPaymentId[$item['payment_id']] =
                                ($refundsByPaymentId[$item['payment_id']] ?? 0) + (int) ($item['debit'] ?? $item['amount'] ?? 0);
                        } elseif (($item['type'] ?? null) === 'payment' && !empty($item['entity_id'])) {
                            $paymentAmountsById[$item['entity_id']] = (int) ($item['amount'] ?? 0);
                        }
                    }

                    $received = count($items);
                    $skip += $received;
                    $pages++;
                } while ($received >= $count && $pages < self::MAX_PAGES_PER_MONTH);

                $monthsScanned[] = $label;
            } catch (\Throwable $e) {
                $monthsFailed[] = $label . ' (' . $e->getMessage() . ')';
            }
        }

        return [$refundsByPaymentId, $paymentAmountsById, $monthsScanned, $monthsFailed];
    }
}
