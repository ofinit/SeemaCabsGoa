<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_gateway')) {
                $table->string('payment_gateway', 50)->default('razorpay')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('payments', 'pg_order_id')) {
                $table->string('pg_order_id', 191)->nullable()->after('payment_gateway');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'pg_order_id')) {
                $table->dropColumn('pg_order_id');
            }
            if (Schema::hasColumn('payments', 'payment_gateway')) {
                $table->dropColumn('payment_gateway');
            }
        });
    }
};
