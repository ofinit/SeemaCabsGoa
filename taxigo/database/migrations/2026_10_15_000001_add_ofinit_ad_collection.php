<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ads are sold by OfinIT: advertisers pay through a Razorpay payment link on
 * OfinIT's account (payment_url), Seema's share goes out by Route. Admins can
 * also run complimentary ads with no payment.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->boolean('complimentary')->default(false)->after('status');
            $table->string('payment_url')->nullable()->after('pg_orders');
        });
    }

    public function down(): void
    {
        Schema::table('ad_campaigns', fn (Blueprint $table) => $table->dropColumn(['complimentary', 'payment_url']));
    }
};
