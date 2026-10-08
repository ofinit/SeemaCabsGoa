<?php

namespace App\Exports;

use App\Enums\Type;
use App\Models\Cab;
use App\Models\Payment;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportFleetOperatorPayment implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch Cab Data for Export
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Payment::with('bookingDetails.assignDriver')->get();
    }

    /**
     * Map data for each row
     * @param $cab
     * @return array
     */
    public function map($payment): array
    {
        $headings = [];
        $settlementAmount = json_decode($payment->amount_settlement, true);

        $headings = array_merge($headings, [
            $payment->date,
            $payment->bookingDetails ? $payment->bookingDetails->booking_id : null,
            // $payment->bookingDetails ? $payment->bookingDetails->getCabDetails ? $payment->bookingDetails->getCabDetails->getFleetOperatorDetails ? $payment->bookingDetails->getCabDetails->getFleetOperatorDetails->name : null : null : null,
            // $payment->bookingDetails ? $payment->bookingDetails->getCabDetails ? $payment->bookingDetails->getCabDetails->number : null : null,
            // $payment->bookingDetails ? $payment->bookingDetails->assignDriver ? $payment->bookingDetails->assignDriver->name : null : null,
            // $payment->bookingDetails ? $payment->bookingDetails->assignDriver ? $payment->bookingDetails->assignDriver->mobile : null : null,
            $payment->bookingDetails ? $payment->bookingDetails->getPickupFrom ? $payment->bookingDetails->getPickupFrom->name : null : null,
            $payment->bookingDetails ? $payment->bookingDetails->getDropTo ? $payment->bookingDetails->getDropTo->name : null : null,
            $payment->amount ?? null,
            $settlementAmount['company_commission'] ?? 0,
            $settlementAmount['tds_amount'] ?? 0,
            $settlementAmount['fleet_operator_total_payment'] ?? 0,
            0 ?? null
        ]);

        return $headings;
    }

    /**
     * Excel Header Titles
     * @return array
     */
    public function headings(): array
    {
        $headings = [];
        $headings = array_merge($headings, [
            'Date',
            'Booking ID',
            // 'Fleet Operator',
            // 'Cab Number',
            // 'Driver Name',
            // 'Mobile Number',
            'Pickup From',
            'Drop To',
            'Total Amount',
            'Aggregator Commissions',
            'TDS',
            'Settlement Amount',
            'Refund',
        ]);

        return $headings;
    }
}
