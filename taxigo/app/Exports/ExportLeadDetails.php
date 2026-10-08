<?php

namespace App\Exports;
use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportLeadDetails implements FromCollection,WithHeadings
{

    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['No', 'Date', 'Name', 'Mobile Number', 'Email ID'];
    }
}
