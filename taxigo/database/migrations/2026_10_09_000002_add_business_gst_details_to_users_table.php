<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional business GST details a customer enters at checkout, remembered for
 * their next booking. Each booking also snapshots them (booking_details).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gstin', 15)->nullable()->after('phone_number');
            $table->string('gst_legal_name')->nullable()->after('gstin');
            $table->string('gst_billing_address', 500)->nullable()->after('gst_legal_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'gst_legal_name', 'gst_billing_address']);
        });
    }
};
