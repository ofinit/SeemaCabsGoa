<?php

// database/seeders/CountrySeeder.php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        getCountryUsingThirdPartyApi();
        // Country::truncate();

        // Country::create(
        //     [
        //         'name' => 'India'
        //     ]
        // );
    }
}
