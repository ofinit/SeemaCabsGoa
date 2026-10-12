<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Platform billing (OfinIT → Seema Holidays): settlement method, due dates,
 * amounts due after split collections and credit notes, and the Razorpay
 * payment link (OfinIT's own account) used to pay each invoice.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('issue_date');
            $table->decimal('collected_by_split', 12, 2)->default(0)->after('total');
            $table->decimal('credit_applied', 12, 2)->default(0)->after('collected_by_split');
            $table->decimal('amount_due', 12, 2)->nullable()->after('credit_applied');
            $table->string('payment_link_id', 40)->nullable()->index()->after('paid_at');
            $table->string('payment_link_url')->nullable()->after('payment_link_id');
            $table->string('payment_link_status', 20)->nullable()->after('payment_link_url');
            $table->string('paid_via', 20)->nullable()->after('payment_link_status');
            $table->string('payment_reference', 80)->nullable()->after('paid_via');
            $table->unsignedTinyInteger('notices_sent')->default(0)->after('payment_reference');
        });

        // The gateways no longer allow splits: start on monthly invoicing.
        $now = now();
        foreach (['platform_settlement_mode' => 'invoice', 'platform_auto_suspend' => '1', 'platform_suspended' => '0'] as $title => $value) {
            if (!DB::table('environments')->where('title', $title)->exists()) {
                DB::table('environments')->insert(['title' => $title, 'value' => $value, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('invoices', fn (Blueprint $table) => $table->dropColumn([
            'due_date', 'collected_by_split', 'credit_applied', 'amount_due', 'payment_link_id', 'payment_link_url',
            'payment_link_status', 'paid_via', 'payment_reference', 'notices_sent',
        ]));
        DB::table('environments')->whereIn('title', ['platform_settlement_mode', 'platform_auto_suspend', 'platform_suspended'])->delete();
    }
};
