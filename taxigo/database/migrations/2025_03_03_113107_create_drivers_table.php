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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cab_id')->nullable()->constrained('cabs')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->string('aadhar_card_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('front_license')->nullable();
            $table->string('back_license')->nullable();
            $table->string('front_aadhar_card')->nullable();
            $table->string('back_aadhar_card')->nullable();
            $table->tinyInteger('assignDriver')->default('0')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
