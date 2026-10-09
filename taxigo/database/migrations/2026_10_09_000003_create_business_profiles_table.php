<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The two legal entities that issue invoices:
 *   supplier — Seema Holidays: supplies rides & packages to customers
 *   platform — OfinIT Solutions Pvt. Ltd.: charges Seema Holidays a platform fee
 *
 * GSTIN, address etc. are entered by an admin (Settings → Business Profiles);
 * invoices cannot be issued until the issuing profile is complete.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('role', 20)->unique();
            $table->string('legal_name');
            $table->string('trade_name')->nullable();
            $table->string('gstin', 15)->nullable();
            $table->string('pan', 10)->nullable();
            $table->string('address_line_one')->nullable();
            $table->string('address_line_two')->nullable();
            $table->string('city')->nullable();
            $table->string('state_name')->nullable();
            $table->string('state_code', 2)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('bank_details', 500)->nullable();
            $table->string('invoice_prefix', 12);
            $table->timestamps();
        });

        $now = now();
        DB::table('business_profiles')->insert([
            [
                'role' => 'supplier',
                'legal_name' => 'Seema Holidays',
                'trade_name' => 'Seema Cabs Goa',
                'address_line_one' => 'H.No. EN2/10, Naika Vaddo',
                'address_line_two' => 'Calangute, Bardez',
                'city' => 'Calangute',
                'state_name' => 'Goa',
                'state_code' => '30',
                'pincode' => '403516',
                'invoice_prefix' => 'SH',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role' => 'platform',
                'legal_name' => 'OfinIT Solutions Pvt. Ltd.',
                'trade_name' => 'OfinIT',
                'address_line_one' => null,
                'address_line_two' => null,
                'city' => null,
                'state_name' => 'Goa',
                'state_code' => '30',
                'pincode' => null,
                'invoice_prefix' => 'OFN',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
