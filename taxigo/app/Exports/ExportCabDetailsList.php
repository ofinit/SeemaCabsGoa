<?php

namespace App\Exports;

use App\Enums\Type;
use App\Models\Cab;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportCabDetailsList implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch Cab Data for Export
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $cab = Cab::orderBy('id', 'DESC');
        if (Auth()->user()->type != Type::ADMIN) {
            $cab->where('fleet_operator_id', Auth::id());
        }
        return $cab->get();
    }

    /**
     * Map data for each row
     * @param $cab
     * @return array
     */
    public function map($cab): array
    {
        $headings = [];
        // if (Auth::user()->type == Type::ADMIN) {
        //     $headings[] = $cab->getFleetOperatorDetails->name;
        // }

        $headings = array_merge($headings, [
            $cab->zone_names,
            $this->getCabType($cab->type),
            $this->getModelName($cab->model),
            $cab->getColorDetails->name ?? 'N/A',
            $cab->number,
            $cab->getAssignedDriverDetails->name ?? 'N/A',
            $cab->getAssignedDriverDetails->mobile ?? 'N/A',
            // $cab->getBaseFareDetails->value ?? 0,
            // $cab->getNoOfKMsDetails->value ?? 0,
            // $cab->getAdditionalChargeDetails->value ?? 0,
            // $cab->getWaitingChargeDetails->value ?? 0,
            $cab->status == 1 ? 'Active' : 'Suspend'
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
        // if (Auth::user()->type == Type::ADMIN) {
        //     $headings[] = 'Fleet Operator';
        // }
        $headings = array_merge($headings, [
            'Cab Zone',
            'Cab Type',
            'Model Name',
            'Color',
            'Cab Number',
            'Driver Name',
            'Driver Mobile',
            // 'Base Fare',
            // 'KMS',
            // 'ADDL.KM',
            // 'Waiting Charges',
            'Status'
        ]);

        return $headings;
    }

    /**
     * Determine Cab Type based on Zone
     */
    private function getCabType($type)
    {
        $typeH = 'Hatchback';
        if ($type == 2) {
            $typeH = 'Sedan';
        } else if ($type == 3) {
            $typeH = 'SUV';
        }
        return $typeH;
    }

    /**
     * Determine Model Name based on Model ID
     */
    private function getModelName($modelId)
    {
        $model = 'Baleno, Swift or similar';
        if ($modelId == 2) {
            $model = 'Dzire, Etios or similar';
        } else if ($modelId == 3) {
            $model = 'Xylo, Ertiga or similar';
        }

        return $model;
    }
}
