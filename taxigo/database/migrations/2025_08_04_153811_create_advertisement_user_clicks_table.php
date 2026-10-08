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
        if (!Schema::hasTable('advertisement_user_clicks')) {
            Schema::create('advertisement_user_clicks', function (Blueprint $table) {
                $table->id();
                $table->integer('advertisement_id')->index()->constrained('advertisements')->nullOnDelete();
                $table->integer('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisement_user_clicks');
    }
};
