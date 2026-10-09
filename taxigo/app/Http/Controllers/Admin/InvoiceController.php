<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Invoice;
use App\Services\Invoicing\InvoiceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin → Accounting → Invoices: every GST document, the monthly OfinIT
 * platform-fee invoice, issuing drafts, and the GSTR-1 export.
 */
class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoices)
    {
    }

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user() && (int) Auth::user()->type === Type::ADMIN, 403);
    }

    private function month(Request $request): Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m', (string) $request->input('month', now('Asia/Kolkata')->format('Y-m')), 'Asia/Kolkata')->startOfMonth();
        } catch (\Throwable) {
            return now('Asia/Kolkata')->startOfMonth();
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $month = $this->month($request);
        $type = $request->input('type');

        $query = Invoice::with('booking')->latest('id');
        if ($request->input('scope') !== 'all') {
            $query->where(function ($q) use ($month) {
                $q->whereBetween('issue_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
                    ->orWhere(fn ($q) => $q->whereNull('issue_date')->where('status', Invoice::DRAFT));
            });
        }
        if ($type && array_key_exists($type, Invoice::TYPE_LABELS)) {
            $query->where('type', $type);
        }

        $list = $query->paginate(50)->withQueryString();
        $drafts = Invoice::where('status', Invoice::DRAFT)->count();
        $profiles = [BusinessProfile::supplier(), BusinessProfile::platform()];
        $platformInvoice = Invoice::where('type', Invoice::PLATFORM_FEE)->whereDate('period_start', $month->toDateString())->latest('id')->first();

        return view('accounting.invoices.index', compact('list', 'month', 'type', 'drafts', 'profiles', 'platformInvoice'));
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeAdmin();
        $invoice->load('lines', 'booking', 'related');

        return view('invoices.document', compact('invoice'));
    }

    public function generatePlatformFee(Request $request)
    {
        $this->authorizeAdmin();
        $month = $this->month($request);

        try {
            $result = $this->invoices->platformFeeDraft($month);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if (!$result['invoice'] && !$result['credit_note']) {
            return back()->with('error', 'No completed or no-show bookings picked up in ' . $month->format('F Y') . ' are left to bill.');
        }

        $message = 'Draft platform-fee invoice prepared for ' . $month->format('F Y') . " ({$result['bookings']} bookings).";
        if ($result['credit_note']) {
            $message .= ' A draft credit note was also prepared for bookings refunded after earlier invoicing.';
        }

        return redirect()->route('admin.invoices.index', ['month' => $month->format('Y-m'), 'type' => Invoice::PLATFORM_FEE])->with('success', $message . ' Review it, then click Issue.');
    }

    public function issue(Invoice $invoice)
    {
        $this->authorizeAdmin();

        try {
            $this->invoices->issue($invoice);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $invoice->label() . ' ' . $invoice->number . ' issued.');
    }

    public function issueDrafts()
    {
        $this->authorizeAdmin();
        $issued = 0;
        $errors = [];
        // Customer documents only — platform-fee invoices are reviewed and issued one by one.
        Invoice::where('status', Invoice::DRAFT)
            ->whereIn('type', [Invoice::RECEIPT_VOUCHER, Invoice::TAX_INVOICE, Invoice::REFUND_VOUCHER])
            ->orderBy('id')->get()
            ->each(function (Invoice $invoice) use (&$issued, &$errors) {
                try {
                    $this->invoices->issue($invoice);
                    $issued++;
                } catch (RuntimeException $e) {
                    $errors[$e->getMessage()] = true;
                }
            });

        return back()->with($errors ? 'error' : 'success', "{$issued} draft customer document(s) issued." . ($errors ? ' ' . implode(' ', array_keys($errors)) : ''));
    }

    public function markPaid(Invoice $invoice)
    {
        $this->authorizeAdmin();
        abort_unless($invoice->type === Invoice::PLATFORM_FEE && $invoice->isIssued(), 404);
        $invoice->paid_at = $invoice->paid_at ? null : now();
        $invoice->save();

        return back()->with('success', $invoice->number . ($invoice->paid_at ? ' marked as paid.' : ' marked as unpaid.'));
    }

    /** GSTR-1 working export: one row per issued document in the month. */
    public function exportGstr1(Request $request): StreamedResponse
    {
        $this->authorizeAdmin();
        $month = $this->month($request);
        $supplierRole = $request->input('supplier', BusinessProfile::SUPPLIER);
        $prefix = optional(BusinessProfile::where('role', $supplierRole)->first())->invoice_prefix;

        $rows = Invoice::where('status', Invoice::ISSUED)
            ->whereBetween('issue_date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->when($prefix, fn ($q) => $q->where('number', 'like', $prefix . '/%'))
            ->orderBy('issue_date')->orderBy('id')->get();

        $filename = 'gstr1-' . strtolower($prefix ?? 'all') . '-' . $month->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Section', 'Document type', 'Number', 'Date', 'Recipient GSTIN', 'Recipient name', 'Place of supply',
                'SAC', 'Taxable value', 'CGST rate', 'CGST', 'SGST rate', 'SGST', 'IGST rate', 'IGST', 'Total', 'Against', 'Booking']);
            foreach ($rows as $inv) {
                $section = match ($inv->type) {
                    Invoice::RECEIPT_VOUCHER => 'Advances received (11A)',
                    Invoice::REFUND_VOUCHER => 'Advance adjusted / refunded (11B)',
                    Invoice::CREDIT_NOTE => $inv->is_b2b ? 'Credit note — registered (9B)' : 'Credit note — unregistered (9B)',
                    default => $inv->is_b2b ? 'B2B (4A)' : 'B2C small (7)',
                };
                fputcsv($out, [
                    $section, $inv->label(), $inv->number, optional($inv->issue_date)->format('d-m-Y'),
                    $inv->recipient['gstin'] ?? '', $inv->recipient['name'] ?? '', $inv->place_of_supply,
                    $inv->sac_code, $inv->taxable_value, $inv->cgst_rate, $inv->cgst_amount, $inv->sgst_rate,
                    $inv->sgst_amount, $inv->igst_rate, $inv->igst_amount, $inv->total,
                    optional($inv->related)->number, optional($inv->booking)->booking_id,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
