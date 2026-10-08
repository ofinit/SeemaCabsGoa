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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->boolean('gender')->nullable()->after('phone_number');
            $table->integer('country_id')->nullable()->after('gender');
            $table->integer('state_id')->nullable()->after('country_id');
            $table->string('device')->nullable()->after('state_id');
            $table->string('image')->nullable()->after('device');
            $table->boolean('status')->nullable()->after('image');
            $table->string('delete_reason')->nullable()->after('status');
            $table->softDeletes()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number','gender','country_id','state_id','device','image','status']);
        });
    }
};
