<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCabRateRequest;
use App\Models\CabPriceDetails;
use App\Models\CabPriceType;
use App\Models\CabRate;
use App\Models\City;
use App\Models\Environment;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CabRateController extends Controller
{
    public function index(Request $request)
    {
        $city = City::select('id', 'name')->get();
        $airport = getAirportList();
        $page = $request->get('page', 1);
        $airportPickupPercentage = Environment::where('title','airport_pickup_percentage')->first();
        //Airport pick up Query
        $airPortPickup = CabRate::with('cabPriceType')->where('tab', Type::AIRPORT_PICKUP);

        if ($request->get('tab') == 0){
            //From and To filter query
            if(!empty($request->search_from) && !empty($request->search_to))
            {
                $airPortPickup = $airPortPickup->where('from',$request->search_from)->where('to',$request->search_to);
            }
            elseif(!empty($request->search_from))
            {
                $airPortPickup = $airPortPickup->where('from',$request->search_from);
            }
            elseif(!empty($request->search_to))
            {
                $airPortPickup = $airPortPickup->where('to',$request->search_to);
            }
        }

        $airPortPickupCount = $airPortPickup->count();
        $airPortPickup = $airPortPickup->paginate(10, ['*'], 'page', $page);

        //Airport drop Query
        $airPortDrop = CabRate::with('cabPriceType')->where('tab', Type::AIRPORT_DROP);

        if ($request->get('tab') == 1){

            if(!empty($request->search_from) && !empty($request->search_to))
            {
               $airPortDrop = $airPortDrop->where('from',$request->search_from)->where('to',$request->search_to);
            }
            elseif(!empty($request->search_from))
            {
                $airPortDrop = $airPortDrop->where('from',$request->search_from);
            }
            elseif(!empty($request->search_to))
            {
                $airPortDrop = $airPortDrop->where('to',$request->search_to);
            }
        }

        $airPortDropCount = $airPortDrop->count();
        $airPortDrop = $airPortDrop->paginate(10, ['*'], 'page', $page);

        //In City-rides Query
        $cityRide = CabRate::with('cabPriceType')
            ->whereHas('cabPriceType', function ($query) {
                $query->where(function ($q) {
                    $q->where('cab_type', Type::CAB_HATCHBACK_ID)
                        ->orWhere('cab_type', Type::CAB_SEDAN_ID)
                        ->orWhere('cab_type', Type::CAB_SUV_ID);
                });//->where('base_fare', operator: 5000.00);
            })
            ->where('tab', Type::CITY_RIDES);

        if ($request->get('tab') == 2){

            if(!empty($request->search_from) && !empty($request->search_to))
            {
               $cityRide = $cityRide->where('from',$request->search_from)->where('to',$request->search_to);
            }
            elseif(!empty($request->search_from))
            {
                $cityRide = $cityRide->where('from',$request->search_from);
            }
            elseif(!empty($request->search_to))
            {
                $cityRide = $cityRide->where('to',$request->search_to);
            }
        }
        $cityRideCount = $cityRide->count();
        $cityRide = $cityRide->paginate(10, ['*'], 'page', $page);
        if ($request->ajax()) {
            $airport = getAirportList();
            $typeList = getCabType();
            if ($request->get('tab') == 0) {
                // if ($airPortPickupCount > 9) {
                    $html = view('settings.partials.airport_pickup', [
                        'airPortPickup' => $airPortPickup,
                        'city' => City::all(),
                        'airport' => $airport,
                        'typeList' => $typeList,
                    ])->render();
                    return response()->json([
                        'html' => $html,
                        'next_page' => $airPortPickup->currentPage() < $airPortPickup->lastPage() ? $airPortPickup->currentPage() + 1 : null
                    ]);
                // }
            } elseif ($request->get('tab') == 1) {
                // if ($airPortDropCount > 9) {
                    $html = view('settings.partials.airport_drop', [
                        'airPortDrop' => $airPortDrop,
                        'city' => City::all(),
                        'airport' => $airport,
                        'typeList' => $typeList,
                    ])->render();
                    return response()->json([
                        'html' => $html,
                        'next_page' => $airPortDrop->currentPage() < $airPortDrop->lastPage() ? $airPortDrop->currentPage() + 1 : null
                    ]);
                // }
            } elseif ($request->get('tab') == 2) {

                // if ($cityRideCount > 9) {
                    $html = view('settings.partials.city_ride_rows', [
                        'cityRide' => $cityRide,
                        'city' => City::all()
                    ])->render();
                    return response()->json([
                        'html' => $html,
                        'next_page' => $cityRide->currentPage() < $cityRide->lastPage() ? $cityRide->currentPage() + 1 : null
                    ]);
                // }
            }
        }
        return view('settings.cab-rate', compact(['city', 'airport','airPortPickup', 'airPortDrop', 'cityRide', 'airPortPickupCount', 'airPortDropCount', 'cityRideCount','airportPickupPercentage']));
    }

    public function list(Request $request)
    {
        try {
            $pageNumber = 1;
            $pageLength = 10;
            if (isset($request->start) && isset($request->length)) {
                $pageNumber = ($request->start / $request->length) + 1;
                $pageLength = $request->length;
            }
            $skip = ($pageNumber - 1) * $pageLength;
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id'];
            $orderByColumn = $columns[$orderColumnIndex] ?? 'id';
            $query = CabRate::query()->with(['cityFrom', 'cityTo']);
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get();
            $recordsFiltered = $recordsTotal;

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                'data' => $list,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Getting error of display cab rate list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }


    public function StoreUpdate(StoreCabRateRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($request->edit_from) {
                foreach ($request->edit_from as $key => $value) {

                    if ($request->tab == 3 && $request->edit_to[$key] == $value) {
                        return response()->json(['success' => false, 'message' => 'You can not add same destination.']);
                    }

                    $cabRateDetails = CabRate::where('tab', $request->tab)
                        ->where('from', $value)
                        ->where('to', $request->edit_to[$key])
                        // ->where('cab_id', $request->edit_cab_id[$key])
                        ->first();

                    $reverseCabRateDetails = CabRate::where('tab', $request->tab)
                        ->where('from', $request->edit_to[$key])
                        ->where('to', $value)
                        // ->where('cab_id', $request->edit_cab_id[$key])
                        ->first();
                    if ($reverseCabRateDetails && $cabRateDetails->id != $request->edit_id[$key]) {
                        return response()->json(['success' => false, 'message' => 'Already added for this destination.']);
                    }

                    if ($cabRateDetails && $cabRateDetails->id != $request->edit_id[$key]) {
                        return response()->json(['success' => false, 'message' => 'Already added for this destination.']);
                    }

                    $cabRate = CabRate::find($request->edit_id[$key]);
                    $cabRate->tab = $request->tab;
                    $cabRate->from = $value;
                    $cabRate->to = $request->edit_to[$key];
                    $cabRate->base_km = $request->edit_base_km[$key];
                    // $cabRate->cab_id = $request->edit_cab_id[$key];
                    // $cabRate->base_fare = $request->edit_base_fare[$key];
                    $cabRate->save();
                    if ($cabRate) {
                        $cabRateDelete = CabPriceType::where('cab_rate_id', $request->edit_id[$key])->delete();
                        if ($cabRateDelete) {
                            if ($request->edit_hatchback_base_fare[$key]) {
                                $cabRateTypeHatch = CabPriceType::store($cabRate->id, Type::CAB_HATCHBACK_ID, $request->edit_hatchback_base_fare[$key]);
                                if (!$cabRateTypeHatch) {
                                    DB::rollBack();
                                    Log::info('Getting error on saving Hatch back price.');
                                }
                            }

                            if ($request->edit_sedan_base_fare[$key]) {
                                $cabRateTypeSedan = CabPriceType::store($cabRate->id, Type::CAB_SEDAN_ID, $request->edit_sedan_base_fare[$key]);
                                if (!$cabRateTypeSedan) {
                                    DB::rollBack();
                                    Log::info('Getting error on saving sedan price.');
                                }
                            }

                            if ($request->edit_suv_base_fare[$key]) {
                                $cabRateTypeSuv = CabPriceType::store($cabRate->id, Type::CAB_SUV_ID, $request->edit_suv_base_fare[$key]);
                                if (!$cabRateTypeSuv) {
                                    DB::rollBack();
                                    Log::info('Getting error on saving suv price.');
                                }
                            }
                        }
                    }
                }
            }
            if ($request->from) {
                foreach ($request->from as $key => $value) {
                    if ($request->tab == 3 && $request->to[$key] == $value) {
                        return response()->json(['success' => false, 'message' => 'You can not add same destination.']);
                    }
                    $cabRateDetails = CabRate::where('tab', $request->tab)
                        ->where('from', $value)
                        ->where('to', $request->to[$key])
                        // ->where('cab_id', $request->cab_id[$key])
                        ->first();
                    $reverseCabRateDetails = CabRate::where('tab', $request->tab)
                        ->where('from', $request->to[$key])
                        ->where('to', $value)
                        // ->where('cab_id', $request->cab_id[$key])
                        ->first();

                    if ($cabRateDetails || $reverseCabRateDetails) {
                        return response()->json(['success' => false, 'message' => 'Already added for this destination.']);
                    }

                    $cabRate = new CabRate();
                    $cabRate->tab = $request->tab;
                    $cabRate->from = $value;
                    $cabRate->to = $request->to[$key];
                    $cabRate->base_km = $request->base_km[$key];
                    $cabRate->save();

                    if ($cabRate) {
                        if ($request->hatchback_base_fare[$key]) {
                            $cabRateTypeHatch = CabPriceType::store($cabRate->id, Type::CAB_HATCHBACK_ID, $request->hatchback_base_fare[$key]);
                            if (!$cabRateTypeHatch) {
                                DB::rollBack();
                                Log::info('Getting error on saving Hatch back price.');
                            }
                        }

                        if ($request->sedan_base_fare[$key]) {
                            $cabRateTypeSedan = CabPriceType::store($cabRate->id, Type::CAB_SEDAN_ID, $request->sedan_base_fare[$key]);
                            if (!$cabRateTypeSedan) {
                                DB::rollBack();
                                Log::info('Getting error on saving sedan price.');
                            }
                        }

                        if ($request->suv_base_fare[$key]) {
                            $cabRateTypeSuv = CabPriceType::store($cabRate->id, Type::CAB_SUV_ID, $request->suv_base_fare[$key]);
                            if (!$cabRateTypeSuv) {
                                DB::rollBack();
                                Log::info('Getting error on saving suv price.');
                            }
                        }
                    }

                    // for city rides copy same value
                    if ($request->tab == 3) {
                        $cabRate = new CabRate();
                        $cabRate->tab = $request->tab;
                        $cabRate->from = $request->to[$key];
                        $cabRate->to = $value;
                        $cabRate->base_km = $request->base_km[$key];
                        $cabRate->save();
                        if ($cabRate) {
                            if ($request->hatchback_base_fare[$key]) {
                                $cabRateTypeHatch = CabPriceType::store($cabRate->id, Type::CAB_HATCHBACK_ID, $request->hatchback_base_fare[$key]);
                                if (!$cabRateTypeHatch) {
                                    DB::rollBack();
                                    Log::info('Getting error on saving Hatch back price.');
                                }
                            }

                            if ($request->sedan_base_fare[$key]) {
                                $cabRateTypeSedan = CabPriceType::store($cabRate->id, Type::CAB_SEDAN_ID, $request->sedan_base_fare[$key]);
                                if (!$cabRateTypeSedan) {
                                    DB::rollBack();
                                    Log::info('Getting error on saving sedan price.');
                                }
                            }

                            if ($request->suv_base_fare[$key]) {
                                $cabRateTypeSuv = CabPriceType::store($cabRate->id, Type::CAB_SUV_ID, $request->suv_base_fare[$key]);
                                if (!$cabRateTypeSuv) {
                                    DB::rollBack();
                                    Log::info('Getting error on saving suv price.');
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cab rate added successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Getting error of store and update Cab rate :' . $e->getMessage());
            Log::error('Getting error of store and update Cab rate :' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Something went wrong,Please try again latter.']);
        }
    }

    public function edit(CabPriceDetails $priceDetails)
    {
        try {
            return view('settings.base-fare', compact('priceDetails'));
        } catch (\Exception $e) {
            Log::error('Getting error of display fleet-operator details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function delete(CabRate $cabRate)
    {
        try {
            $cabRate->delete();
            return response()->json(['status' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Getting error of delete cab rate :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function airportPickupPercentageCalculation(Request $request)
    {
        try{
            $slug = Type::AIRPORT_PICKUP_PERCENTAGE;

            $settingData = Environment::where('title', $slug)->first();
            $message = 'saved';

            $percentage = (double) $request->airport_pickup_percentage;
            if (!empty($settingData)) {
                $settingData->value = json_encode($percentage);
                $settingData->save();
                $message = 'updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => json_encode($percentage)
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Airport pickup percentage ' . $message . ' successfully.']);
        }catch(\Exception $e)
        {
            Log::info($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
