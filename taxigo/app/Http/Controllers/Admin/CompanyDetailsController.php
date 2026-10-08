<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\CompanyDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateCompanyDetailsRequest;

class CompanyDetailsController extends Controller
{
    public function index()
    {
        try {
            $country = Country::all();
            $companyDetails = CompanyDetails::whereNotNull('name')->first();
            $states = [];
            $city = [];
            if ($companyDetails) {
                $states = State::where('country_id', $companyDetails->country_id)->get();
                $city = City::where('state_id', $companyDetails->state_id)->get();
            }
            return view('settings.company-details', compact('country', 'companyDetails', 'states', 'city'));
        } catch (\Exception $e) {
            Log::error('Getting error of display company-details details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }


    public function storeUpdate(StoreUpdateCompanyDetailsRequest $request)
    {
        try {
            $message = 'created';
            $data = $request->all();
            if ($request->id) {
                $companyDetails = CompanyDetails::find($request->id);
                $companyDetails->fill($data);
                $companyDetails->save();
                $message = 'updated';
            } else {
                CompanyDetails::create($data);
            }
            return redirect()->back()->with('success', 'Company details ' . $message . ' successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Getting error of store and update company details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
