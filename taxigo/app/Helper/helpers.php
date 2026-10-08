<?php

use App\Enums\Type;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Enums\BookingEnum;

use App\Models\Environment;
use App\Models\ScreenPrice;
use Illuminate\Support\Str;
use App\Models\Notification;
use Intervention\Image\Facades\Image;
use App\Enums\NotificationEnum;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

if (!function_exists('UploadImage')) {
    function UploadImage($image, $path)
    {
        if ($image) {
            $file = $image;
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $fileName, 'public');
            return $fileName;
        }
    }
}

if (!function_exists('UploadImageWebpConversion')) {
    function UploadImageWebpConversion($image, $path)
    {
        if ($image) {
            $file = $image;

            $tempFileName = time() . '.' . $file->getClientOriginalExtension();

            $tempPath = $file->storeAs('temp', $tempFileName, 'public');

            $tempFullPath = storage_path('app/public/' . $tempPath);

            $manager = ImageManager::gd();

            $img = $manager->read($tempFullPath);

            $webpFileName = pathinfo($tempFileName, PATHINFO_FILENAME) . '.webp';
            $finalPath = storage_path('app/public/' . $path . '/' . $webpFileName);

            if (!file_exists(dirname($finalPath))) {
                mkdir(dirname($finalPath), 0755, true);
            }

            $img->toWebp()->save($finalPath);

            unlink($tempFullPath);

            return $webpFileName;
        }
    }
}
if (!function_exists('RemoveImage')) {
    function RemoveImage($image, $file)
    {
        if ($image) {
            $oldFile = public_path('storage/' . $file . '/' . $image);
            if (Storage::exists($oldFile)) {
                Storage::delete($oldFile);
            }
        }
    }
}

if (!function_exists('getInvoiceLogo')) {
    function getInvoiceLogo()
    {
        $logo = Environment::where('title', 'invoicelogo')->first();
        return $logo->invoice_logo_image ?? '';
    }
}


if (!function_exists('getCabType')) {
    function getCabType($type = null)
    {
        if ($type != null) {
            $typeHtml = 'Hatchback';
            if ($type == 2) {
                $typeHtml = 'Sedan';
            } else if ($type == 3) {
                $typeHtml = 'SUV';
            }

            return $typeHtml;
        } else {
            $list = ['Hatchback', 'Sedan', 'SUV'];
            return $list;
        }
    }
}

if (!function_exists('getCountryList')) {
    function getCountryList()
    {
        $country = Country::select('id', 'name')->get();
        return $country;
    }
}
if (!function_exists('getStateList')) {
    function getStateList()
    {
        $state = State::select('id', 'name')->get();
        return $state;
    }
}

if (!function_exists('getModal')) {
    function getModal($modelVal = null)
    {
        if ($modelVal != null) {
            $model = 'Baleno, Swift or similar';

            if ($modelVal == 2) {
                $model = 'Dzire, Etios or similar';
            } else if ($modelVal == 3) {
                $model = 'Xylo, Ertiga or similar';
            }
            return $model;
        } else {
            $list = ['0' => 'Baleno, Swift or similar', '2' => 'Dzire, Etios or similar', '3' => 'Xylo, Ertiga or similar'];
            return $list;
        }
    }
}

if (!function_exists('getFuelType')) {
    function getFuelType($fuelType = null)
    {
        if ($fuelType != null) {
            $type = 'CNG';
            if ($fuelType == 2) {
                $type = 'Petrol';
            } else if ($fuelType == 3) {
                $type = 'Diesel';
            }

            return $type;
        } else {
            $list = ['CNG', 'Petrol', 'Diesel'];
            return $list;
        }
    }
}

if (!function_exists('getEnvoirements')) {
    function getEnvironments()
    {
        $environments = Environment::all();
        return $environments;
    }
}

if (!function_exists('getMaxCancellationHour')) {
    function getMaxCancellationHour()
    {
        $environments = (int) Environment::where('title', 'bookingcancellationhour')->first()?->value ?? 0;
        return $environments;
    }
}

if (!function_exists('getMinCancellationHour')) {
    function getMinCancellationHour()
    {
        $environments = (int) Environment::where('title', 'bookingcancellationminhour')->first()?->value ?? 0;
        return $environments;
    }
}

if (!function_exists('getMinimumBookingTime')) {
    function getMinimumBookingTime()
    {
        $advanceBooking = (int) Environment::where('title', Type::MIN_BOOKING_TIME)->first()?->value ?? 0;
        return $advanceBooking;
    }
}

if (!function_exists('getMaximumBookingTime')) {
    function getMaximumBookingTime()
    {
        $advanceBooking = (int) Environment::where('title', Type::BOOKING_CANCELLATION)->first()?->value ?? 0;
        return $advanceBooking;
    }
}

if (!function_exists('getDeleteReasonList')) {
    function getDeleteReasonList()
    {
        $list = [
            'High fares',
            'Unreliable service (late arrivals, cancellations)',
            'Difficult to book',
            'Limited availability',
            'Poor customer service',
            'Other (please specify)',
        ];

        return $list;
    }
}

if (!function_exists('getPortTransferList')) {
    function getPortTransferList()
    {
        $list = [
            'Dabolim Goa Airport (GOI)',
            'Manohar International Airport (GOX)',
        ];

        return $list;
    }
}

if (!function_exists('getAppLogo')) {
    function getAppLogo()
    {
        $logo = Environment::where('title', Type::AppLogoKey)->first();
        if ($logo) {
            return $logo->app_logo_image;
        } else {
            return asset('build/images/logo.svg');
        }
    }
}
if (!function_exists('getLogo')) {
    function getLogo()
    {
        $logo = Environment::where('title', Type::AdminPanelLogo)->first();
        if ($logo) {
            return $logo->admin_panel_logo_image;
        } else {
            return asset('build/images/logo.svg');
        }
    }
}
if (!function_exists('getMailLogo')) {
    function getMailLogo()
    {
        $logo = Environment::where('title', Type::MAIL_LOGO)->first();
        if ($logo) {
            return $logo->mail_logo_image;
        } else {
            return asset('build/images/logo.svg');
        }
    }
}

if (!function_exists('getAccountTypeList')) {
    function getAccountTypeList()
    {
        $list = [
            'Fixed',
            'Saving',
            'Salary'
        ];

        return $list;
    }
}

if (!function_exists('getPriceScreenList')) {
    function getPriceScreenList()
    {
        $list = [
            'Home Screen',
            'Book Ride Screen',
            'Confirm Booking Screen',
            'Driver Details Screen',
            'Ride Rating Screen',
            'Account Screen',
            'Searching For Taxi Screen Top Section',
            'Searching For Taxi Screen Bottom Section',
        ];

        return $list;
    }
}
if (!function_exists('getSmtpAuthentication')) {
    function getSmtpAuthentication()
    {
        $list = [
            'TLS',
            'SSL',
            'PLAIN'
        ];

        return $list;
    }
}


if (!function_exists('getScreenPriceValue')) {
    function getScreenPriceValue($screen)
    {
        $priceDetails = ScreenPrice::where('screen', $screen)->first();
        if (empty($priceDetails)) {

            return '';
        }
        return $priceDetails->price_per_day;
    }
}



if (!function_exists('getScreenTitleList')) {
    function getScreenTitleList($ids)
    {
        $priceDetails = ScreenPrice::whereIn('id', $ids)->pluck('title');

        return $priceDetails ?? null;
    }
}

if (!function_exists('getCurrencySign')) {
    function getCurrencySign()
    {
        return Type::CURRENCY_SIGN;
    }
}
if (!function_exists('getCountryUsingThirdPartyApi')) {
    function getCountryUsingThirdPartyApi()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://countriesnow.space/api/v0.1/countries/positions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET"
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $countries = json_decode($response, true);

        if (isset($countries['data'])) {
            foreach ($countries['data'] as $item) {
                if (isset($item['name']) && !empty($item['name'])) {
                    $country = new Country();
                    $country->name = $item['name'];
                    $country->save();
                }
            }
            return "Countries imported successfully.";
        } else {
            return "Failed to fetch country data.";
        }
    }
}
if (!function_exists('getStateUsingThirdPartyApi')) {
    function getStateUsingThirdPartyApi()
    {

        $countries = Country::all();

        foreach ($countries as $country) {
            $countryName = urlencode($country->name);
            $url = "https://countriesnow.space/api/v0.1/countries/states/q?country={$countryName}";
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPGET => true,
            ]);

            $response = curl_exec($curl);

            $result = json_decode($response, true);
            if (isset($result['data']['states']) && $result['data']['states'] != null) {
                foreach ($result['data']['states'] as $stateData) {
                    if (!empty($stateData['name'])) {
                        if (!State::where('name', $stateData['name'])->where('country_id', $country->id)->exists()) {
                            $state = new State();
                            $state->country_id = $country->id;
                            $state->name = $stateData['name'];
                            $state->save();
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('getAirportList')) {
    function getAirportList($airportId = null)
    {
        if ($airportId != null) {
            if ($airportId == 1) {
                $type = 'Dabolim Goa Airport (GOI)';
            } else if ($airportId == 2) {
                $type = 'Manohar International Airport (GOX)';
            }

            return $type;
        } else {
            $list = ['Dabolim Goa Airport (GOI)', 'Manohar International Airport (GOX)'];
            return $list;
        }
    }
}

if (!function_exists('randomPasswordFromEmail')) {
    function randomPasswordFromEmail($email, $date = '')
    {
        $date = $date ?? date('Y-m-d');

        // Extract parts
        $username = strstr($email, '@', true);
        $year = date('Y', strtotime($date));
        $dayMonth = date('md', strtotime($date));

        // Combine into password
        return $year . $username . $dayMonth;
    }
}

if (!function_exists('getNotificationList')) {
    function getNotificationList()
    {
        $notification = Notification::where('user_id', Auth::user()->id)->where('status', NotificationEnum::UNREAD)->get();
        return $notification ?? [];
    }
}

if (!function_exists('getFileUrl')) {
    function getFileUrl($path)
    {
        return asset("storage/$path");
    }
}
