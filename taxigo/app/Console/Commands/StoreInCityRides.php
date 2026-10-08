<?php

namespace App\Console\Commands;

use App\Enums\CabRateEnum;
use App\Enums\Type;
use App\Models\CabPriceType;
use App\Models\CabRate;
use App\Models\City;
use Illuminate\Console\Command;

class StoreInCityRides extends Command
{
    protected $signature = 'store:in-city-rides';
    protected $description = 'This command is used to store in city rides';

    public function handle()
    {
        $cities = City::all();

        foreach ($cities as $fromCity) {
            foreach ($cities as $toCity) {
                if ($fromCity->id !== $toCity->id) {
                    $cabRate = new CabRate();
                    $cabRate->tab = CabRateEnum::TAB_THREE;
                    $cabRate->from = $fromCity->id ?? null;
                    $cabRate->to = $toCity->id ?? null;
                    $cabRate->base_km = null;
                    $cabRate->save();

                    if ($cabRate) {
                        CabPriceType::store($cabRate->id, Type::CAB_HATCHBACK_ID, 5000);
                        CabPriceType::store($cabRate->id, Type::CAB_SEDAN_ID, 5000);
                        CabPriceType::store($cabRate->id, Type::CAB_SUV_ID, 5000);
                    }
                }
            }
        }

        $this->info('In city rides store successfully.');
    }
}
