<?php

namespace App\Http\Controllers\Api;

use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorDeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class vendorController extends ResponseController
{
    public function update(Request $request)
    {
        try {
            $data = $request->all();

            VendorDeviceToken::updateOrCreate(
                ['device_id' => $data['device_id']], // search condition
                ['fcm_token' => $data['fcm_token']] // update this
            );

           $totalUser = User::where('type', Type::CUSTOMER)
                     ->where('status', Type::ActiveUser)
                     ->count();

            return $this->success([
                'device_id'   => $data['device_id'],
                'fcm_token'   => $data['fcm_token'],
                'total_user'  => $totalUser
            ], 'Details updated successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of update account details :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
