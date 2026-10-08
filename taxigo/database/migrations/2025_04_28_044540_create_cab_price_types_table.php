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
        Schema::create('cab_price_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cab_rate_id')->nullable()->constrained('cab_rates')->nullOnDelete();
            $table->integer('cab_type')->nullable();
            $table->decimal('base_fare')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cab_price_types');
    }
};
