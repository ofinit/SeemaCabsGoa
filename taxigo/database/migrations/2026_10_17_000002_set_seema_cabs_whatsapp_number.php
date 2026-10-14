<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** +91 97650 40490 isn't Seema Cabs' number: a saved booking WhatsApp number with it becomes +91 98223 85180. */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('environments')->where('title', 'bookingwhatsappnumber')->where('value', '919765040490')
            ->update(['value' => '919822385180']);
    }

    public function down(): void
    {
        // Nothing to restore: the old number was wrong.
    }
};
