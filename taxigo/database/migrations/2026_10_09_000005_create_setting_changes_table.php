<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Audit log for pricing, GST, platform-fee and business-profile changes, and
 * seeds the pricing v2 settings: the existing 20% "GST" value becomes the
 * internal fare markup, so customer prices are unchanged on rollout. GST
 * itself starts switched off.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('key', 100)->index();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        $existingMarkup = DB::table('environments')->where('title', 'gsttitle')->whereNull('deleted_at')->value('value');

        $defaults = [
            'faremarkuppercent' => is_numeric($existingMarkup) ? (string) $existingMarkup : '20',
            'faremarkuppercentpackages' => '0',
            'gst_enabled' => '0',
            'gst_start_at' => '',
            'gst_rate_rides' => '5',
            'gst_rate_packages' => '5',
            'gst_sac_rides' => '',
            'gst_sac_packages' => '',
            'gst_rate_platform_fee' => '18',
            'gst_sac_platform_fee' => '',
        ];

        $now = now();
        foreach ($defaults as $title => $value) {
            $exists = DB::table('environments')->where('title', $title)->whereNull('deleted_at')->exists();
            if (!$exists) {
                DB::table('environments')->insert([
                    'title' => $title,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        DB::table('setting_changes')->insert([
            'key' => 'faremarkuppercent',
            'old_value' => null,
            'new_value' => $defaults['faremarkuppercent'] . ' (migrated from gsttitle)',
            'created_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('environments')->whereIn('title', [
            'faremarkuppercent', 'faremarkuppercentpackages', 'gst_enabled', 'gst_start_at',
            'gst_rate_rides', 'gst_rate_packages', 'gst_sac_rides', 'gst_sac_packages',
            'gst_rate_platform_fee', 'gst_sac_platform_fee',
        ])->delete();

        Schema::dropIfExists('setting_changes');
    }
};
