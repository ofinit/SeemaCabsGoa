<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GetCountryListResource;
use App\Http\Resources\Api\GetStateListResource;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends ResponseController
{
    public function countryList()
    {
        try {
            $list = Country::all();
            $data = GetCountryListResource::collection($list);
            return $this->success($data, 'Country list fetched successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of country list :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function getStateList($id)
    {
        try {
            $list = State::where('country_id', $id)->get();
            if ($list->isEmpty()) {
                $country = Country::find($id);
                if ($country) {
                    $defaultState = State::firstOrCreate([
                        'country_id' => $country->id,
                        'name' => $country->name,
                    ]);
                    $list = collect([$defaultState]);
                }
            }
            $data = GetStateListResource::collection($list);
            return $this->success($data, 'State list fetched successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of state list :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function getCityList()
    {
        try {
            $list = City::select('id', 'name')->get();
            return $this->success($list, 'City list fetched successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of city list :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
