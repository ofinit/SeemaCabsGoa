<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ExportUserList implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('users')
            ->orderBy('users.id', 'DESC')
            ->join('countries', 'users.country_id', '=', 'countries.id')
            ->join('states', 'users.state_id', '=', 'states.id')
            // ->where('users.role_id', 3)
            ->select(
                'users.name',
                DB::raw("CASE
                            WHEN users.gender = 1 THEN 'Male'
                            WHEN users.gender = 0 THEN 'Female'
                            WHEN users.gender = 2 THEN 'Other'
                            ELSE ''
                         END AS gender"),
                'countries.name as country_name',
                'states.name as state_name',
                'users.email',
                'users.phone_number',
                'users.device'
            )
            ->get();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Customer Name',
            'Gender',
            'Country',
            'State',
            'Email Id',
            'Mobile Number',
            'Device',
        ];
    }
}
