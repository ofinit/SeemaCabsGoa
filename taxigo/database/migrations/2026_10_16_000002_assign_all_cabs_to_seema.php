<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

/**
 * All cabs belong to Seema Holidays, the only fleet operator. Cabs still
 * owned by a login that isn't a fleet operator (cab 9 sat on the admin login)
 * move to it, so its dashboard and reports include their rides. Recorded in
 * `data_fixes`; `down()` restores the previous owner.
 */
return new class extends Migration
{
    private const FIX = '2026_10_16_cabs_to_seema';

    public function up(): void
    {
        $operators = DB::table('users')->where('type', 2)->whereNull('deleted_at')->where('name', 'Seema Holidays')->pluck('id');
        if ($operators->count() !== 1) {
            return; // Not exactly one Seema Holidays fleet login: leave it to Cab Management.
        }
        $seema = (int) $operators->first();
        $operatorIds = DB::table('users')->where('type', 2)->pluck('id');

        $cabs = DB::table('cabs')->whereNull('deleted_at')
            ->where(fn ($q) => $q->whereNull('fleet_operator_id')->orWhereNotIn('fleet_operator_id', $operatorIds))
            ->get(['id', 'fleet_operator_id']);
        foreach ($cabs as $cab) {
            DB::table('data_fixes')->insert([
                'fix' => self::FIX, 'table_name' => 'cabs', 'row_id' => $cab->id, 'column_name' => 'fleet_operator_id',
                'old_value' => $cab->fleet_operator_id, 'new_value' => $seema, 'created_at' => now(),
            ]);
            DB::table('cabs')->where('id', $cab->id)->update(['fleet_operator_id' => $seema]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('data_fixes')->where('fix', self::FIX)->get() as $fix) {
            DB::table('cabs')->where('id', $fix->row_id)->update(['fleet_operator_id' => $fix->old_value]);
        }
        DB::table('data_fixes')->where('fix', self::FIX)->delete();
    }
};
