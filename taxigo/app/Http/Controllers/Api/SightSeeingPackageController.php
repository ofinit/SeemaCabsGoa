<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SightSeeingPackageCabPriceResource;
use App\Http\Resources\Api\SightSeeingPackageResource;
use App\Models\SightSeeingPackageCabPrice;
use App\Models\SightSeeingPackages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SightSeeingPackageController extends ResponseController
{
    public function getSightSeeingPackagesList(Request $request)
    {
        try {
            $sightseeing_packages = SightSeeingPackages::with('packageImages');
            if($request->search)
            {
                $sightseeing_packages->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('location', 'like', '%'.$request->search.'%');
            }

            $sightseeing_packages = $sightseeing_packages->get();
            $data = SightSeeingPackageResource::collection($sightseeing_packages);
            return $this->success($data, 'Sight Seeing Packages fetched successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of sight seeing packages list: ' . $e->getMessage());
            Log::error('Getting error of sight seeing packages list: ' . $e->getLine());

            return $this->error('Something went wrong, Please try again later.');
        }
    }

    public function getCabList(Request $request)
    {
        try {
            $data = $request->validate([
                'sightseeing_id' => 'required|exists:sight_seeing_packages,id',
                'city_id' => 'required',
            ]);
            $user = Auth::user();

            // Check if the user is suspended
            if ($user && $user->status === 0) {
                return response()->json([
                    'status' => false,
                    'user_status' => false,
                    'message' => 'Account Suspended. Contact Support.'
                ]);
            }

            // Get Cab list price based on the sight seeing package id
            $packageCabPrices = SightSeeingPackageCabPrice::where('sight_seeing_package_id', $data['sightseeing_id'])->where('city_id', $data['city_id'])->first();
            if(empty($packageCabPrices))
            {
                return $this->error('No cabs found for this package.', 400);
            }
            $data = new SightSeeingPackageCabPriceResource($packageCabPrices);
            return $this->success($data, 'Cabs list fetched successfully.');
        }catch(ValidationException $e){
            return $this->error([
                'errors' => $e->errors(),
            ], 422);
        }
        catch (\Exception $e) {
            Log::error('Getting error of sight seeing packages cab price list: ' . $e->getMessage());
            Log::error('Getting error of sight seeing packages cab price list: ' . $e->getLine());

            return $this->error('Something went wrong, Please try again later.');
        }

    }
}
