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
        Schema::create('cab_price_details', function (Blueprint $table) {
            $table->id();
            $table->integer('cab_type');
            $table->integer('base_fare');
            $table->integer('no_of_kms');
            $table->integer('additional_km_charges');
            $table->integer('waiting_charges');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cab_price_details');
    }
};
