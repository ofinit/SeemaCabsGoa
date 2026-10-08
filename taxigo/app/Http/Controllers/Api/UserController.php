<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateAccountDetailsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends ResponseController
{
    public function details()
    {
        try {
            $user = auth()->user();
            $data = [];
            $data["id"] = $user->id;
            $data["name"] = $user->name;
            $data["gender"] = $user->gender;
            $data["phone_number"] = $user->phone_number;
            $data["email"] = $user->email;
            $data['image'] = $user->user_image;
            $data['country'] = $user->getCountryDetails ? $user->getCountryDetails->name : null;
            $data['country_id'] = $user->country_id ?? null;
            $data['state'] = $user->getStateDetails ? $user->getStateDetails->name : null;
            $data['state_id'] = $user->state_id ?? null;
            $data['status'] = $user->status ?? 0;
            return $this->success($data, 'Details fetched successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of user account details :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function update(UpdateAccountDetailsRequest $request)
    {
        try {
            $data = $request->all();
            $user = auth()->user();
            if (isset($data['image']) && $data['image'] != null) {
                // destroy old image if exists
                if ($user->image) {
                    RemoveImage($user->image, 'customer');
                }
                // upload new image 
                $imageName = UploadImage($data['image'], 'customer');
                $data['image'] = $imageName;
            }
            $user->fill($data);
            $user->save();
            $data = [];
            $data["id"] = $user->id;
            $data["name"] = $user->name;
            $data["gender"] = $user->gender;
            $data["phone_number"] = $user->phone_number;
            $data["email"] = $user->email;
            $data['image'] = $user->user_image;
            $data['country'] = $user->getCountryDetails ? $user->getCountryDetails->name : null;
            $data['state'] = $user->getStateDetails ? $user->getStateDetails->name : null;
            return $this->success($data, 'Details updated successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of update account details :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            if (!isset($request->reason) && $request->reason == null) {
                return $this->error('Reason is required for delete account.');
            }
            $user = auth()->user();
            $user->delete_reason = $request->reason;
            $user->save();
            $user->delete();
            return $this->success([], 'Account deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of delete account :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
