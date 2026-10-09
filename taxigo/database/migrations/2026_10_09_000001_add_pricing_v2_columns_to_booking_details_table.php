<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pricing v2: the fare/markup/GST split, the platform fee and the optional
 * business GSTIN are snapshotted on every booking at booking time, so later
 * setting changes never alter an existing booking or its invoices.
 *
 * Existing (v1) bookings: their `tax_amount` was the 20% markup labelled as
 * tax. It is copied into `markup_amount` for reporting; `tax_amount` itself is
 * left untouched (GST is never applied retroactively).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('pricing_version')->default(1)->after('tax_amount');
            $table->decimal('fare_before_markup', 10, 2)->nullable()->after('pricing_version');
            $table->decimal('markup_percent', 6, 2)->nullable()->after('fare_before_markup');
            $table->decimal('markup_amount', 10, 2)->nullable()->after('markup_percent');
            $table->decimal('taxable_amount', 10, 2)->nullable()->after('markup_amount');
            $table->decimal('gst_rate', 5, 2)->nullable()->after('taxable_amount');
            $table->decimal('cgst_amount', 10, 2)->nullable()->after('gst_rate');
            $table->decimal('sgst_amount', 10, 2)->nullable()->after('cgst_amount');
            $table->decimal('igst_amount', 10, 2)->nullable()->after('sgst_amount');
            $table->string('sac_code', 10)->nullable()->after('igst_amount');
            $table->decimal('platform_fee_percent', 6, 2)->nullable()->after('sac_code');
            $table->decimal('platform_fee_amount', 10, 2)->nullable()->after('platform_fee_percent');
            $table->string('customer_gstin', 15)->nullable()->after('platform_fee_amount');
            $table->string('customer_legal_name')->nullable()->after('customer_gstin');
            $table->string('customer_billing_address', 500)->nullable()->after('customer_legal_name');
            $table->dateTime('no_show_at')->nullable()->after('customer_billing_address');
            $table->string('no_show_reason')->nullable()->after('no_show_at');
            $table->unsignedBigInteger('no_show_by')->nullable()->after('no_show_reason');
        });

        DB::table('booking_details')->update([
            'markup_amount' => DB::raw('tax_amount'),
            'fare_before_markup' => DB::raw('base_fare'),
        ]);
    }

    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn([
                'pricing_version', 'fare_before_markup', 'markup_percent', 'markup_amount',
                'taxable_amount', 'gst_rate', 'cgst_amount', 'sgst_amount', 'igst_amount',
                'sac_code', 'platform_fee_percent', 'platform_fee_amount', 'customer_gstin',
                'customer_legal_name', 'customer_billing_address', 'no_show_at',
                'no_show_reason', 'no_show_by',
            ]);
        });
    }
};
