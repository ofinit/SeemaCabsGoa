<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-off clean-up of old booking money data (see the dashboard audit,
 * Oct 2026). Every changed value is written to `data_fixes` first, so the
 * fix is auditable and `down()` puts the old values back.
 *
 *  1. Refunded bookings stored as "completed": refund code used to put
 *     Type::REFUND (4) into booking status, which for bookings means
 *     COMPLETE. Fully refunded, never-assigned bookings become CANCEL (3).
 *  2. v1 settlements saved fleet_operator_commission at 10× the fare
 *     (e.g. "29,000.00" on a ₹2,900 ride): divided by 100. Negative
 *     commissions left by old cancellation code are set to 0.
 *  3. Two bookings whose cash-to-driver didn't match their fare.
 *  4. The fleet operator still named "Test Company" is Seema Holidays.
 */
return new class extends Migration
{
    private const FIX = '2026_10_16_legacy_money';

    public function up(): void
    {
        if (!Schema::hasTable('data_fixes')) {
            Schema::create('data_fixes', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->string('fix', 60)->index();
                $table->string('table_name', 60);
                $table->unsignedBigInteger('row_id');
                $table->string('column_name', 60);
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        // 1. Refunded, never-assigned bookings marked "completed" → cancelled.
        $refunded = DB::table('booking_details as bd')
            ->whereNull('bd.deleted_at')->where('bd.status', 4)->whereNull('bd.assigned_driver_id')
            ->whereExists(fn ($q) => $q->from('payments as p')->whereColumn('p.booking_id', 'bd.id')->where('p.status', 4)->whereNull('p.deleted_at'))
            ->pluck('bd.id');
        foreach ($refunded as $id) {
            $this->change('booking_details', $id, 'status', 4, 3);
        }

        // 2. Settlement JSON on payments of v1 bookings.
        $rows = DB::table('payments as p')->join('booking_details as bd', 'bd.id', '=', 'p.booking_id')
            ->where(fn ($q) => $q->whereNull('bd.pricing_version')->orWhere('bd.pricing_version', '<', 2))
            ->whereNotNull('p.amount_settlement')
            ->get(['p.id', 'p.amount_settlement', 'bd.total_payment', 'bd.tax_amount']);
        foreach ($rows as $row) {
            $settlement = json_decode((string) $row->amount_settlement, true);
            if (!is_array($settlement)) {
                continue;
            }
            $fixed = $settlement;
            $fare = (float) $row->total_payment + (float) $row->tax_amount;
            $operator = $this->number($settlement['fleet_operator_commission'] ?? null);
            if ($operator !== null && $fare > 0 && $operator > $fare) {
                $fixed['fleet_operator_commission'] = number_format($operator / 100, 2);
            }
            foreach (['company_commission', 'fleet_operator_total_payment', 'fleet_operator_commission'] as $key) {
                $value = $this->number($fixed[$key] ?? null);
                if ($value !== null && $value < 0) {
                    $fixed[$key] = '0.00';
                }
            }
            if ($fixed !== $settlement) {
                $this->change('payments', $row->id, 'amount_settlement', $row->amount_settlement, json_encode($fixed));
            }
        }

        // 3. Cash-to-driver that doesn't match the fare (only if still unchanged).
        $cash = [
            // ₹1,560 fare + ₹312 markup − ₹374 advance (identical to SC-GOA0062).
            'SC-GOA0059' => ['1560.00', '1498.00'],
            // ₹1,650 final fare − ₹220 advance (cash was left at the original ₹1,100 fare).
            'SC-GOA0018' => ['880.00', '1430.00'],
        ];
        foreach ($cash as $bookingId => [$old, $new]) {
            $row = DB::table('booking_details')->where('booking_id', $bookingId)->first(['id', 'cash_with_driver']);
            if ($row && number_format((float) $row->cash_with_driver, 2, '.', '') === $old) {
                $this->change('booking_details', $row->id, 'cash_with_driver', $old, $new);
            }
        }

        // 4. "Test Company" → Seema Holidays (the fleet operator login and its record).
        foreach (DB::table('users')->where('type', 2)->where('name', 'Test Company')->pluck('id') as $id) {
            $this->change('users', $id, 'name', 'Test Company', 'Seema Holidays');
        }
        foreach (DB::table('fleet_operators')->where('company_name', 'Test Company')->pluck('id') as $id) {
            $this->change('fleet_operators', $id, 'company_name', 'Test Company', 'Seema Holidays');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('data_fixes')) {
            return;
        }
        foreach (DB::table('data_fixes')->where('fix', self::FIX)->orderByDesc('id')->get() as $fix) {
            DB::table($fix->table_name)->where('id', $fix->row_id)->update([$fix->column_name => $fix->old_value]);
        }
        DB::table('data_fixes')->where('fix', self::FIX)->delete();
    }

    private function change(string $table, int $id, string $column, mixed $old, mixed $new): void
    {
        DB::table('data_fixes')->insert([
            'fix' => self::FIX, 'table_name' => $table, 'row_id' => $id, 'column_name' => $column,
            'old_value' => $old, 'new_value' => $new, 'created_at' => now(),
        ]);
        DB::table($table)->where('id', $id)->update([$column => $new]);
    }

    private function number(mixed $value): ?float
    {
        return is_numeric($clean = str_replace(',', '', (string) $value)) && $value !== null ? (float) $clean : null;
    }
};
