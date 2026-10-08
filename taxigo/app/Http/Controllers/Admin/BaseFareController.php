<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\BaseFareStoreUpdateRequest;
use App\Models\CabPriceDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseFareController extends Controller
{
    public function index()
    {
        return view('settings.base-fare');
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
            $query = CabPriceDetails::query();
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
            Log::error('Getting error of display base fare list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }


    public function StoreUpdate(BaseFareStoreUpdateRequest $request)
    {
        try {
            $data = $request->all();
            // Check cab type unique validation 
            $priceDetails = CabPriceDetails::where('id', $request->id)->where('cab_type', $request->cab_type)->first();
            if (empty($priceDetails)) {
                $priceDetailsData = CabPriceDetails::where('cab_type', $request->cab_type)->first();
                if ($priceDetailsData) {
                    return redirect()->back()->with('error', 'The cab type has already been taken.');
                }
            }

            $message = 'created';
            if (isset($request->id) && $request->id != null) {
                $priceDetails = CabPriceDetails::find($request->id);
                $priceDetails->update($data);
                $message = 'updated';
            } else {
                CabPriceDetails::create($data);
            }
            return redirect()->route('admin.setting.baseFare.index')->with('success', 'Base fare ' . $message . ' successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Getting error of store and update base fare :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
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

    public function delete(CabPriceDetails $priceDetails)
    {
        try {
            $priceDetails->delete();
            return response()->json(['status' => true, 'message' => 'Deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Getting error of delete base fare details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
