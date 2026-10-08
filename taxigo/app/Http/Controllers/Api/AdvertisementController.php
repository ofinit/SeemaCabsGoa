<?php

namespace App\Http\Controllers\Api;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AdvertisementListResource;
use App\Models\Advertisement;
use App\Models\AdvertisementUserClick;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class AdvertisementController extends ResponseController
{

    public function index(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $currentDate = date('Y-m-d');
            $stateId = $request->state ?? 0;
            $all = Type::AllValue;

            $query = Advertisement::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate);

            if ($token) {
                $sanctumToken = PersonalAccessToken::findToken($token);
                if ($sanctumToken) {
                    $user = $sanctumToken->tokenable;

                    if ($user) {
                        $query = $query->where(function ($q) use ($user, $all) {
                            $q->where('gender', $user->gender)->orWhere('gender', $all);
                        });

                        if ($stateId > 0) {
                            $query = $query->where(function ($q) use ($stateId, $all) {
                                $q->where('location', $stateId)
                                    ->orWhere('location', $all)
                                    ->orWhere('country_id', $all)
                                    ->orWhere('state_id', $all);
                            });
                        }
                    }
                }
            } else {
                $query = $query->where(function ($q) use ($stateId, $all) {
                    $q->where('location', $stateId)->orWhere('location', $all);
                });
            }

            $allAds = $query->orderBy('id', 'desc')->get();
            $topAds = collect();
            $bottomAds = collect();

            foreach ($allAds as $ad) {
                $screens = json_decode($ad->screens, true);

                if (is_array($screens)) {
                    if (in_array(8, $screens)) {
                        $bottomAds->push($ad);
                    } elseif (in_array(7, $screens)) {
                        $topAds->push($ad);
                    } else {
                        $topAds->push($ad);
                    }
                } else {
                    $topAds->push($ad);
                }
            }
            if ($topAds->isEmpty()) {
                $topAds = Advertisement::where('default', 1)->orderBy('id', 'desc')->get()->filter(function ($ad) {
                    $screens = json_decode($ad->screens, true);
                    return !is_array($screens) || !in_array(8, $screens);
                });
            }

            if ($bottomAds->isEmpty()) {
                $bottomAds = Advertisement::where('default', 1)->orderBy('id', 'desc')->get()->filter(function ($ad) {
                    $screens = json_decode($ad->screens, true);
                    return is_array($screens) && in_array(8, $screens);
                });
            }

            return $this->success([
                'data' => AdvertisementListResource::collection($topAds->unique('id')),
                'second_data' => AdvertisementListResource::collection($bottomAds->unique('id')),
            ], 'Advertisement list fetched successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of fetched advertisement list :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again later.');
        }
    }



    public function manageClick(Request $request)
    {
        Log::error('Getting error of manage advertisement clicks ');
        try {
            
            $auth_user = Auth::user();
            //Create Advertisement User Data
            $advertisementUserClickData = array(
                'advertisement_id' => $request->id,
                'user_id' => $auth_user->id ?? null,
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
            );

            AdvertisementUserClick::create($advertisementUserClickData);

            return $this->success([], ' successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of manage advertisement clicks :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
