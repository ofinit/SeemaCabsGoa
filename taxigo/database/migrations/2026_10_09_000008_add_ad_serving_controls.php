<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ads phase 0:
 * - approval + payment status: only approved, paid ads are served. Existing
 *   (admin-created) ads count as approved and paid, so nothing live changes.
 * - daily viewable-impression counters per ad / screen / platform.
 * - clicks record the screen and platform (and no longer store GPS).
 * - the "Searching for taxi" top/bottom screen slugs were swapped against
 *   their titles; the titles (and the API's screen ids) are right.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('approved')->after('default');
            $table->string('payment_status', 20)->default('paid')->after('approval_status');
            $table->unsignedBigInteger('status_changed_by')->nullable()->after('payment_status');
            $table->timestamp('status_changed_at')->nullable()->after('status_changed_by');
            $table->string('status_note')->nullable()->after('status_changed_at');
        });

        Schema::create('advertisement_impressions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('advertisement_id');
            $table->unsignedSmallInteger('screen_id')->default(0);
            $table->string('platform', 10)->default('pwa');
            $table->date('date');
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->unique(['advertisement_id', 'screen_id', 'platform', 'date'], 'ad_impressions_unique');
            $table->index(['advertisement_id', 'date']);
        });

        Schema::table('advertisement_user_clicks', function (Blueprint $table) {
            $table->unsignedSmallInteger('screen_id')->nullable()->after('user_id');
            $table->string('platform', 10)->nullable()->after('screen_id');
        });

        DB::table('screen_prices')->where('title', 'like', 'Searching For Taxi Screen Top%')
            ->update(['screen' => 'searching-for-taxi-screen-top-section']);
        DB::table('screen_prices')->where('title', 'like', 'Searching For Taxi Screen Bottom%')
            ->update(['screen' => 'searching-for-taxi-screen-bottom-section']);
    }

    public function down(): void
    {
        DB::table('screen_prices')->where('title', 'like', 'Searching For Taxi Screen Top%')
            ->update(['screen' => 'searching-for-taxi-screen-bottom-section']);
        DB::table('screen_prices')->where('title', 'like', 'Searching For Taxi Screen Bottom%')
            ->update(['screen' => 'searching-for-taxi-screen-top-section']);

        Schema::table('advertisement_user_clicks', function (Blueprint $table) {
            $table->dropColumn(['screen_id', 'platform']);
        });
        Schema::dropIfExists('advertisement_impressions');
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'payment_status', 'status_changed_by', 'status_changed_at', 'status_note']);
        });
    }
};
