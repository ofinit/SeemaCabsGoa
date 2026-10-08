<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // getStateUsingThirdPartyApi();

        $cities = [
            "Aldona",
            "Arambol",
            "Baga",
            "Bambolim",
            "Bandora",
            "Benaulim",
            "Calangute",
            "Candolim",
            "Carapur",
            "Cavelossim",
            "Chicalim",
            "Chinchinim",
            "Colovale",
            "Colva",
            "Cortalim",
            "Cuncolim",
            "Curchorem",
            "Curti",
            "Davorlim",
            "Dicholi",
            "Goa Velha",
            "Guirim",
            "Jua",
            "Kānkon",
            "Madgaon",
            "Māpuca",
            "Morjim",
            "Mormugao",
            "Navelim",
            "North Goa",
            "Palle",
            "Panaji",
            "Pernem",
            "Ponda",
            "Quepem",
            "Queula",
            "Raia",
            "Saligao",
            "Sancoale",
            "Sanguem",
            "Sanquelim",
            "Sanvordem",
            "Serula",
            "Solim",
            "South Goa",
            "Taleigao",
            "Vagator",
            "Valpoy",
            "Varca",
            "Vasco da Gama"
        ];


        $state = State::where('name', 'Goa')->first();
        foreach ($cities as $city) {

            if (!City::where('name', $city)->where('state_id', $state->id ?? null)->exists()) {
                $citySave = new City();
                $citySave->state_id = $state->id ?? null;
                $citySave->name = $city;
                $citySave->save();
            }
        }
    }
}
