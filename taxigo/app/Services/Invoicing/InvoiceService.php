<?php

namespace App\Services\Invoicing;

use App\Enums\BookingEnum;
use App\Enums\Type;
use App\Models\AdCampaign;
use App\Models\BookingDetail;
use App\Models\BusinessProfile;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\Ads\AdSettings;
use App\Services\Pricing\PricingSettings;
use App\Support\Gstin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Creates GST documents.
 *
 * Seema Holidays → customer (only for bookings priced with GST):
 *   advance paid online   → receipt voucher
 *   ride/package complete → tax invoice for the full value (less advance)
 *   admin marks no-show   → tax invoice for the forfeited advance
 *   advance refunded      → refund voucher
 *
 * OfinIT → Seema Holidays: monthly platform-fee tax invoice (+ credit notes
 * when previously invoiced bookings are later refunded).
 *
 * Advertising: Seema Holidays → advertiser tax invoice when an ad is paid
 * (credit note on refund), and a monthly OfinIT → Seema Holidays "ad
 * platform & operations" invoice for OfinIT's share.
 *
 * Every method is idempotent per booking/period. If the issuing business
 * profile is incomplete (e.g. no GSTIN yet), the document is saved as a draft
 * with no number and can be issued from Admin → Invoices once fixed; customer
 * payments and trip actions are never blocked by invoicing.
 */
class InvoiceService
{
    public function receiptVoucher(BookingDetail $booking, ?Payment $payment = null): ?Invoice
    {
        if (!$booking->hasGst() || $this->existing($booking, Invoice::RECEIPT_VOUCHER)) {
            return null;
        }

        $advance = $this->paise($payment?->amount ?? $booking->part_payment);
        if ($advance <= 0) {
            return null;
        }

        [$taxable, $cgst, $sgst] = $this->splitInclusive($advance, (float) $booking->gst_rate);

        return $this->createCustomerDocument($booking, Invoice::RECEIPT_VOUCHER, $taxable, $cgst, $sgst, [
            'description' => 'Advance received for ' . $this->serviceDescription($booking),
            'transaction_id' => $payment?->transaction_id,
            'payment_gateway' => $payment?->payment_gateway,
        ]);
    }

    public function completionInvoice(BookingDetail $booking): ?Invoice
    {
        if (!$booking->hasGst() || $this->existing($booking, Invoice::TAX_INVOICE)) {
            return null;
        }

        $rv = $this->existing($booking, Invoice::RECEIPT_VOUCHER);
        $advance = (float) $booking->part_payment;

        return $this->createCustomerDocument(
            $booking,
            Invoice::TAX_INVOICE,
            $this->paise($booking->taxable_amount),
            $this->paise($booking->cgst_amount),
            $this->paise($booking->sgst_amount),
            [
                'kind' => 'completion',
                'description' => $this->serviceDescription($booking),
                'advance_received' => $advance,
                'receipt_voucher' => $rv?->number,
                'balance_collected_by_driver' => round((float) $booking->total_payment - $advance, 2),
            ],
            $rv,
        );
    }

    /** Tax invoice for the advance retained when an admin marks a no-show. */
    public function noShowInvoice(BookingDetail $booking): ?Invoice
    {
        if (!$booking->hasGst() || $this->existing($booking, Invoice::TAX_INVOICE)) {
            return null;
        }

        $retained = $this->paise($booking->part_payment);
        [$taxable, $cgst, $sgst] = $this->splitInclusive($retained, (float) $booking->gst_rate);
        $rv = $this->existing($booking, Invoice::RECEIPT_VOUCHER);

        return $this->createCustomerDocument($booking, Invoice::TAX_INVOICE, $taxable, $cgst, $sgst, [
            'kind' => 'no_show',
            'description' => 'No-show / cancellation charge (advance forfeited) for ' . $this->serviceDescription($booking),
            'advance_received' => (float) $booking->part_payment,
            'receipt_voucher' => $rv?->number,
            'reason' => $booking->no_show_reason,
        ], $rv);
    }

    public function refundVoucher(BookingDetail $booking, float $refundedAmount): ?Invoice
    {
        $rv = $this->existing($booking, Invoice::RECEIPT_VOUCHER);
        if (!$booking->hasGst() || !$rv || $this->existing($booking, Invoice::REFUND_VOUCHER)) {
            return null;
        }

        $amount = min($this->paise($refundedAmount), $this->paise($rv->total));
        [$taxable, $cgst, $sgst] = $this->splitInclusive($amount, (float) $booking->gst_rate);

        return $this->createCustomerDocument($booking, Invoice::REFUND_VOUCHER, $taxable, $cgst, $sgst, [
            'description' => 'Refund of advance for ' . $this->serviceDescription($booking),
            'receipt_voucher' => $rv->number,
        ], $rv);
    }

    /** Reverse an issued (or draft) document, e.g. when a no-show is undone. */
    public function creditNote(Invoice $original, string $reason): Invoice
    {
        if (!$original->isIssued()) {
            $original->lines()->delete();
            $original->delete();

            return $original;
        }

        return DB::transaction(function () use ($original, $reason) {
            $note = Invoice::create([
                'type' => Invoice::CREDIT_NOTE,
                'status' => Invoice::DRAFT,
                'booking_id' => $original->booking_id,
                'related_invoice_id' => $original->id,
                'supplier' => $original->supplier,
                'recipient' => $original->recipient,
                'is_b2b' => $original->is_b2b,
                'place_of_supply' => $original->place_of_supply,
                'sac_code' => $original->sac_code,
                'taxable_value' => $original->taxable_value,
                'cgst_rate' => $original->cgst_rate,
                'cgst_amount' => $original->cgst_amount,
                'sgst_rate' => $original->sgst_rate,
                'sgst_amount' => $original->sgst_amount,
                'igst_rate' => $original->igst_rate,
                'igst_amount' => $original->igst_amount,
                'total' => $original->total,
                'meta' => ['description' => 'Credit note against ' . $original->number, 'reason' => $reason],
                'created_by' => auth()->id(),
            ]);
            $note->lines()->create([
                'booking_id' => $original->booking_id,
                'description' => 'Reversal of ' . $original->label() . ' ' . $original->number . ' — ' . $reason,
                'sac_code' => $original->sac_code,
                'taxable_value' => $original->taxable_value,
            ]);

            return $this->issue($note, $this->profileFor($original));
        });
    }

    /**
     * Builds (or rebuilds) the draft OfinIT → Seema Holidays platform-fee
     * invoice for a calendar month, from completed rides/packages and
     * admin-marked no-shows picked up in that month. Refunded bookings that
     * were billed in an earlier issued invoice get a credit-note draft.
     *
     * @return array{invoice: ?Invoice, credit_note: ?Invoice, bookings: int}
     */
    public function platformFeeDraft(Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $settings = PricingSettings::load();
        $rate = (float) $settings[PricingSettings::GST_RATE_PLATFORM_FEE];
        $sac = trim((string) $settings[PricingSettings::GST_SAC_PLATFORM_FEE]) ?: null;

        $alreadyIssued = Invoice::where('type', Invoice::PLATFORM_FEE)->where('status', Invoice::ISSUED)
            ->whereDate('period_start', $start)->first();
        if ($alreadyIssued) {
            throw new RuntimeException("The platform-fee invoice for {$start->format('F Y')} is already issued ({$alreadyIssued->number}).");
        }

        $billedBookingIds = DB::table('invoice_lines')
            ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->where('invoices.type', Invoice::PLATFORM_FEE)->where('invoices.status', Invoice::ISSUED)
            ->whereNotNull('invoice_lines.booking_id')
            ->pluck('invoice_lines.booking_id')->all();

        $bookings = BookingDetail::where('payment_status', Type::ACTIVE)
            ->whereBetween('pickup_date', [$start->toDateString(), $end->toDateString()])
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('status', BookingEnum::COMPLETE)->whereNull('refund');
                })->orWhere('status', BookingEnum::NO_SHOW);
            })
            ->whereNotIn('id', $billedBookingIds)
            ->orderBy('pickup_date')->orderBy('id')
            ->get();

        return DB::transaction(function () use ($start, $end, $rate, $sac, $bookings, $billedBookingIds) {
            Invoice::where('type', Invoice::PLATFORM_FEE)->where('status', Invoice::DRAFT)
                ->whereDate('period_start', $start)->get()->each(function (Invoice $draft) {
                    $draft->lines()->delete();
                    $draft->delete();
                });

            $platform = BusinessProfile::platform();
            $supplier = BusinessProfile::supplier();

            $invoice = null;
            if ($bookings->isNotEmpty()) {
                $invoice = $this->createPlatformDocument(Invoice::PLATFORM_FEE, $platform, $supplier, $start, $end, $rate, $sac,
                    'OfinIT platform fee for ' . $start->format('F Y'));
                $taxable = 0;
                foreach ($bookings as $booking) {
                    [$base, $percent, $fee] = $this->platformFeeFor($booking);
                    $taxable += $fee;
                    $invoice->lines()->create([
                        'booking_id' => $booking->id,
                        'description' => 'Platform fee — ' . $booking->booking_id . ' (' . $booking->pickup_date . ($booking->status == BookingEnum::NO_SHOW ? ', no-show' : '') . ')',
                        'sac_code' => $sac,
                        'rate_percent' => $percent,
                        'base_amount' => $base / 100,
                        'taxable_value' => $fee / 100,
                    ]);
                }
                $this->applyTotals($invoice, $taxable, $rate);
            }

            // Bookings billed in an earlier issued invoice that have since been refunded.
            $reversed = BookingDetail::whereIn('id', $billedBookingIds)
                ->where(function ($q) {
                    $q->whereNotNull('refund')->orWhere('status', BookingEnum::CANCEL);
                })->get();
            $alreadyCredited = DB::table('invoice_lines')
                ->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
                ->where('invoices.type', Invoice::CREDIT_NOTE)->whereNotNull('invoice_lines.booking_id')
                ->whereRaw("JSON_EXTRACT(invoices.meta, '$.platform') = true")
                ->pluck('invoice_lines.booking_id')->all();
            $reversed = $reversed->reject(fn ($b) => in_array($b->id, $alreadyCredited));

            $creditNote = null;
            if ($reversed->isNotEmpty()) {
                $creditNote = $this->createPlatformDocument(Invoice::CREDIT_NOTE, $platform, $supplier, $start, $end, $rate, $sac,
                    'Credit note — platform fee on bookings refunded after invoicing', ['platform' => true]);
                $taxable = 0;
                foreach ($reversed as $booking) {
                    $line = DB::table('invoice_lines')->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
                        ->where('invoices.type', Invoice::PLATFORM_FEE)->where('invoice_lines.booking_id', $booking->id)
                        ->select('invoice_lines.*', 'invoices.number', 'invoices.id as original_invoice_id')->first();
                    $fee = $this->paise($line->taxable_value);
                    $taxable += $fee;
                    $creditNote->related_invoice_id = $creditNote->related_invoice_id ?? $line->original_invoice_id;
                    $creditNote->lines()->create([
                        'booking_id' => $booking->id,
                        'description' => 'Reversal — ' . $booking->booking_id . ' (billed in ' . $line->number . ', refunded)',
                        'sac_code' => $sac,
                        'rate_percent' => $line->rate_percent,
                        'base_amount' => $line->base_amount,
                        'taxable_value' => $line->taxable_value,
                    ]);
                }
                $this->applyTotals($creditNote, $taxable, $rate);
            }

            return ['invoice' => $invoice, 'credit_note' => $creditNote, 'bookings' => $bookings->count()];
        });
    }

    /** Assigns the next number and locks the document. */
    public function issue(Invoice $invoice, ?BusinessProfile $issuer = null): Invoice
    {
        if ($invoice->isIssued()) {
            return $invoice;
        }

        $issuer = $issuer ?? $this->profileFor($invoice);
        if (!$issuer || !$issuer->isReadyForInvoicing()) {
            $missing = $issuer ? implode(', ', $issuer->missingForInvoicing()) : 'business profile';
            throw new RuntimeException("Complete the {$issuer?->legal_name} business profile first ({$missing}).");
        }

        return DB::transaction(function () use ($invoice, $issuer) {
            $invoice->refresh();
            if ($invoice->isIssued()) {
                return $invoice;
            }

            $issueDate = now('Asia/Kolkata');
            $invoice->supplier = $issuer->snapshot();
            if (in_array($invoice->type, [Invoice::PLATFORM_FEE, Invoice::AD_PLATFORM_FEE], true) || ($invoice->type === Invoice::CREDIT_NOTE && ($invoice->meta['platform'] ?? false))) {
                $invoice->recipient = $this->platformRecipient();
            }
            $invoice->number = $this->nextNumber($issuer->invoice_prefix, Invoice::SERIES[$invoice->type], $issueDate);
            $invoice->issue_date = $issueDate->toDateString();
            $invoice->issued_at = now();
            $invoice->status = Invoice::ISSUED;
            $invoice->save();

            return $invoice;
        });
    }

    public function nextNumber(string $prefix, string $series, Carbon $date): string
    {
        $fy = self::financialYear($date);
        $key = strtoupper($prefix) . '-' . $series;

        return DB::transaction(function () use ($key, $fy, $prefix, $series) {
            $row = DB::table('invoice_sequences')->where('series', $key)->where('financial_year', $fy)->lockForUpdate()->first();
            if (!$row) {
                DB::table('invoice_sequences')->insertOrIgnore([
                    'series' => $key, 'financial_year' => $fy, 'last_number' => 0,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $row = DB::table('invoice_sequences')->where('series', $key)->where('financial_year', $fy)->lockForUpdate()->first();
            }
            $next = $row->last_number + 1;
            DB::table('invoice_sequences')->where('id', $row->id)->update(['last_number' => $next, 'updated_at' => now()]);

            return strtoupper($prefix) . '/' . $fy . '/' . $series . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
        });
    }

    /** Indian financial year label, e.g. 2026-10-09 → "26-27". */
    public static function financialYear(Carbon $date): string
    {
        $startYear = $date->month >= 4 ? $date->year : $date->year - 1;

        return sprintf('%02d-%02d', $startYear % 100, ($startYear + 1) % 100);
    }

    /** Splits a GST-inclusive amount (paise) into [taxable, cgst, sgst]. */
    public function splitInclusive(int $inclusive, float $rate): array
    {
        $taxable = (int) round($inclusive * 100 / (100 + $rate));
        $tax = $inclusive - $taxable;
        $cgst = intdiv($tax, 2);

        return [$taxable, $cgst, $tax - $cgst];
    }

    // ------------------------------------------------------------------
    // Advertising
    // ------------------------------------------------------------------

    /** Seema Holidays → advertiser tax invoice for a paid ad (idempotent). */
    public function adInvoice(AdCampaign $campaign): ?Invoice
    {
        if (!$campaign->isPaid() || $this->existingAd($campaign, Invoice::AD_INVOICE)) {
            return null;
        }
        $campaign->loadMissing('items.placement', 'advertiser');
        $supplier = BusinessProfile::supplier();
        $recipient = $this->advertiserRecipient($campaign);
        $sac = trim((string) AdSettings::get(AdSettings::SAC_SEEMA)) ?: null;
        $rate = (float) $campaign->gst_rate;

        $invoice = DB::transaction(function () use ($campaign, $supplier, $recipient, $sac, $rate) {
            $invoice = Invoice::create(array_merge([
                'type' => Invoice::AD_INVOICE,
                'status' => Invoice::DRAFT,
                'ad_campaign_id' => $campaign->id,
                'supplier' => $supplier?->snapshot() ?? [],
                'recipient' => $recipient,
                'is_b2b' => !empty($recipient['gstin']),
                'place_of_supply' => $recipient['state_code'] ?: ($supplier?->state_code ?: '30'),
                'sac_code' => $sac,
                'meta' => [
                    'description' => 'Advertising space in the Seema Cabs Goa app — ' . $campaign->reference,
                    'campaign' => $campaign->reference,
                    'period' => $campaign->start_date->format('d-m-Y') . ' to ' . $campaign->end_date->format('d-m-Y'),
                    'transaction_id' => $campaign->transaction_id,
                    'payment_gateway' => $campaign->payment_gateway,
                ],
                'created_by' => auth()->id(),
            ], $this->adTaxColumns((int) $campaign->net_amount, $rate, (bool) $campaign->inter_state)));

            // Spread the discount over the lines so they add up to the net amount.
            $remaining = (int) $campaign->net_amount;
            $items = $campaign->items->values();
            foreach ($items as $i => $item) {
                $taxable = $i === $items->count() - 1
                    ? $remaining
                    : (int) round($item->subtotal * $campaign->net_amount / max(1, $campaign->list_amount));
                $remaining -= $taxable;
                $invoice->lines()->create([
                    'ad_campaign_id' => $campaign->id,
                    'description' => 'Advertising — ' . $item->placement->code . ' ' . $item->placement->name . ', ' . $item->days . ' days from ' . $campaign->start_date->format('d-m-Y')
                        . ($campaign->discount_percent > 0 ? ' (incl. ' . rtrim(rtrim(number_format((float) $campaign->discount_percent, 2), '0'), '.') . '% discount)' : ''),
                    'sac_code' => $sac,
                    'rate_percent' => $rate,
                    'base_amount' => $item->subtotal / 100,
                    'taxable_value' => $taxable / 100,
                ]);
            }

            return $invoice;
        });

        try {
            return $this->issue($invoice, $supplier);
        } catch (RuntimeException $e) {
            Log::warning("Ad invoice {$invoice->id} ({$campaign->reference}) saved as draft: " . $e->getMessage());

            return $invoice;
        }
    }

    /** Credit note against an ad invoice for a refund (amount incl. GST, paise). */
    public function adCreditNote(AdCampaign $campaign, int $refundedInclusive, string $reason): ?Invoice
    {
        $original = $this->existingAd($campaign, Invoice::AD_INVOICE);
        if (!$original || $refundedInclusive <= 0) {
            return null;
        }
        $rate = (float) $campaign->gst_rate;
        $taxable = min((int) round($refundedInclusive * 100 / (100 + $rate)), $this->paise($original->taxable_value));

        $note = DB::transaction(function () use ($campaign, $original, $taxable, $rate, $reason) {
            $note = Invoice::create(array_merge([
                'type' => Invoice::CREDIT_NOTE,
                'status' => Invoice::DRAFT,
                'ad_campaign_id' => $campaign->id,
                'related_invoice_id' => $original->id,
                'supplier' => $original->supplier,
                'recipient' => $original->recipient,
                'is_b2b' => $original->is_b2b,
                'place_of_supply' => $original->place_of_supply,
                'sac_code' => $original->sac_code,
                'meta' => ['description' => 'Credit note against ' . ($original->number ?? 'draft ad invoice'), 'reason' => $reason, 'campaign' => $campaign->reference],
                'created_by' => auth()->id(),
            ], $this->adTaxColumns($taxable, $rate, (bool) $campaign->inter_state)));
            $note->lines()->create([
                'ad_campaign_id' => $campaign->id,
                'description' => 'Refund — advertising ' . $campaign->reference . ' — ' . $reason,
                'sac_code' => $original->sac_code,
                'rate_percent' => $rate,
                'taxable_value' => $taxable / 100,
            ]);

            return $note;
        });

        try {
            return $this->issue($note, BusinessProfile::supplier());
        } catch (RuntimeException $e) {
            Log::warning("Ad credit note {$note->id} ({$campaign->reference}) saved as draft: " . $e->getMessage());

            return $note;
        }
    }

    /**
     * Draft OfinIT → Seema Holidays "ad platform & operations" invoice for a
     * month: OfinIT's share of every ad paid in that month (net of refunds
     * made before invoicing), plus a credit-note draft for ads billed earlier
     * and refunded since.
     *
     * @return array{invoice: ?Invoice, credit_note: ?Invoice, campaigns: int}
     */
    public function adPlatformDraft(Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $rate = (float) AdSettings::get(AdSettings::GST_RATE);
        $sac = trim((string) AdSettings::get(AdSettings::SAC_OFINIT)) ?: null;

        $alreadyIssued = Invoice::where('type', Invoice::AD_PLATFORM_FEE)->where('status', Invoice::ISSUED)
            ->whereDate('period_start', $start)->first();
        if ($alreadyIssued) {
            throw new RuntimeException("The ad platform invoice for {$start->format('F Y')} is already issued ({$alreadyIssued->number}).");
        }

        $billed = DB::table('invoice_lines')->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
            ->where('invoices.type', Invoice::AD_PLATFORM_FEE)->where('invoices.status', Invoice::ISSUED)
            ->whereNotNull('invoice_lines.ad_campaign_id')
            ->select('invoice_lines.*', 'invoices.number', 'invoices.id as original_invoice_id')->get()->keyBy('ad_campaign_id');

        // Month boundaries in IST, compared against paid_at (stored in app time).
        $from = Carbon::parse($start->format('Y-m-d') . ' 00:00:00', 'Asia/Kolkata')->timezone(config('app.timezone'));
        $to = Carbon::parse($end->format('Y-m-d') . ' 23:59:59', 'Asia/Kolkata')->timezone(config('app.timezone'));
        $campaigns = AdCampaign::whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->whereNotIn('id', $billed->keys())
            ->orderBy('paid_at')->get()
            ->map(fn (AdCampaign $c) => [$c, $this->ofinitShareAfterRefunds($c)])
            ->filter(fn ($row) => $row[1] > 0)->values();

        return DB::transaction(function () use ($start, $end, $rate, $sac, $campaigns, $billed) {
            Invoice::whereIn('type', [Invoice::AD_PLATFORM_FEE, Invoice::CREDIT_NOTE])->where('status', Invoice::DRAFT)
                ->whereDate('period_start', $start)
                ->where(fn ($q) => $q->where('type', Invoice::AD_PLATFORM_FEE)->orWhereRaw("JSON_EXTRACT(meta, '$.ads') = true"))
                ->get()->each(function (Invoice $draft) {
                    $draft->lines()->delete();
                    $draft->delete();
                });

            $platform = BusinessProfile::platform();
            $supplier = BusinessProfile::supplier();

            $invoice = null;
            if ($campaigns->isNotEmpty()) {
                $invoice = $this->createPlatformDocument(Invoice::AD_PLATFORM_FEE, $platform, $supplier, $start, $end, $rate, $sac,
                    'Ad platform & operations for ' . $start->format('F Y'), ['ads' => true]);
                $taxable = 0;
                foreach ($campaigns as [$campaign, $share]) {
                    $taxable += $share;
                    $invoice->lines()->create([
                        'ad_campaign_id' => $campaign->id,
                        'description' => 'Ad platform & operations — ' . $campaign->reference . ' (paid ' . $campaign->paid_at->timezone('Asia/Kolkata')->format('d-m-Y')
                            . ', ' . ($campaign->transfer_reference ? 'collected by split' : 'payable') . ')',
                        'sac_code' => $sac,
                        'rate_percent' => round(100 - (float) $campaign->seema_percent, 2),
                        'base_amount' => $campaign->net_amount / 100,
                        'taxable_value' => $share / 100,
                    ]);
                }
                $this->applyTotals($invoice, $taxable, $rate);
            }

            // Ads billed in an earlier issued invoice and refunded since.
            $credited = DB::table('invoice_lines')->join('invoices', 'invoices.id', '=', 'invoice_lines.invoice_id')
                ->where('invoices.type', Invoice::CREDIT_NOTE)->whereRaw("JSON_EXTRACT(invoices.meta, '$.ads') = true")
                ->whereNotNull('invoice_lines.ad_campaign_id')
                ->groupBy('invoice_lines.ad_campaign_id')
                ->selectRaw('invoice_lines.ad_campaign_id, SUM(invoice_lines.taxable_value) as credited')
                ->pluck('credited', 'ad_campaign_id');
            $reversals = AdCampaign::whereIn('id', $billed->keys())->where('refund_amount', '>', 0)->get()
                ->map(function (AdCampaign $c) use ($billed, $credited) {
                    $billedShare = $this->paise($billed[$c->id]->taxable_value);
                    $due = $billedShare - $this->ofinitShareAfterRefunds($c) - $this->paise($credited[$c->id] ?? 0);

                    return [$c, max(0, min($due, $billedShare)), $billed[$c->id]];
                })->filter(fn ($row) => $row[1] > 0)->values();

            $creditNote = null;
            if ($reversals->isNotEmpty()) {
                $creditNote = $this->createPlatformDocument(Invoice::CREDIT_NOTE, $platform, $supplier, $start, $end, $rate, $sac,
                    'Credit note — ad platform share on ads refunded after invoicing', ['platform' => true, 'ads' => true]);
                $taxable = 0;
                foreach ($reversals as [$campaign, $amount, $line]) {
                    $taxable += $amount;
                    $creditNote->related_invoice_id = $creditNote->related_invoice_id ?? $line->original_invoice_id;
                    $creditNote->lines()->create([
                        'ad_campaign_id' => $campaign->id,
                        'description' => 'Reversal — ' . $campaign->reference . ' (billed in ' . $line->number . ', refunded)',
                        'sac_code' => $sac,
                        'taxable_value' => $amount / 100,
                    ]);
                }
                $this->applyTotals($creditNote, $taxable, $rate);
            }

            return ['invoice' => $invoice, 'credit_note' => $creditNote, 'campaigns' => $campaigns->count()];
        });
    }

    /** OfinIT's share (before GST, paise) after any refunds, pro rata. */
    private function ofinitShareAfterRefunds(AdCampaign $campaign): int
    {
        $kept = max(0, (int) $campaign->total_amount - (int) $campaign->refund_amount);

        return (int) round(((int) $campaign->ofinit_amount) * $kept / max(1, (int) $campaign->total_amount));
    }

    private function adTaxColumns(int $taxable, float $rate, bool $interState): array
    {
        $tax = (int) round($taxable * $rate / 100);
        $cgst = $interState ? 0 : intdiv($tax, 2);
        $sgst = $interState ? 0 : $tax - $cgst;

        return [
            'taxable_value' => $taxable / 100,
            'cgst_rate' => $interState ? 0 : $rate / 2,
            'cgst_amount' => $cgst / 100,
            'sgst_rate' => $interState ? 0 : $rate / 2,
            'sgst_amount' => $sgst / 100,
            'igst_rate' => $interState ? $rate : 0,
            'igst_amount' => $interState ? $tax / 100 : 0,
            'total' => ($taxable + $tax) / 100,
        ];
    }

    private function advertiserRecipient(AdCampaign $campaign): array
    {
        $advertiser = $campaign->advertiser;
        $gstin = Gstin::isValid($advertiser->gstin) ? Gstin::normalize($advertiser->gstin) : null;

        return [
            'name' => $gstin && $advertiser->legal_name ? $advertiser->legal_name : $advertiser->business_name,
            'gstin' => $gstin,
            'state_code' => $gstin ? Gstin::stateCode($gstin) : null,
            'address' => $advertiser->billing_address ? [$advertiser->billing_address] : [],
            'email' => $advertiser->email,
            'phone' => $advertiser->phone,
            'customer_id' => $campaign->user_id,
        ];
    }

    private function existingAd(AdCampaign $campaign, string $type): ?Invoice
    {
        return Invoice::where('ad_campaign_id', $campaign->id)->where('type', $type)->latest('id')->first();
    }


    // ------------------------------------------------------------------

    private function createCustomerDocument(BookingDetail $booking, string $type, int $taxable, int $cgst, int $sgst, array $meta, ?Invoice $related = null): Invoice
    {
        $supplier = BusinessProfile::supplier();
        $recipient = $this->customerRecipient($booking);
        $rate = (float) $booking->gst_rate;

        $invoice = DB::transaction(function () use ($booking, $type, $taxable, $cgst, $sgst, $meta, $related, $supplier, $recipient, $rate) {
            $invoice = Invoice::create([
                'type' => $type,
                'status' => Invoice::DRAFT,
                'booking_id' => $booking->id,
                'related_invoice_id' => $related?->id,
                'supplier' => $supplier?->snapshot() ?? [],
                'recipient' => $recipient,
                'is_b2b' => !empty($recipient['gstin']),
                'place_of_supply' => $supplier?->state_code ?: '30',
                'sac_code' => $booking->sac_code,
                'taxable_value' => $taxable / 100,
                'cgst_rate' => $rate / 2,
                'cgst_amount' => $cgst / 100,
                'sgst_rate' => $rate / 2,
                'sgst_amount' => $sgst / 100,
                'total' => ($taxable + $cgst + $sgst) / 100,
                'meta' => $meta,
                'created_by' => auth()->id(),
            ]);
            $invoice->lines()->create([
                'booking_id' => $booking->id,
                'description' => $meta['description'],
                'sac_code' => $booking->sac_code,
                'rate_percent' => $rate,
                'taxable_value' => $taxable / 100,
            ]);

            return $invoice;
        });

        try {
            return $this->issue($invoice, $supplier);
        } catch (RuntimeException $e) {
            Log::warning("Invoice {$invoice->id} ({$type}, booking {$booking->booking_id}) saved as draft: " . $e->getMessage());

            return $invoice;
        }
    }

    private function createPlatformDocument(string $type, ?BusinessProfile $platform, ?BusinessProfile $supplier, Carbon $start, Carbon $end, float $rate, ?string $sac, string $description, array $meta = []): Invoice
    {
        return Invoice::create([
            'type' => $type,
            'status' => Invoice::DRAFT,
            'supplier' => $platform?->snapshot() ?? [],
            'recipient' => $this->platformRecipient($supplier),
            'is_b2b' => true,
            'place_of_supply' => $supplier?->state_code ?: '30',
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'sac_code' => $sac,
            'meta' => array_merge(['description' => $description], $meta),
            'created_by' => auth()->id(),
        ]);
    }

    private function applyTotals(Invoice $invoice, int $taxable, float $rate): void
    {
        $tax = (int) round($taxable * $rate / 100);
        $interState = ($invoice->supplier['state_code'] ?? '30') !== ($invoice->place_of_supply ?? '30');
        $cgst = $interState ? 0 : intdiv($tax, 2);
        $sgst = $interState ? 0 : $tax - $cgst;

        $invoice->fill([
            'taxable_value' => $taxable / 100,
            'cgst_rate' => $interState ? 0 : $rate / 2,
            'cgst_amount' => $cgst / 100,
            'sgst_rate' => $interState ? 0 : $rate / 2,
            'sgst_amount' => $sgst / 100,
            'igst_rate' => $interState ? $rate : 0,
            'igst_amount' => $interState ? $tax / 100 : 0,
            'total' => ($taxable + $tax) / 100,
        ])->save();
    }

    /** [base (paise), percent, fee (paise)] for one booking. */
    private function platformFeeFor(BookingDetail $booking): array
    {
        if ($booking->platform_fee_amount !== null) {
            // v3: fee is a % of the fare incl. markup; v2: of the fare before markup.
            $base = (int) $booking->pricing_version >= 3 ? $booking->base_fare : ($booking->fare_before_markup ?? $booking->base_fare);

            return [
                $this->paise($base),
                (float) $booking->platform_fee_percent,
                $this->paise($booking->platform_fee_amount),
            ];
        }

        // v1 bookings: the aggregator commission recorded in the payment settlement.
        $settlement = json_decode((string) Payment::where('booking_id', $booking->id)->value('amount_settlement'), true) ?: [];
        $fee = $this->paise(str_replace(',', '', (string) ($settlement['company_commission'] ?? 0)));
        $base = $this->paise($booking->base_fare);

        return [$base, $base > 0 ? round($fee * 100 / $base, 2) : 0.0, $fee];
    }

    private function customerRecipient(BookingDetail $booking): array
    {
        $user = User::find($booking->customer_id);
        $gstin = Gstin::isValid($booking->customer_gstin) ? Gstin::normalize($booking->customer_gstin) : null;

        return [
            'name' => $gstin && $booking->customer_legal_name ? $booking->customer_legal_name : ($user?->name ?? 'Customer'),
            'gstin' => $gstin,
            'state_code' => $gstin ? Gstin::stateCode($gstin) : null,
            'address' => $gstin && $booking->customer_billing_address ? [$booking->customer_billing_address] : [],
            'email' => $user?->email,
            'phone' => $user?->phone_number,
            'customer_id' => $booking->customer_id,
        ];
    }

    private function platformRecipient(?BusinessProfile $supplier = null): array
    {
        $supplier = $supplier ?? BusinessProfile::supplier();

        return [
            'name' => $supplier?->legal_name ?? 'Seema Holidays',
            'gstin' => $supplier?->gstin,
            'state_code' => $supplier?->state_code,
            'address' => $supplier?->addressLines() ?? [],
            'email' => $supplier?->email,
            'phone' => $supplier?->phone,
        ];
    }

    private function profileFor(Invoice $invoice): ?BusinessProfile
    {
        $isPlatform = in_array($invoice->type, [Invoice::PLATFORM_FEE, Invoice::AD_PLATFORM_FEE], true)
            || ($invoice->type === Invoice::CREDIT_NOTE && ($invoice->meta['platform'] ?? false));

        return $isPlatform ? BusinessProfile::platform() : BusinessProfile::supplier();
    }

    /** Latest document of a type for the booking, ignoring ones reversed by a credit note. */
    private function existing(BookingDetail $booking, string $type): ?Invoice
    {
        $credited = Invoice::where('type', Invoice::CREDIT_NOTE)->where('booking_id', $booking->id)
            ->whereNotNull('related_invoice_id')->pluck('related_invoice_id');

        return Invoice::where('booking_id', $booking->id)->where('type', $type)
            ->whereNotIn('id', $credited)->latest('id')->first();
    }

    private function serviceDescription(BookingDetail $booking): string
    {
        $what = $booking->sight_seeing_package_id ? 'Sightseeing package' : 'Cab ride';

        return "{$what} {$booking->booking_id} on {$booking->pickup_date}";
    }

    private function paise(mixed $rupees): int
    {
        return (int) round(((float) $rupees) * 100);
    }
}
