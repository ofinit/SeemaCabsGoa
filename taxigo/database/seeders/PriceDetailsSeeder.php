<?php

namespace Database\Seeders;

use App\Models\AdditionalKmCharges;
use App\Models\BaseFare;
use App\Models\NoOfKm;
use App\Models\WaitingCharges;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseFare = [
          [
            'value'=>30
          ],
          [
            'value'=>35
          ],
          [
            'value'=>40
          ],
        ];

        $NoOffKms = [
            [
              'value'=>30
            ],
            [
              'value'=>35
            ],
            [
              'value'=>40
            ],
        ];

        $additionalKmCharges = [
            [
              'value'=>30
            ],
            [
              'value'=>35
            ],
            [
              'value'=>40
            ],
        ];

        $waitingCharges = [
            [
              'value'=>30
            ],
            [
              'value'=>35
            ],
            [
              'value'=>40
            ],
        ];

        BaseFare::insert($baseFare);
        NoOfKm::insert($NoOffKms);
        AdditionalKmCharges::insert($additionalKmCharges);
        WaitingCharges::insert($waitingCharges);

    }
}
