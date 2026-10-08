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
        Schema::create('cab_rates', function (Blueprint $table) {
            $table->id();
            $table->boolean('tab')->nullable();
            $table->integer('from')->nullable();
            $table->integer('to')->nullable();
            $table->integer('cab_id')->nullable();
            $table->decimal('base_fare', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cab_rates');
    }
};
