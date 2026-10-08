<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getStates($id)
    {
        $states = State::query();
        if ($id == 'all') {
            $states = $states->get();
        } else {
            $states = $states->where('country_id', $id)->get();
        }


        return response()->json($states);
    }

    public function getCity($id)
    {
        $city = City::query();
        if ($id == 'all') {
            $city = $city->get();
        } else {
            $city = $city->where('state_id', $id)->get();
        }

        return response()->json($city);
    }
}
