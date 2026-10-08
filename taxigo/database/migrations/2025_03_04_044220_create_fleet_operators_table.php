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
        Schema::create('fleet_operators', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('person_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_id')->nullable();
            $table->string('mobile_number_two')->nullable();
            $table->string('mobile_number_three')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('zip_code')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('gst_number')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->string('front_side_aadhar')->nullable();
            $table->string('back_side_aadhar')->nullable();
            $table->string('company_license')->nullable();
            $table->string('aggrement')->nullable();
            $table->string('password')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet_operators');
    }
};
