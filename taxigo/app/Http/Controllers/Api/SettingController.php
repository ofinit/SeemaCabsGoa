<?php

namespace App\Http\Controllers\Api;

use App\Enums\CustomerDetailsEnum;
use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\Environment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends ResponseController
{
    public function settingDetails()
    {
        try {

            $envoirements = Environment::whereIn('title', [
                Type::AppLogo,
                Type::SplashScreenLogo,
                Type::AppUpdateTitle,
                Type::AppVersion,
                Type::AppIosUrl,
                Type::AppAndroidUrl,
                Type::PAYMENT_KEY,
                Type::RAZORPAY_ENABLED,
                Type::CASHFREE_APP_ID,
                Type::CASHFREE_SECRET_KEY,
                Type::CASHFREE_MODE,
                Type::CASHFREE_ENABLED,
                Type::PRIMARY_PAYMENT_GATEWAY,
                'google_client_id',
            ])->pluck('value', 'title');


            $data = [];
            $data['reason'] = getDeleteReasonList();
            $data['model'] = getModal();
            $data['type'] = getCabType();
            $data['airport_list'] = getPortTransferList();
            $data['country_list'] = getCountryList();
            $data['state_list'] = getStateList();

            $data['logo'] = $envoirements[Type::AppLogo] ? asset('storage/logo/' . $envoirements[Type::AppLogo]) : null;
            $data['splashscreenlogo'] = $envoirements[Type::SplashScreenLogo] ? asset('storage/logo/' . $envoirements[Type::SplashScreenLogo]) : null;


            if (empty($envoirements[Type::AppUpdateTitle])) {
                $data['update'] = 'Update not available.';
            } else {
                $data['update'] = $envoirements[Type::AppUpdateTitle] ?? null;
                $data['appDetails']['version'] = $envoirements[Type::AppVersion] ?? null;
                $data['appDetails']['AppIosUrl'] = $envoirements[Type::AppIosUrl] ?? null;
                $data['appDetails']['AppAndroidUrl'] = $envoirements[Type::AppAndroidUrl] ?? null;
            }

            $razorpayEnabledVal = $envoirements[Type::RAZORPAY_ENABLED] ?? '1';
            $data['razorpay_enabled'] = ($razorpayEnabledVal === '1' || $razorpayEnabledVal === 1 || $razorpayEnabledVal === true || $razorpayEnabledVal === 'true');
            $data['payment_key'] = $envoirements[Type::PAYMENT_KEY] ?? null;
            // This endpoint is public. Secrets stay server-side; the keys are kept
            // (as null) only so older app builds that read them don't break.
            $data['payment_secrete_key'] = null;

            $cashfreeEnabledVal = $envoirements[Type::CASHFREE_ENABLED] ?? '0';
            $cashfreeAppId = $envoirements[Type::CASHFREE_APP_ID] ?? null;
            $cashfreeConfigured = !empty($cashfreeAppId) && !empty($envoirements[Type::CASHFREE_SECRET_KEY] ?? null);
            $data['cashfree_enabled'] = ($cashfreeEnabledVal === '1' || $cashfreeEnabledVal === 1 || $cashfreeEnabledVal === true || $cashfreeEnabledVal === 'true') && $cashfreeConfigured;
            $data['cashfree_app_id'] = $cashfreeAppId;
            $data['cashfree_mode'] = strtolower($envoirements[Type::CASHFREE_MODE] ?? 'sandbox');
            $data['primary_payment_gateway'] = $envoirements[Type::PRIMARY_PAYMENT_GATEWAY] ?? 'both';


            $data['google_client_id'] = $envoirements['google_client_id'] ?? null;
            $data['google_client_secret'] = null;
            $data['min_booking_time'] = getMinimumBookingTime() ?? null;
            $data['max_booking_time'] = getMaximumBookingTime() ?? null;
            $data['max_cancellation_time'] = getMaxCancellationHour() ?? null;
            $data['min_cancellation_time'] = getMinCancellationHour() ?? null;
            $data['fleet_operator_phone_one'] = CustomerDetailsEnum::FLEET_OPERATOR_PHONE_ONE;
            $data['fleet_operator_phone_two'] = CustomerDetailsEnum::FLEET_OPERATOR_PHONE_TWO;
            $data['fleet_operator_name'] = CustomerDetailsEnum::FLEET_OPERATOR_NAME;
            $data['fleet_operator_address'] = CustomerDetailsEnum::FLEET_OPERATOR_ADDRESS;
            $data['additional_charges'] = [
                'Tax',
                'Airport Entry',
                'Toll Charges',
                'Driver Charges',
                'All Covered!'
            ];

            return $this->success($data, 'Setting details get successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of get setting details :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
