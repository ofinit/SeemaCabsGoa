<?php

namespace App\Exports;
use App\Models\Advertiser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class exportAdvertiserList implements FromCollection, WithHeadings
{
    /**
     * Fetch Cab Data for Export
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $advertiser = Advertiser::where('add_id', request()->id)->select('date', 'company_name', 'name', 'phone_number', 'email', )->get();
        return $advertiser;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'Advertiser',
            'Name',
            'Mobile Number',
            'Email'
        ];
    }
}
