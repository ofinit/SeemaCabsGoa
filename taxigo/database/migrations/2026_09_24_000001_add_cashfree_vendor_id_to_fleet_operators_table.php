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
        Schema::table('fleet_operators', function (Blueprint $table) {
            if (!Schema::hasColumn('fleet_operators', 'cashfree_vendor_id')) {
                $table->string('cashfree_vendor_id')->nullable()->after('razorpay_account');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fleet_operators', function (Blueprint $table) {
            if (Schema::hasColumn('fleet_operators', 'cashfree_vendor_id')) {
                $table->dropColumn('cashfree_vendor_id');
            }
        });
    }
};
