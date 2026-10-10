<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ad payment safety: remember every gateway order created for a campaign
 * (so a payment on an older order is never lost and extra payments are
 * refunded), and the refund still owed when a refund attempt fails.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->json('pg_orders')->nullable()->after('pg_order_id');
            $table->unsignedBigInteger('refund_due')->default(0)->after('refund_amount');
            $table->string('refund_reason', 100)->nullable()->after('refund_due');
        });
    }

    public function down(): void
    {
        Schema::table('ad_campaigns', fn (Blueprint $table) => $table->dropColumn(['pg_orders', 'refund_due', 'refund_reason']));
    }
};
