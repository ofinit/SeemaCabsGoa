<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fleet-operator payout tracking (Razorpay Route transfer / Cashfree split):
 * the gateway reference proves a payout happened and makes retries
 * idempotent; the error explains a failure in the admin Payouts screen.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('transfer_reference', 100)->nullable()->after('transfer_amount_status');
            $table->decimal('transfer_amount', 10, 2)->nullable()->after('transfer_reference');
            $table->string('transfer_error', 500)->nullable()->after('transfer_amount');
            $table->timestamp('transferred_at')->nullable()->after('transfer_error');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['transfer_reference', 'transfer_amount', 'transfer_error', 'transferred_at']);
        });
    }
};
