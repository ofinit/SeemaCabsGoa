<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GetCabListResource;
use App\Models\BaseFare;
use App\Models\CabPriceDetails;
use App\Models\CabPriceType;
use App\Models\CabRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class CabController extends ResponseController
{
    public function getCabList(Request $request)
    {
        try {
            Log::info('cab list request data: ' .  json_encode($request->all()));
             
            $user = Auth::user();

            // Check if the user is suspended
            if ($user && $user->status === 0) {
                return response()->json([
                    'status' => false,
                    'user_status' => false,
                    'message' => 'Account Suspended. Contact Support.'
                ]);
            }
        DB::enableQueryLog();

            // Get the minimum ID per cab_type to avoid duplicates
            // $ids = CabPriceType::selectRaw('MIN(id) as id')
            //     ->groupBy('cab_type')
            //     ->pluck('id');

            // // Initialize query
            // $list = CabPriceType::with('cabRate');

            // // Apply route filter if available (supports bidirectional matching)
            // if ($request->filled(['tab', 'from', 'to'])) {
            //     $list->whereHas('cabRate', function ($query) use ($request) {
            //         $query->where('tab', $request->tab)
            //             ->where(function ($q) use ($request) {
            //                 $q->where(function ($q1) use ($request) {
            //                     $q1->where('from', $request->from)
            //                         ->where('to', $request->to);
            //                 })->orWhere(function ($q2) use ($request) {
            //                     $q2->where('from', $request->to)
            //                         ->where('to', $request->from);
            //                 });
            //             });
            //     });
            // } else {
            //     // Default fallback if no filters are provided
            //     $list->whereIn('id', $ids);
            // }
            
            // Always get MIN(id) per cab_type based on route filter
            $ids = CabPriceType::selectRaw('MIN(cab_price_types.id) as id')
                ->join('cab_rates', 'cab_price_types.cab_rate_id', '=', 'cab_rates.id')
                ->when($request->filled(['tab', 'from', 'to']), function ($query) use ($request) {
                    $query->where('cab_rates.tab', $request->tab)
                        ->where(function ($q) use ($request) {
                            $q->where(function ($q1) use ($request) {
                                $q1->where('cab_rates.from', $request->from)
                                    ->where('cab_rates.to', $request->to);
                            })->orWhere(function ($q2) use ($request) {
                                $q2->where('cab_rates.from', $request->from)
                                    ->where('cab_rates.to', $request->to);
                            });
                        });
                })
                ->whereNull('cab_price_types.deleted_at')
                ->whereNull('cab_rates.deleted_at')
                ->groupBy('cab_price_types.cab_type')
                ->pluck('id');
            
            // Get only records with selected IDs
            $listData = CabPriceType::with('cabRate')
                ->whereIn('id', $ids)
                ->get();
            


            // Execute the query and format response
            // $listData = $list->get();
                    Log::info('Executed Queries: ', DB::getQueryLog());

            $data = GetCabListResource::collection($listData);


 Log::info('cab list request data: ' .  json_encode($data));
 
            return $this->success($data, 'Cab list fetched successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of cab list: ' . $th->getMessage());
            Log::error('Getting error of cab list: ' . $th->getLine());

            return $this->error('Something went wrong, Please try again later.');
        }
    }
}
