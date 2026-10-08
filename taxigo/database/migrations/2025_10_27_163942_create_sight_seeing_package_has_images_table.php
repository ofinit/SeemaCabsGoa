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
        if (!Schema::hasTable('sight_seeing_package_has_images')) {

            Schema::create('sight_seeing_package_has_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sight_seeing_package_id')->nullable()->constrained('sight_seeing_packages')->cascadeOnDelete();
                $table->string('image')->nullable();
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
        Schema::dropIfExists('sight_seeing_package_has_images');
    }
};
