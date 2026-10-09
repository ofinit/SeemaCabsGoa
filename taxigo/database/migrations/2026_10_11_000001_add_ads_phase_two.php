<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Self-serve ads, Phase 2: new placements (P9, P10, P14, P15, P16),
 * targeting, peak pricing, category exclusivity, launch offer, coupons,
 * bundles, ad reports, automatic link / licence checks and reporting.
 * Amounts are in paise.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->json('targeting')->nullable()->after('screens');
        });

        Schema::table('ad_campaigns', function (Blueprint $table) {
            $table->json('targeting')->nullable()->after('landing_url');
            $table->string('headline', 60)->nullable()->after('targeting');
            $table->boolean('exclusive_category')->default(false)->after('headline');
            $table->unsignedBigInteger('peak_amount')->default(0)->after('list_amount');
            $table->unsignedBigInteger('bundle_discount')->default(0)->after('peak_amount');
            $table->unsignedBigInteger('exclusivity_amount')->default(0)->after('bundle_discount');
            $table->unsignedBigInteger('promo_discount')->default(0)->after('discount_amount');
            $table->string('promo_label', 80)->nullable()->after('promo_discount');
            $table->unsignedBigInteger('coupon_id')->nullable()->after('promo_label');
            $table->string('coupon_code', 30)->nullable()->after('coupon_id');
            $table->string('paused_reason', 30)->nullable()->after('status');
            $table->timestamp('link_checked_at')->nullable();
            $table->unsignedTinyInteger('link_failures')->default(0);
            $table->timestamp('licence_warned_at')->nullable();
            $table->timestamp('weekly_report_at')->nullable();
            $table->timestamp('final_report_at')->nullable();
        });

        Schema::table('ad_campaign_placements', function (Blueprint $table) {
            $table->unsignedSmallInteger('units')->default(1)->after('days');
        });

        Schema::create('ad_pricing_rules', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('multiplier', 4, 2)->default(1.5);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('ad_coupons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('description')->nullable();
            $table->string('type', 10)->default('percent');
            $table->unsignedInteger('value');
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used')->default(0);
            $table->unsignedSmallInteger('per_advertiser')->default(1);
            $table->unsignedBigInteger('min_amount')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('first_booking_only')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('ad_bundles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->json('placement_codes');
            $table->unsignedInteger('price_standard');
            $table->unsignedInteger('price_premium');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('ad_reports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('advertisement_id')->index();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('reporter', 64);
            $table->string('reason', 30);
            $table->string('note', 500)->nullable();
            $table->string('platform', 10)->default('pwa');
            $table->string('status', 10)->default('open')->index();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->unique(['advertisement_id', 'reporter']);
        });

        $now = now();
        foreach ([
            12 => ['Airport Arrival Offers', 'airport-arrival-offers'],
            13 => ['Sightseeing Package Sponsored Stop', 'sightseeing-sponsored-stop'],
            14 => ['App-open Sponsor', 'app-open-sponsor'],
            15 => ['Website Landing Pages', 'website-landing-pages'],
            16 => ['Booking Email Footer', 'booking-email-footer'],
        ] as $id => [$title, $slug]) {
            DB::table('screen_prices')->insertOrIgnore([
                'id' => $id, 'title' => $title, 'screen' => $slug, 'price_per_day' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $sort = (int) DB::table('ad_placements')->max('sort');
        foreach ([
            ['P15', 'Airport arrival offers', 'Booking confirmed and trip screens, for airport pickups only — tourists who just landed.', 12, '2:1', 1600, 800, 3, false, 249, 749],
            ['P14', 'Sightseeing package — sponsored stop', 'On the sightseeing package you choose (e.g. the restaurant or spice farm on the route).', 13, '3:1', 1500, 500, 3, false, 99, 299],
            ['P16', 'App-open sponsor', '"Presented by" your logo and one line on the app splash screen. One advertiser at a time.', 14, '1:1', 1080, 1080, 1, true, 399, 999],
            ['P9', 'Website landing pages', 'Banner on seemacabsgoa.com pages. Priced per page group.', 15, '3:1', 1500, 500, 3, false, 149, 499],
            ['P10', 'Booking email footer', 'At the bottom of booking and invoice emails.', 16, '4:1', 1200, 300, 3, false, 49, 149],
        ] as [$code, $name, $desc, $screen, $shape, $w, $h, $slots, $exclusive, $std, $prem]) {
            DB::table('ad_placements')->insertOrIgnore([
                'code' => $code, 'name' => $name, 'description' => $desc, 'screen_id' => $screen,
                'shape' => $shape, 'width' => $w, 'height' => $h, 'slots' => $slots, 'exclusive' => $exclusive,
                'price_standard' => $std * 100, 'price_premium' => $prem * 100, 'active' => true, 'sort' => ++$sort,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Bundles from the rate card (§5.2): per-day price for the whole set.
        foreach ([
            ['FIND', 'Finding-a-taxi bundle', ['P12', 'P4'], 249, 749],
            ['JOURNEY', 'Booking-journey bundle', ['P1', 'P12', 'P5'], 449, 1299],
        ] as [$code, $name, $codes, $std, $prem]) {
            DB::table('ad_bundles')->insertOrIgnore([
                'code' => $code, 'name' => $name, 'placement_codes' => json_encode($codes),
                'price_standard' => $std * 100, 'price_premium' => $prem * 100, 'active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Peak windows (plan §5.3); admins edit or add more.
        foreach ([
            ['Christmas & New Year', '2026-12-15', '2027-01-05', 2.0],
        ] as [$name, $start, $end, $multiplier]) {
            DB::table('ad_pricing_rules')->insert([
                'name' => $name, 'start_date' => $start, 'end_date' => $end, 'multiplier' => $multiplier, 'active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_reports');
        Schema::dropIfExists('ad_bundles');
        Schema::dropIfExists('ad_coupons');
        Schema::dropIfExists('ad_pricing_rules');
        Schema::table('ad_campaign_placements', fn (Blueprint $table) => $table->dropColumn('units'));
        Schema::table('ad_campaigns', fn (Blueprint $table) => $table->dropColumn([
            'targeting', 'headline', 'exclusive_category', 'peak_amount', 'bundle_discount', 'exclusivity_amount',
            'promo_discount', 'promo_label', 'coupon_id', 'coupon_code', 'paused_reason', 'link_checked_at',
            'link_failures', 'licence_warned_at', 'weekly_report_at', 'final_report_at',
        ]));
        Schema::table('advertisements', fn (Blueprint $table) => $table->dropColumn('targeting'));
        DB::table('ad_placements')->whereIn('code', ['P9', 'P10', 'P14', 'P15', 'P16'])->delete();
        DB::table('screen_prices')->whereIn('id', [12, 13, 14, 15, 16])->delete();
    }
};
