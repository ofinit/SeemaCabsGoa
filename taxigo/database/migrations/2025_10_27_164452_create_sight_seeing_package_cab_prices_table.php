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
        if (!Schema::hasTable('sight_seeing_package_cab_prices')) {

            Schema::create('sight_seeing_package_cab_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sight_seeing_package_id')->nullable()->constrained(table: 'sight_seeing_packages')->cascadeOnDelete();
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
                $table->decimal('sedan_price', 10, 2)->nullable()->index();
                $table->decimal('suv_price', 10, 2)->nullable()->index();
                $table->decimal('hatchback_price', 10, 2)->nullable()->index();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sight_seeing_package_cab_prices');
    }
};
