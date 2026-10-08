<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Exports\exportAdvertiserList;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateAdvertisementRequest;
use App\Models\Advertisement;
use App\Models\Advertiser;
use App\Models\City;
use App\Models\Country;
use App\Models\ScreenPrice;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AdvertisementController extends Controller
{
    public function index()
    {
        try {
            $country = Country::all();
            $indiaId = Country::where('name', 'India')->first()->id ?? Type::INDIA_ID;
            $location = State::where('country_id', $indiaId)->get();
            $customers = User::where('type', Type::CUSTOMER)->where('status', Type::ActiveUser)->get();

            $advertisementsScreens = ScreenPrice::get();

            return view('advertisements.submit-ad', compact('customers', 'country', 'location', 'advertisementsScreens'));
        } catch (\Exception $e) {
            Log::error('Getting error of display advertisement list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
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
            $search = $request->search ?? '';
            $query = Advertisement::query()->with('user', 'country', 'state', 'location');
            if ($search) {
                $query->where('billing_company_name', 'like', "%" . $search . "%")
                    ->orWhere('banner_url', 'like', "%" . $search . "%")
                    ->orWhere('billing_pan', 'like', "%" . $search . "%")
                    ->orWhere('billing_gst', 'like', "%" . $search . "%")
                    ->OrWhereRaw("DATE_FORMAT(start_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->OrWhereRaw("DATE_FORMAT(end_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("('male' LIKE CONCAT('%', ?, '%') AND gender = 1) OR ('female' LIKE CONCAT('%', ?, '%') AND gender = 0) OR ('other' LIKE CONCAT('%', ?, '%') AND gender = 2)OR ('All' LIKE CONCAT('%', ?, '%') AND gender = 10001)", [$search, $search, $search, $search])
                    ->orWhereHas('country', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhereHas('user', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhereHas('state', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    });
            }
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
            Log::error('Getting error of display advertisement list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storeUpdate(StoreUpdateAdvertisementRequest $request)
    {

        try {
            $message = 'created';
            $data = $request->all();
            $data['screens'] = json_encode($data['screens']);
            $data['start_date'] = Carbon::parse($data['start_date'])->format('Y-m-d');
            $data['start_time'] = Carbon::parse($data['start_time'])->format('H:i');
            $data['end_date'] = Carbon::parse($data['end_date'])->format('Y-m-d');
            $data['end_time'] = Carbon::parse($data['end_time'])->format('H:i');
            $data['default'] = $data['default'] ?? 0;
            if ($data['country_id'] == 'all') {
                $data['country_id'] = Type::AllValue;
            }
            if ($data['state_id'] == 'all') {
                $data['state_id'] = Type::AllValue;
            }
            if ($data['location'] == 'all') {
                $data['location'] = Type::AllValue;
            }
            if ($data['gender'] == 'all') {
                $data['gender'] = Type::AllValue;
            }

            if (isset($data['banner_image']) && $data['banner_image'] != null) {
                $imageName = UploadImageWebpConversion($data['banner_image'], 'banner');
                $data['banner_image'] = $imageName;
            }
            if ($data['id']) {
                $advertisement = Advertisement::find($data['id']);
                if ($advertisement->banner_image && (isset($data['banner_image']) && $data['banner_image'] != null)) {
                    RemoveImage($advertisement->banner_image, 'banner');
                }
                $advertisement->update($data);
                $message = 'updated';
            } else {
                $advertisement = Advertisement::create($data);
            }
            return redirect()->route('admin.advertisements.index')->with('success', 'Submit adds ' . $message . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store advertisement submit :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function edit(Advertisement $advertisement)
    {
        try {
            $states = State::query();
            if ($advertisement->country_id == Type::AllValue) {
                $states = $states->get();
            } else {
                $states = $states->where('country_id', $advertisement->country_id)->get();
            }
            $city = City::query();
            if ($advertisement->state_id == Type::AllValue) {
                $city = $city->get();
            } else {
                $city = $city->where('state_id', $advertisement->state_id)->get();
            }

            $advertisementsScreens = ScreenPrice::get();
            $genderCount = User::where('type', Type::CUSTOMER);
            $countryCount = User::where('type', Type::CUSTOMER);
            $stateCount = User::where('type', Type::CUSTOMER);
            $locationCount = User::where('type', Type::CUSTOMER);
            if ($advertisement->gender != Type::AllValue) {
                $genderCount = $genderCount->where('gender', $advertisement->gender);
            }

            if ($advertisement->country_id != Type::AllValue) {
                $countryCount = $countryCount->where('country_id', $advertisement->country_id);
            }

            if ($advertisement->state_id != Type::AllValue) {
                $stateCount = $stateCount->where('state_id', $advertisement->state_id);
            }

            if ($advertisement->location != Type::AllValue) {
                $locationCount = $locationCount->where('state_id', $advertisement->location);
            }
            $genderCount = $genderCount->count();
            $countryCount = $countryCount->count();
            $stateCount = $stateCount->count();
            $locationCount = $locationCount->count();
            $data = [
                'location' => $states = State::all(),
                'country' => Country::all(),
                'advertisement' => State::query(),
                'states' => $states,
                'city' => $city,
                'advertisement' => $advertisement,
                'customers' => User::where('type', Type::CUSTOMER)->where('status', Type::ActiveUser)->get(),
                'genderCount' => $genderCount,
                'countryCount' => $countryCount,
                'stateCount' => $stateCount,
                'locationCount' => $locationCount,
                'advertisementsScreens' => $advertisementsScreens
            ];
            return view('advertisements.submit-ad')->with($data);
        } catch (\Exception $e) {
            Log::error('Getting error of display advertisement edit details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function delete(Advertisement $advertisement)
    {
        try {
            $advertisement->delete();
            return response()->json(['status' => true, 'message' => 'Adds deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Getting error of delete adds :' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong,Please try again latter.']);
        }
    }

    public function getAdvertiserDetails(Request $request)
    {
        try {
            $advertiser = Advertiser::where('add_id', $request->id)->get();
            $html = view('advertisements.getAdvertiserDetails', compact('advertiser'))->render();
            return response()->json(['status' => true, 'Message' => 'Data get successfully.', 'html' => $html]);
        } catch (\Throwable $th) {
            Log::error('Getting error of fetched advertiser list:' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Something went wrong, Please try again latter.']);
        }
    }

    public function getAdvertiserDetailsAmount(Request $request)
    {
        try {
            $advertiser = Advertisement::find($request->id);
            return response()->json(['status' => true, 'Message' => 'Data get successfully.', 'amount' => $advertiser->total_amount ?? 0]);
        } catch (\Throwable $th) {
            Log::error('Getting error of fetched advertiser list:' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Something went wrong, Please try again latter.']);
        }
    }

    public function exportAdvertiserList()
    {
        return Excel::download(new exportAdvertiserList, 'AdvertiserList.xlsx');
    }

    public function getTotalCustomerCount(Request $request)
    {
        try {
            $user = User::where('type', Type::CUSTOMER);
            if ($request->type == 'gender') {
                if ($request->value != 'all') {
                    $user->where('gender', $request->value)->count();
                }
            } elseif ($request->type == 'country') {
                if ($request->value != 'all') {
                    $user->where('country_id', $request->value)->count();
                }
            } elseif ($request->type == 'state') {
                if ($request->value != 'all') {
                    $user->where('state_id', $request->value)->count();
                }
            } elseif ($request->type == 'location') {
                if ($request->value != 'all') {
                    $user->where('state_id', $request->value)->count();
                }
            }
            $user = $user->count();
            return response()->json(['status' => true, 'message' => 'Count fetched successfully', 'count' => $user]);
        } catch (\Throwable $th) {
            Log::error('Getting error of fetched customer count:' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Something went wrong, Please try again latter.']);
        }
    }

    public function getScreenAmount(Request $request)
    {
        try {
            $priceDetails = ScreenPrice::find($request->id);
            return response()->json(['status' => true, 'message' => 'Amount get successfully', 'amount' => $priceDetails->price_per_day ?? 0]);
        } catch (\Throwable $th) {
            Log::error('Getting error of fetched screen amount:' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Something went wrong, Please try again latter.']);
        }
    }
}
