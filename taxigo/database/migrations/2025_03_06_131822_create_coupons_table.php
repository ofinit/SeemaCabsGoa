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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->boolean('type')->nullable();
            $table->string('value')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('no_expiry')->nullable();
            $table->string('description')->nullable();
            $table->boolean('status')->nullable();
            $table->boolean('show_app')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
