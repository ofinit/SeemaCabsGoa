<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Self-serve ads, Phase 3: in-cab QR cards (P18), sponsored push (P11),
 * conversion tracking, agency accounts, push opt-in. Amounts are in paise.
 */
return new class extends Migration
{
    public function up(): void
    {
        // How a placement is billed: per day, per unit per 30 days, or per send.
        Schema::table('ad_placements', function (Blueprint $table) {
            $table->string('billing', 10)->default('day')->after('price_premium');
        });

        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->string('push_body', 160)->nullable()->after('headline');
            $table->string('conversion_token', 32)->nullable()->unique()->after('push_body');
        });

        Schema::table('ad_advertisers', function (Blueprint $table) {
            $table->unsignedBigInteger('agency_id')->nullable()->index()->after('user_id');
        });
        // An agency login can own several advertiser (client) profiles.
        Schema::table('ad_advertisers', function (Blueprint $table) {
            $table->dropUnique('ad_advertisers_user_id_unique');
            $table->index('user_id');
        });

        Schema::create('ad_agencies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('name');
            $table->string('gstin', 15)->nullable();
            $table->string('status', 10)->default('pending');
            $table->string('status_note')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_qr_cards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->string('code', 12)->unique();
            $table->unsignedBigInteger('cab_id')->nullable();
            $table->date('placed_on')->nullable();
            $table->date('removed_on')->nullable();
            $table->unsignedInteger('scans')->default(0);
            $table->timestamps();
        });

        Schema::create('ad_push_sends', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->date('send_on');
            $table->string('status', 10)->default('scheduled')->index();
            $table->unsignedInteger('recipients')->default(0);
            $table->unsignedInteger('delivered')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->string('error', 500)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_conversions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->unsignedBigInteger('click_id')->nullable()->index();
            $table->string('label', 40)->default('lead');
            $table->unsignedBigInteger('value')->default(0);
            $table->string('visitor', 40);
            $table->date('date')->index();
            $table->timestamps();
            $table->unique(['campaign_id', 'visitor', 'label', 'date']);
        });

        // Marketing push consent (DPDP): off until the customer turns it on.
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('ad_push_opt_in')->default(false);
            $table->timestamp('ad_push_opt_in_at')->nullable();
            $table->timestamp('last_ad_push_at')->nullable();
        });

        $now = now();
        foreach ([
            17 => ['In-cab QR Card', 'in-cab-qr-card'],
            18 => ['Sponsored Push', 'sponsored-push'],
        ] as $id => [$title, $slug]) {
            DB::table('screen_prices')->insertOrIgnore([
                'id' => $id, 'title' => $title, 'screen' => $slug, 'price_per_day' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $sort = (int) DB::table('ad_placements')->max('sort');
        foreach ([
            ['P18', 'In-cab QR card', 'Printed A6 card with your ad and a QR code on the seat-back of our cabs. Priced per cab per month; we print and place it.', 17, '4:5', 1080, 1350, 20, 'month', 499, 1499],
            ['P11', 'Sponsored push', 'One push notification to customers who opted in to offers (each person gets at most one a week). Priced per send.', 18, '2:1', 1600, 800, 1, 'send', 999, 2999],
        ] as [$code, $name, $desc, $screen, $shape, $w, $h, $slots, $billing, $std, $prem]) {
            DB::table('ad_placements')->insertOrIgnore([
                'code' => $code, 'name' => $name, 'description' => $desc, 'screen_id' => $screen,
                'shape' => $shape, 'width' => $w, 'height' => $h, 'slots' => $slots, 'exclusive' => false,
                'price_standard' => $std * 100, 'price_premium' => $prem * 100, 'billing' => $billing,
                'active' => true, 'sort' => ++$sort, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['ad_push_opt_in', 'ad_push_opt_in_at', 'last_ad_push_at']));
        Schema::dropIfExists('ad_conversions');
        Schema::dropIfExists('ad_push_sends');
        Schema::dropIfExists('ad_qr_cards');
        Schema::dropIfExists('ad_agencies');
        Schema::table('ad_advertisers', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->unique('user_id');
            $table->dropColumn('agency_id');
        });
        Schema::table('ad_campaigns', fn (Blueprint $table) => $table->dropColumn(['push_body', 'conversion_token']));
        DB::table('ad_placements')->whereIn('code', ['P18', 'P11'])->delete();
        Schema::table('ad_placements', fn (Blueprint $table) => $table->dropColumn('billing'));
        DB::table('screen_prices')->whereIn('id', [17, 18])->delete();
    }
};
