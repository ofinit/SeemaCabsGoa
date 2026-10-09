<?php

namespace App\Console\Commands;

use App\Services\Invoicing\InvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Prepares (never issues) the OfinIT → Seema Holidays platform-fee invoice
 * draft for a month; an admin reviews and issues it in Admin → Invoices.
 * Scheduled on the 1st for the previous month.
 */
class PreparePlatformFeeInvoice extends Command
{
    protected $signature = 'invoices:platform-fee {month? : YYYY-MM, defaults to last month}';

    protected $description = 'Prepare the draft OfinIT platform-fee invoice for a month';

    public function handle(InvoiceService $invoices): int
    {
        $month = $this->argument('month')
            ? Carbon::createFromFormat('Y-m', $this->argument('month'), 'Asia/Kolkata')->startOfMonth()
            : now('Asia/Kolkata')->subMonthNoOverflow()->startOfMonth();

        // Ad platform & operations (self-serve ads) — a separate monthly invoice.
        try {
            $ads = $invoices->adPlatformDraft($month);
            if ($ads['invoice'] || $ads['credit_note']) {
                $this->info("Ad platform draft for {$month->format('F Y')}: {$ads['campaigns']} ad(s).");
            }
        } catch (\RuntimeException $e) {
            $this->warn($e->getMessage());
        }

        try {
            $result = $invoices->platformFeeDraft($month);
        } catch (\RuntimeException $e) {
            $this->warn($e->getMessage());

            return self::SUCCESS;
        }

        if (!$result['invoice'] && !$result['credit_note']) {
            $this->info("Nothing to bill for {$month->format('F Y')}.");

            return self::SUCCESS;
        }

        $this->info("Draft prepared for {$month->format('F Y')}: {$result['bookings']} booking(s)"
            . ($result['invoice'] ? ', ₹' . number_format((float) $result['invoice']->total, 2) : '')
            . ($result['credit_note'] ? ', plus a credit-note draft' : '') . '.');

        return self::SUCCESS;
    }
}
