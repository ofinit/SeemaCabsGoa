<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SightSeeingAddEditRequest;
use App\Models\City;
use App\Models\SightSeeingPackageCabPrice;
use App\Models\SightSeeingPackageHasImages;
use App\Models\SightSeeingPackages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SightSeeingPackageController extends Controller
{
    public function index()
    {
        return view('sight-seeing-package.index');
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

            $query  = SightSeeingPackages::query();
            if ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            }
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get();
            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                'data' => $list,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Getting error of view sight seeing package list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function manage(Request $request)
    {
        $cities = City::all();
        return view('sight-seeing-package.create-edit', compact('cities'));
    }

    public function save(SightSeeingAddEditRequest $request)
    {
        DB::beginTransaction();
        try {

            // Save sightseeing package
            $sightseeing = SightSeeingPackages::updateOrCreate(['id' => $request->sight_seeing_id],$request->only([
                'title',
                'start_time',
                'end_time',
                'location',
                'description',
                'terms_and_condition'
            ]));
            if($request->has('images'))
            {
                foreach ($request->file('images') as $image) {
                    $path = 'sightseeingpackages';

                    $fileName = UploadImage($image, $path);
                    $imagePath = $path . '/' . $fileName;
                    SightSeeingPackageHasImages::create([
                        'sight_seeing_package_id' => $sightseeing->id,
                        'image' => $imagePath
                    ]);
                }
            }

            if($request->city)
            {
                foreach ($request->city as $key => $cityId) {
                    $sightSeeingPackageCabPrice = SightSeeingPackageCabPrice::where('city_id', $cityId)
                        ->where('sight_seeing_package_id' , $sightseeing->id)
                        ->first();

                    if ((isset($sightSeeingPackageCabPrice->id)) && ($sightSeeingPackageCabPrice->id != $request->package_cab_price_id[$key])) {
                        return response()->json(['success' => false, 'message' => 'Already added for this destination.'],400);
                    }
                    SightSeeingPackageCabPrice::updateOrCreate(['id' => $request->package_cab_price_id[$key]],[
                        'sight_seeing_package_id' => $sightseeing->id,
                        'city_id' => $cityId,
                        'hatchback_price' => $request->hatch_back_price[$key],
                        'sedan_price' => $request->sedan_price[$key],
                        'suv_price' => $request->suv_price[$key],
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => $request->sight_seeing_id ? 'Sightseeing package updated successfully!' : 'Sightseeing package created successfully!',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Getting error of save sight seeing package :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function edit(SightSeeingPackages $sightSeeingPackages)
    {
        $cities = City::all();
        $sightSeeingPackages->load('packageImages', 'packageCabPrices');
        return view('sight-seeing-package.create-edit', compact('sightSeeingPackages', 'cities'));
    }

    public function delete(Request $request, SightSeeingPackages $sightSeeingPackages)
    {
        try {
            if ($sightSeeingPackages->packageImages()->exists()) {
                foreach ($sightSeeingPackages->packageImages as $image) {
                    $imagePath = storage_path('app/public/sightseeingpackages/' . $image->image);
                    if (file_exists($imagePath)) {
                        @unlink($imagePath);
                    }

                    $image->delete();
                }
            }


            if ($sightSeeingPackages->packageCabPrices()->exists()) {
                $sightSeeingPackages->packageCabPrices()->delete();
            }
            $sightSeeingPackages->delete();

            return response()->json([
                'success' => true,
                'message' => 'Package and related data deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while deleting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePackageImage(Request $request)
    {
        try {
            $sightSeeingPackageImage = SightSeeingPackageHasImages::where('id', $request->id)->first();
            if(!$sightSeeingPackageImage){
                return response()->json(['success' => false, 'message' => 'Image not found'],404);
            }
            $sightSeeingPackageImage->delete();
            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Getting error of delete sight seeing package image :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function deletePackageCabPrice(Request $request)
    {
        try {
            $sightSeeingPackageCabPrice = SightSeeingPackageCabPrice::where('id', $request->id)->first();
            if(!$sightSeeingPackageCabPrice){
                return response()->json(['success' => false, 'message' => 'Cab price not found'],404);
            }
            $sightSeeingPackageCabPrice->delete();
            return response()->json(['success' => true, 'message' => 'Cab price deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Getting error of delete sight seeing package cab price :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
