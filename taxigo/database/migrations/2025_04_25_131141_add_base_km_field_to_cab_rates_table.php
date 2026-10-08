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
        Schema::table('cab_rates', function (Blueprint $table) {
            $table->integer('base_km')->nullable()->after('base_fare');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cab_rates', function (Blueprint $table) {
            $table->dropColumn('base_km');
        });
    }
};
