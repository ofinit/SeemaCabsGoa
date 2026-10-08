<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Exports\ExportCabDetailsList;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cabs\StoreCabRequest;
use App\Http\Requests\Admin\Cabs\StoreSurgePriceRequest;
use App\Http\Requests\Admin\Cabs\UpdateCabRequest;
use App\Http\Resources\CabDetailsResource;
use App\Models\AdditionalKmCharges;
use App\Models\BaseFare;
use App\Models\Cab;
use App\Models\CabColor;
use App\Models\CabModel;
use App\Models\City;
use App\Models\Driver;
use App\Models\Environment;
use App\Models\FleetOperator;
use App\Models\NoOfKm;
use App\Models\WaitingCharges;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CabManagementController extends Controller
{

    public function index()
    {
        return view('cab-management.view-cabs');
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
            $query = Cab::query();
            if (Auth()->user()->type != Type::ADMIN) {
                $query->where('fleet_operator_id', Auth::id());
            }
            // Search
            if ($search = $request->search) {
                $searchLower = strtolower($search);
                $query->where(function ($query) use ($search) {
                    $query->whereHas('getCityDetails', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                        ->orWhereHas('getCabModelDetails', function ($data) use ($search) {
                            $data->where('name', 'like', "%" . $search . "%");
                        })
                        ->orWhereHas('getColorDetails', function ($data) use ($search) {
                            $data->where('name', 'like', "%" . $search . "%");
                        })
                        ->orWhereHas('getAssignedDriverDetails', function ($query) use ($search) {
                            $query->where('name', 'like', "%" . $search . "%")
                                ->orWhere('mobile', 'like', "%" . $search . "%");
                        })->orWhere('number', 'like', "%" . $search . "%");
                    // ->orWhereHas('getFleetOperatorDetails', function ($data) use ($search) {
                    //     $data->where('person_name', 'like', "%" . $search . "%");
                    // });
                });
                $query->orWhereRaw("('hatchback' LIKE CONCAT('%', ?, '%') AND type = 1) OR ('sedan' LIKE CONCAT('%', ?, '%') AND type = 2) OR ('suv' LIKE CONCAT('%', ?, '%') AND type = 3)", [$searchLower, $searchLower, $searchLower])->orWhereRaw("('active' LIKE CONCAT('%', ?, '%') AND status != 0) OR ('suspend' LIKE CONCAT('%', ?, '%') AND status = 0)", [$searchLower, $searchLower]);
            }
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get();
            foreach ($list as $key => $value) {
                $value['city_list'] = $value->zone_names;
            }
            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                'data' => $list,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Getting error of display cab details list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function create()
    {
        try {
            $baseFare = BaseFare::all();
            $noOfKms = NoOfKm::all();
            $additional = AdditionalKmCharges::all();
            $waitingChar = WaitingCharges::all();
            $citys = City::all();
            $cabModels = CabModel::all();
            $cabColors = CabColor::all();
            $fleetOperator = FleetOperator::all();
            $data = [
                'citys' => $citys,
                'cabModels' => $cabModels,
                'cabColors' => $cabColors,
                'fleetOperator' => $fleetOperator,
                'baseFare' => $baseFare,
                'noOfKms' => $noOfKms,
                'additional' => $additional,
                'waitingChar' => $waitingChar,
            ];
            return view('cab-management.add-cabs')->with($data);
        } catch (\Throwable $th) {
            Log::error('Getting error of display cab create form :' . $th->getMessage());
            return redirect()->back()->with('error', 'Somthing went wrong, Please try again latter.');
        }
    }
    public function store(StoreCabRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $cab = Cab::store($data);
            if (isset($request->driver_name) && count($request->driver_name) > 0) {
                $driveDetails = $this->storeDeriverDetails($cab->id, $data);
                if (!$driveDetails) {
                    DB::rollBack();
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Cab details added successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Getting error of store cab details :' . $th->getMessage());
            return redirect()->back()->with('error', 'Somthing went wrong, Please try again latter.');
        }
    }

    public function edit($id)
    {
        try {
            $cab = Cab::find($id);
            $baseFare = BaseFare::all();
            $noOfKms = NoOfKm::all();
            $additional = AdditionalKmCharges::all();
            $waitingChar = WaitingCharges::all();
            $citys = City::all();
            $cabModels = CabModel::all();
            $cabColors = CabColor::all();
            $fleetOperator = FleetOperator::all();
            $data = [
                'citys' => $citys,
                'cabModels' => $cabModels,
                'cabColors' => $cabColors,
                'fleetOperator' => $fleetOperator,
                'baseFare' => $baseFare,
                'noOfKms' => $noOfKms,
                'additional' => $additional,
                'waitingChar' => $waitingChar,
                'cab' => $cab,
            ];
            return view('cab-management.edit-cabs')->with($data);
        } catch (\Throwable $th) {
            Log::error('Getting error of display cab create form :' . $th->getMessage());
            return redirect()->back()->with('error', 'Somthing went wrong, Please try again latter.');
        }
    }

    public function storeDeriverDetails($cabId, $data)
    {
        try {
            foreach ($data['driver_name'] as $index => $driverName) {
                $driver = new Driver();
                $driver->cab_id = $cabId;
                $driver->name = $driverName;
                $driver->mobile = $data['driver_mobile'][$index];
                $driver->bank_name = $data['bank_name'][$index];
                $driver->branch_name = $data['branch_name'][$index];
                $driver->account_holder_name = $data['account_holder_name'][$index];
                $driver->account_number = $data['account_number'][$index];
                $driver->ifsc_code = $data['ifsc_code'][$index];
                $driver->upi_id = $data['upi_id'][$index];
                $driver->driving_license_number = $data['driving_license_number'][$index];
                $driver->aadhar_card_number = $data['aadhar_card_number'][$index];
                if (isset($data['driver_profile_picture'][$index]) && $data['driver_profile_picture'][$index] != null) {
                    $driver->profile_picture = UploadImage($data['driver_profile_picture'][$index], 'profile');
                }
                if (isset($data['front_driver_license'][$index]) && $data['front_driver_license'][$index] != null) {
                    $driver->front_license = UploadImage($data['front_driver_license'][$index], 'license');
                }
                if (isset($data['back_driver_license'][$index]) && $data['back_driver_license'][$index] != null) {
                    $driver->back_license = UploadImage($data['back_driver_license'][$index], 'license');
                }
                if (isset($data['front_aadhar_card'][$index]) && $data['front_aadhar_card'][$index] != null) {
                    $driver->front_aadhar_card = UploadImage($data['front_aadhar_card'][$index], 'aadhar');
                }
                if (isset($data['back_aadhar_card'][$index]) && $data['back_aadhar_card'][$index] != null) {
                    $driver->back_aadhar_card = UploadImage($data['back_aadhar_card'][$index], 'aadhar');
                }

                $driver->assignDriver = (isset($data['assignDriver'][$index]) && $data['assignDriver'][$index] != null) ? 1 : null;
                $driver->save();
            }

            return true;
        } catch (\Throwable $th) {
            Log::error('Getting error of store driver details :' . $th->getMessage());
            return false;
        }
    }

    public function update(UpdateCabRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $cab = Cab::updateDetails($data);

            if (isset($request->edit_driver_name) && count($request->edit_driver_name) > 0) {
                $driveDetails = $this->updateDeriverDetails($cab->id, $data);
                if (!$driveDetails) {
                    DB::rollBack();
                }
            }
            if (isset($request->driver_name) && count($request->driver_name) > 0) {
                $driveDetails = $this->storeDeriverDetails($cab->id, $data);
                if (!$driveDetails) {
                    DB::rollBack();
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Cab details updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Getting error of store cab details :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    public function updateDeriverDetails($cabId, $data)
    {
        try {
            foreach ($data['edit_driver_name'] as $index => $driverName) {
                if (isset($data['edit_driver_id'][$index]) && $data['edit_driver_id'][$index] != null) {
                    $driver = Driver::find($data['edit_driver_id'][$index]);
                    $driver->cab_id = $cabId;
                    $driver->name = $driverName;
                    $driver->mobile = $data['edit_driver_mobile'][$index];
                    $driver->bank_name = $data['edit_bank_name'][$index];
                    $driver->branch_name = $data['edit_branch_name'][$index];
                    $driver->account_holder_name = $data['edit_account_holder_name'][$index];
                    $driver->account_number = $data['edit_account_number'][$index];
                    $driver->ifsc_code = $data['edit_ifsc_code'][$index];
                    $driver->upi_id = $data['edit_upi_id'][$index];
                    $driver->driving_license_number = $data['edit_driving_license_number'][$index];
                    $driver->aadhar_card_number = $data['edit_aadhar_card_number'][$index];
                    if (isset($data['edit_driver_profile_picture'][$index]) && $data['edit_driver_profile_picture'][$index] != null) {
                        $driver->profile_picture = UploadImage($data['edit_driver_profile_picture'][$index], 'profile');
                    }
                    if (isset($data['edit_front_driver_license'][$index]) && $data['edit_front_driver_license'][$index] != null) {
                        $driver->front_license = UploadImage($data['edit_front_driver_license'][$index], 'license');
                    }
                    if (isset($data['edit_back_driver_license'][$index]) && $data['edit_back_driver_license'][$index] != null) {
                        $driver->back_license = UploadImage($data['edit_back_driver_license'][$index], 'license');
                    }
                    if (isset($data['edit_front_aadhar_card'][$index]) && $data['edit_front_aadhar_card'][$index] != null) {
                        $driver->front_aadhar_card = UploadImage($data['edit_front_aadhar_card'][$index], 'aadhar');
                    }
                    if (isset($data['edit_back_aadhar_card'][$index]) && $data['edit_back_aadhar_card'][$index] != null) {
                        $driver->back_aadhar_card = UploadImage($data['edit_back_aadhar_card'][$index], 'aadhar');
                    }
                    if (isset($data['assign_' . $driver->id]) && $data['assign_' . $driver->id] == 1) {
                        $driver->assignDriver = $data['assign_' . $driver->id];
                    } else {
                        $driver->assignDriver = 0;
                    }
                    $driver->save();
                }
                if ((isset($data['deletedFleg'][$index]) && $data['deletedFleg'][$index] == 1) && (isset($data['edit_driver_id'][$index]) && $data['edit_driver_id'][$index] != null)) {
                    Driver::where('id', $data['edit_driver_id'][$index])->delete();
                }
            }
            return true;
        } catch (\Throwable $th) {
            Log::error('Getting error of update driver details :' . $th->getMessage());
            return false;
        }
    }

    public function view($id)
    {
        try {
            $cab = Cab::find($id);
            $html = view('cab-management.view-cab-details', compact('cab'))->render();
            return response()->json(['status' => true, 'message' => 'Data get successfully.', 'html' => $html]);
        } catch (\Throwable $th) {
            Log::error('Getting error of display cab display model :' . $th->getMessage());
            return response()->json(['status' => false, 'message' => 'Somthing went wrong, Please try again latter.']);
        }
    }

    public function suspend($id)
    {
        try {
            $cab = Cab::find($id);
            if (!empty($cab)) {
                $change = ($cab->status == 1) ? 0 : 1;
                $cab->status = $change;
                $cab->save();
            }
            return redirect()->back()->with('success', 'Changed successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of change cab status :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function exportCabList()
    {
        return Excel::download(new ExportCabDetailsList, 'CabList.xlsx');
    }


    public function surgePrice()
    {
        try {
            $surgePrice = Environment::where('title', Type::SURGE_PRICE)->first();
            $data = [];
            if ($surgePrice) {
                $data = json_decode($surgePrice->value, true);
            }
            return view('cab-management.surge-pricing', compact('data'));
        } catch (\Throwable $th) {
            Log::error('Getting error of display surge price :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
    public function storeSurgePrice(StoreSurgePriceRequest $request)
    {
        try {
            $data = $request->validated();
            $message = 'saved';
            $slug = \Str::slug($request->surge_title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                $settingData->value = json_encode($data);
                $settingData->save();
                $message = 'updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => json_encode($data)
                ]);
            }
            return redirect()->back()->with('success', 'Surge price ' . $message . ' successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of store surge pricing data :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
