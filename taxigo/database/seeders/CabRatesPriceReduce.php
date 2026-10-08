<?php

namespace Database\Seeders;

use App\Models\CabPriceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CabRatesPriceReduce extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reduce Cab Price Types To 30%

        $reducePercentage = 30;
        $cabPriceTypes = CabPriceType::get();

        if(!empty($cabPriceTypes))
        {
            foreach ($cabPriceTypes as $cabPriceType) {
                $original_fare = (double)$cabPriceType->base_fare;

                // Calculation to reduce 30% cab rates
                $modified_fare = $original_fare - ($original_fare * $reducePercentage / 100);

                $formatted_fare = number_format($modified_fare, 2, '.', '');

                $cabPriceType->update([
                    'base_fare' => $formatted_fare
                ]);

            }
        }

    }
}
