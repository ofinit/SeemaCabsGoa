<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\URL;

class Invoice extends Model
{
    public const RECEIPT_VOUCHER = 'receipt_voucher';
    public const TAX_INVOICE = 'tax_invoice';
    public const REFUND_VOUCHER = 'refund_voucher';
    public const CREDIT_NOTE = 'credit_note';
    public const PLATFORM_FEE = 'platform_fee';

    public const DRAFT = 'draft';
    public const ISSUED = 'issued';

    public const TYPE_LABELS = [
        self::RECEIPT_VOUCHER => 'Receipt Voucher',
        self::TAX_INVOICE => 'Tax Invoice',
        self::REFUND_VOUCHER => 'Refund Voucher',
        self::CREDIT_NOTE => 'Credit Note',
        self::PLATFORM_FEE => 'Tax Invoice (Platform Fee)',
    ];

    /** Number series code per document type. */
    public const SERIES = [
        self::RECEIPT_VOUCHER => 'RV',
        self::TAX_INVOICE => 'INV',
        self::REFUND_VOUCHER => 'RF',
        self::CREDIT_NOTE => 'CN',
        self::PLATFORM_FEE => 'PF',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'supplier' => 'array',
        'recipient' => 'array',
        'meta' => 'array',
        'is_b2b' => 'boolean',
        'issue_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(BookingDetail::class, 'booking_id');
    }

    public function related(): BelongsTo
    {
        return $this->belongsTo(self::class, 'related_invoice_id');
    }

    public function label(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function isIssued(): bool
    {
        return $this->status === self::ISSUED;
    }

    public function taxTotal(): float
    {
        return round((float) $this->cgst_amount + (float) $this->sgst_amount + (float) $this->igst_amount, 2);
    }

    /** Signed, login-free link (for emails and the mobile apps). */
    public function publicUrl(): string
    {
        return URL::signedRoute('invoices.public', ['invoice' => $this->id]);
    }
}
