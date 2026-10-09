<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GST documents:
 *   receipt_voucher  — advance received online (Seema Holidays → customer)
 *   tax_invoice      — completed ride/package, or forfeited no-show advance
 *   refund_voucher   — advance refunded
 *   credit_note      — reversal of an issued invoice (e.g. no-show undone)
 *   platform_fee     — monthly OfinIT → Seema Holidays platform fee invoice
 *
 * Issued documents are immutable; party details are snapshotted as JSON.
 * Numbers come from invoice_sequences: gap-free per series per financial year.
 */
return new class extends Migration {
    public function up(): void
    {
        // InnoDB for row locks/transactions. booking_details is MyISAM (no FK
        // support), so booking_id is an indexed id rather than a foreign key.
        Schema::create('invoices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('number', 40)->nullable()->unique();
            $table->string('type', 30)->index();
            $table->string('status', 20)->default('issued')->index();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->foreignId('related_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->json('supplier');
            $table->json('recipient');
            $table->boolean('is_b2b')->default(false);
            $table->string('place_of_supply', 2)->nullable();
            $table->date('issue_date')->nullable()->index();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('sac_code', 10)->nullable();
            $table->decimal('taxable_value', 12, 2)->default(0);
            $table->decimal('cgst_rate', 5, 2)->default(0);
            $table->decimal('cgst_amount', 12, 2)->default(0);
            $table->decimal('sgst_rate', 5, 2)->default(0);
            $table->decimal('sgst_amount', 12, 2)->default(0);
            $table->decimal('igst_rate', 5, 2)->default(0);
            $table->decimal('igst_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'type']);
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->string('description');
            $table->string('sac_code', 10)->nullable();
            $table->decimal('rate_percent', 6, 2)->nullable();
            $table->decimal('base_amount', 12, 2)->nullable();
            $table->decimal('taxable_value', 12, 2);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // Numbering takes a SELECT … FOR UPDATE lock on the series row.
        Schema::create('invoice_sequences', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('series', 30);
            $table->string('financial_year', 9);
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['series', 'financial_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoice_sequences');
        Schema::dropIfExists('invoices');
    }
};
