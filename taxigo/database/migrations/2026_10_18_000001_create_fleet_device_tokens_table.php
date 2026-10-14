<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phones running the Seema Cabs Fleet app (Firebase tokens), registered only
 * by a logged-in fleet operator / admin. New-booking alerts go to these.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_device_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('token', 255)->unique();
            $table->string('platform', 20)->default('android');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_device_tokens');
    }
};
