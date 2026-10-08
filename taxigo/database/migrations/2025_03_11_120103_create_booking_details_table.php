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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cab_id')->nullable()->constrained('cabs')->nullOnDelete();
            $table->foreignId('assigned_driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->string('booking_id')->nullable();
            $table->date('booking_date')->nullable();
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->string('trip_type')->nullable();
            $table->string('pickup_from')->nullable();
            $table->string('drop_to')->nullable();
            $table->string('pickup_address')->nullable();
            $table->string('drop_of_address')->nullable();
            $table->integer('cab_type')->nullable();
            $table->integer('cap_model')->nullable();
            $table->integer('trip_otp')->nullable();
            $table->decimal('part_payment', 10, 2)->nullable();
            $table->decimal('base_fare', 10, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('total_payment', 10, 2)->nullable();
            $table->decimal('cash_with_driver', 10, 2)->nullable();
            $table->decimal('refund', 10, 2)->nullable();
            $table->boolean('air_port_drop')->nullable();
            $table->boolean('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
