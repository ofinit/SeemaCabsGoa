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
        Schema::table('booking_details', function (Blueprint $table) {
            $table->foreignId('pickup_from')->nullable()->change()->constrained('cities')->nullOnDelete();
            $table->foreignId('drop_to')->nullable()->change()->constrained('cities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropForeign(['pickup_from']);
            $table->dropForeign(['drop_to']);

            $table->string('pickup_from')->nullable()->change();
            $table->string('drop_to')->nullable()->change();
        });
    }
};
