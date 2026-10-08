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
        Schema::create('cabs', function (Blueprint $table) {
            $table->id();
            $table->integer('fleet_operator_id')->nullable();
            $table->string('zone')->nullable();
            $table->string('number')->nullable();
            $table->string('type')->nullable();
            $table->string('model')->nullable();
            $table->foreignId('model_id')->nullable()->constrained('cab_models')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('cab_colors')->nullOnDelete();
            $table->integer('no_of_seats')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('base_fare')->nullable();
            $table->string('no_of_kms')->nullable();
            $table->string('additional_km_charges')->nullable();
            $table->string('waiting_charges')->nullable();
            $table->string('front_registration_certificate')->nullable();
            $table->string('back_registration_certificate')->nullable();
            $table->string('insurance')->nullable();
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
        Schema::dropIfExists('cabs');
    }
};
