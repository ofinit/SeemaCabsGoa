<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fleet_operator_bank_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fleet_perator_id')->index(); 
            $table->foreign('fleet_perator_id')->references('id')->on('fleet_operators')->onDelete('cascade'); 
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet_operator_bank_details');
    }
};
