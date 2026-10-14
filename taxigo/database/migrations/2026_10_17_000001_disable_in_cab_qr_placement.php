<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * In-cab QR cards (P18) are no longer sold: the placement is switched off, so
 * it disappears from the advertiser wizard, the admin's complimentary ads and
 * pricing. Ads already running with it are left as they are. An admin can
 * switch it back on in Placements & Pricing.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('ad_placements')->where('code', 'P18')->update(['active' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('ad_placements')->where('code', 'P18')->update(['active' => true, 'updated_at' => now()]);
    }
};
