<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Self-serve advertising (ads plan, Phase 1).
 *
 * Commercial / workflow data lives in new `ad_*` tables (InnoDB). Serving is
 * unchanged: when a campaign is approved, one `advertisements` row is created
 * per booked placement, so AdServer, the mobile API, click / impression
 * tracking and the existing admin lists keep working. Amounts are in paise.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('screen_id')->unique();
            $table->string('shape', 10);
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->unsignedTinyInteger('slots')->default(3);
            $table->boolean('exclusive')->default(false);
            $table->unsignedInteger('price_standard');
            $table->unsignedInteger('price_premium');
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('ad_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('tier', 10)->default('standard');
            $table->string('licence_label')->nullable();
            $table->boolean('licence_required')->default(false);
            $table->boolean('second_approval')->default(false);
            $table->string('rules')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('ad_advertisers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('business_name');
            $table->foreignId('category_id')->constrained('ad_categories');
            $table->string('contact_name');
            $table->string('phone', 20);
            $table->string('email');
            $table->string('gstin', 15)->nullable();
            $table->string('legal_name')->nullable();
            $table->string('billing_address', 500)->nullable();
            $table->string('status', 10)->default('active');
            $table->string('status_note')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_licences', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('advertiser_id')->constrained('ad_advertisers')->cascadeOnDelete();
            $table->string('type');
            $table->string('number', 100)->nullable();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->date('valid_until')->nullable();
            $table->string('status', 10)->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('reference', 20)->nullable()->unique();
            $table->foreignId('advertiser_id')->constrained('ad_advertisers');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('status', 20)->default('draft')->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('days')->default(0);
            $table->string('tier', 10)->default('standard');
            $table->string('category_name')->nullable();
            $table->string('landing_type', 10)->default('website');
            $table->string('landing_url', 500)->nullable();
            // Price snapshot (paise).
            $table->unsignedBigInteger('list_amount')->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('net_amount')->default(0);
            $table->decimal('gst_rate', 5, 2)->default(18);
            $table->boolean('inter_state')->default(false);
            $table->unsignedBigInteger('cgst_amount')->default(0);
            $table->unsignedBigInteger('sgst_amount')->default(0);
            $table->unsignedBigInteger('igst_amount')->default(0);
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->decimal('seema_percent', 5, 2)->default(10);
            $table->unsignedBigInteger('ofinit_amount')->default(0);
            $table->unsignedBigInteger('ofinit_gst')->default(0);
            $table->unsignedBigInteger('ofinit_total')->default(0);
            // Payment & OfinIT split.
            $table->string('payment_gateway', 10)->nullable();
            $table->string('pg_order_id', 60)->nullable()->index();
            $table->string('transaction_id', 60)->nullable()->index();
            $table->timestamp('hold_expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('transfer_status', 10)->nullable();
            $table->string('transfer_reference', 80)->nullable();
            $table->unsignedBigInteger('transfer_amount')->default(0);
            $table->string('transfer_error', 500)->nullable();
            $table->timestamp('transferred_at')->nullable();
            $table->unsignedBigInteger('refund_amount')->default(0);
            $table->string('refund_reference', 80)->nullable();
            $table->string('refund_error', 500)->nullable();
            $table->timestamp('refunded_at')->nullable();
            // Review.
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('needs_second_approval')->default(false);
            $table->unsignedBigInteger('first_approved_by')->nullable();
            $table->timestamp('first_approved_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->json('reason_codes')->nullable();
            $table->string('review_note', 1000)->nullable();
            $table->json('auto_flags')->nullable();
            $table->boolean('auto_approve')->default(false);
            // Lifecycle.
            $table->foreignId('renewed_from_id')->nullable()->constrained('ad_campaigns')->nullOnDelete();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_creatives', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->string('shape', 20);
            $table->string('original_path');
            $table->json('crop');
            $table->string('file');
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->unsignedInteger('bytes');
            $table->string('sha1', 40)->index();
            $table->timestamps();
            $table->unique(['campaign_id', 'shape']);
        });

        Schema::create('ad_campaign_placements', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->foreignId('placement_id')->constrained('ad_placements');
            $table->unsignedInteger('price_per_day');
            $table->unsignedSmallInteger('days');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('advertisement_id')->nullable()->index();
            $table->timestamps();
            $table->unique(['campaign_id', 'placement_id']);
        });

        Schema::create('ad_reviews', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->unsignedBigInteger('reviewer_id');
            $table->string('stage', 10)->default('first');
            $table->string('decision', 10);
            $table->json('checklist')->nullable();
            $table->json('reason_codes')->nullable();
            $table->string('note', 1000)->nullable();
            $table->timestamps();
        });

        Schema::table('advertisements', function (Blueprint $table) {
            $table->unsignedBigInteger('ad_campaign_id')->nullable()->index()->after('id');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('ad_campaign_id')->nullable()->index()->after('booking_id');
        });
        Schema::table('invoice_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('ad_campaign_id')->nullable()->index()->after('booking_id');
        });

        $now = now();

        // Legacy screens for the three new placements, so existing admin lists can name them.
        foreach ([
            9 => ['Home Inline Card', 'home-inline-card'],
            10 => ['Searching For Taxi Screen Large Card', 'searching-for-taxi-screen-large-card'],
            11 => ['Searching For Taxi Full Screen', 'searching-for-taxi-full-screen'],
        ] as $id => [$title, $slug]) {
            DB::table('screen_prices')->insertOrIgnore([
                'id' => $id, 'title' => $title, 'screen' => $slug, 'price_per_day' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Rate card from the ads plan §5.2 (rupees per day, before GST).
        $placements = [
            ['P1', 'Home — hero carousel', 'Top of the home screen, up to 5 ads rotating.', 1, '2:1', 1600, 800, 5, false, 199, 599],
            ['P2', 'Home — inline card', 'Between the service tiles on the home screen.', 9, '3:1', 1500, 500, 3, false, 49, 149],
            ['P3', 'Finding a taxi — top banner', 'Above the search progress while a cab is being found.', 3, '3:1', 1500, 500, 3, false, 99, 299],
            ['P4', 'Finding a taxi — bottom banner', 'Below the search progress while a cab is being found.', 8, '3:1', 1500, 500, 3, false, 79, 249],
            ['P12', 'Finding a taxi — large card', 'Double-size card that replaces the top banner.', 10, '4:5', 1080, 1350, 2, false, 199, 599],
            ['P13', 'Finding a taxi — full screen', 'Full-screen takeover, one advertiser per day, closable after 3 seconds.', 11, '9:16', 1080, 1920, 1, true, 499, 1499],
            ['P5', 'Booking confirmed', 'Shown with the booking confirmation.', 4, '2:1', 1600, 800, 3, false, 99, 299],
            ['P6', 'Driver details', 'Trip screen once a driver is assigned; never covers driver info or SOS.', 5, '3:1', 1500, 500, 3, false, 49, 149],
            ['P7', 'Ride complete', 'Trip screen after the ride is completed.', 6, '1:1', 1080, 1080, 3, false, 79, 199],
            ['P8', 'Rides history & Account', 'My rides and account screens.', 7, '3:1', 1500, 500, 3, false, 29, 99],
        ];
        foreach ($placements as $i => [$code, $name, $desc, $screen, $shape, $w, $h, $slots, $exclusive, $std, $prem]) {
            DB::table('ad_placements')->insert([
                'code' => $code, 'name' => $name, 'description' => $desc, 'screen_id' => $screen,
                'shape' => $shape, 'width' => $w, 'height' => $h, 'slots' => $slots, 'exclusive' => $exclusive,
                'price_standard' => $std * 100, 'price_premium' => $prem * 100, 'active' => true, 'sort' => $i,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Advertiser categories (plan §5.1, §9.1). Tier sets the price; rules are shown to the advertiser.
        $categories = [
            ['Restaurant / café', 'standard', 'FSSAI licence', true, false, 'Food, menus and offers. No liquor offers.'],
            ['Scooter / car rental', 'standard', 'Rental permit', true, false, 'Vehicles, tours and prices.'],
            ['Tours & activities', 'standard', 'Operator licence / permit', true, false, 'Tours, activities and prices.'],
            ['Shop / retail', 'standard', null, false, false, null],
            ['Spa / wellness', 'standard', null, false, false, 'No unverified health or weight-loss claims.'],
            ['Local services', 'standard', null, false, false, null],
            ['Events (non-nightlife)', 'standard', null, false, false, null],
            ['Casino (licensed in Goa)', 'premium', 'Goa casino licence', true, true, 'Venue as entertainment only. Must show 21+ and "Play responsibly". No betting, odds, jackpots or "win money".'],
            ['Nightclub', 'premium', 'Trade / excise licence', true, false, 'Venue, music, DJs, events. No alcohol brands, drink images or liquor offers. Age notice per licence.'],
            ['Pub / bar', 'premium', 'Trade / excise licence', true, false, 'Venue, music, food, events. No alcohol brands, drink images or liquor offers. Age notice per licence.'],
            ['Hotel / resort', 'premium', 'Registration / GSTIN', false, false, 'No "No. 1" or rating claims without proof.'],
            ['Real estate', 'premium', 'RERA registration', true, false, 'RERA number must be visible in the ad. No guaranteed-return claims.'],
            ['Brand / national advertiser', 'premium', null, false, false, null],
        ];
        foreach ($categories as $i => [$name, $tier, $licence, $required, $second, $rules]) {
            DB::table('ad_categories')->insert([
                'name' => $name, 'tier' => $tier, 'licence_label' => $licence, 'licence_required' => $required,
                'second_approval' => $second, 'rules' => $rules, 'active' => true, 'sort' => $i,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('invoice_lines', fn (Blueprint $table) => $table->dropColumn('ad_campaign_id'));
        Schema::table('invoices', fn (Blueprint $table) => $table->dropColumn('ad_campaign_id'));
        Schema::table('advertisements', fn (Blueprint $table) => $table->dropColumn('ad_campaign_id'));
        Schema::dropIfExists('ad_reviews');
        Schema::dropIfExists('ad_campaign_placements');
        Schema::dropIfExists('ad_creatives');
        Schema::dropIfExists('ad_campaigns');
        Schema::dropIfExists('ad_licences');
        Schema::dropIfExists('ad_advertisers');
        Schema::dropIfExists('ad_categories');
        Schema::dropIfExists('ad_placements');
        DB::table('screen_prices')->whereIn('id', [9, 10, 11])->delete();
    }
};
