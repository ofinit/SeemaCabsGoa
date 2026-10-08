<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use App\Models\Country;
use Auth;
use Illuminate\Http\Request;
use App\Models\FleetOperator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\FleetOperatorBankDetails;
use App\Http\Requests\Admin\Setting\StoreFleetOperatorRequest;
use App\Http\Requests\Admin\Setting\UpdateFleetOperatorRequest;

class FleetOperatorController extends Controller
{
    public function index()
    {
        try {
            $country = Country::all();
            return view('settings.fleet-operator', compact('country'));
        } catch (\Exception $e) {
            Log::error('Getting error of display fleet-operator details :' . $e->getMessage());
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
            $query = FleetOperator::query();

            if ($search) {
                $query->where('company_name', 'like', "%" . $search . "%")
                    ->orWhere('person_name', 'like', "%" . $search . "%")
                    ->orWhere('email_id', 'like', "%" . $search . "%")
                    ->orWhere('pan_number', 'like', "%" . $search . "%")
                    ->orWhere('gst_number', 'like', "%" . $search . "%")
                    ->orWhereHas('getCountryDetails', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhereHas('getStateDetails', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhere('mobile_number', 'like', "%" . $search . "%");
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
            Log::error('Getting error of display operator list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }


    public function store(StoreFleetOperatorRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $message = 'created';
            $FleetOperator = FleetOperator::create($data);
            User::create([
                'name' => $request->company_name ?? '',
                'email' => $request->email_id ?? '',
                'password' => $request->password ?? '',
                'type' => Type::FleetOperator ?? '',
                'status' => Type::ActiveUser ?? '',
            ]);
            $data['fleet_perator_id'] = $FleetOperator->id;
            FleetOperatorBankDetails::create($data);
            DB::commit();
            return redirect()->back()->with('success', 'Fleet operator ' . $message . ' successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Getting error of store fleet operator :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function edit($id)
    {
        try {
            $country = Country::all();
            $flletOperator = FleetOperator::find($id);
            $states = State::where('country_id', $flletOperator->country_id)->get();
            $city = [];
            if (isset($flletOperator->state_id) && $flletOperator->state_id != null) {
                $city = City::where('state_id', $flletOperator->state_id)->get();
            }
            return view('settings.fleet-operator', compact('country', 'flletOperator', 'states', 'city'));
        } catch (\Exception $e) {
            Log::error('Getting error of display fleet-operator details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function update(UpdateFleetOperatorRequest $request)
    {
        try {
            DB::beginTransaction();
            $message = 'updated';
            $data = $request->all();
            $FleetOperator = FleetOperator::findOrFail($request->id);
            $FleetOperator->fill($data);
            $FleetOperator->save();

            FleetOperatorBankDetails::where('fleet_perator_id', $request->id)->update([
                'fleet_perator_id' => $request->id,
                'bank_name' => $data['bank_name'],
                'branch_name' => $data['branch_name'],
                'holder_name' => $data['holder_name'],
                'account_number' => $data['account_number'],
                'ifsc_code' => $data['ifsc_code'],
                'upi_id' => $data['upi_id'],
            ]);

            DB::commit();
            return redirect()->route('admin.setting.fleetOperators.index')->with('success', 'Fleet operator ' . $message . ' successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Getting error of store fleet operator :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function delete($id)
    {
        try {
            $flletOperator = FleetOperator::find($id);
            if (isset($flletOperator) && $flletOperator != null) {
                $flletOperator->delete();
            }
            return response()->json(['status' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Getting error of delete fleet-operator details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
