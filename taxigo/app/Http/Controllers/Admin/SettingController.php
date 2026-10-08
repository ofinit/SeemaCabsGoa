<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\Logo\StoreUpdateAdminPanelLogo;
use App\Http\Requests\Admin\Setting\Logo\StoreUpdateAppLogo;
use App\Http\Requests\Admin\Setting\Logo\StoreUpdateDriverAppLogo;
use App\Http\Requests\Admin\Setting\Logo\StoreUpdateDriverSplashLogo;
use App\Http\Requests\Admin\Setting\Logo\StoreUpdateInvoiceLogo;
use App\Http\Requests\Admin\Setting\Logo\StoreUpdateMailLogo;
use App\Http\Requests\Admin\Setting\Logo\StoreUpdateSplashScreenLogo;
use App\Http\Requests\Admin\Setting\StoreUpdateAdvanceBokkingRequest;
use App\Http\Requests\Admin\Setting\StoreUpdateSosNumberRequest;
use App\Http\Requests\Admin\Setting\Taxes\SaveUpdatePlatFormSmtpCredRequest;
use App\Http\Requests\Admin\Setting\Taxes\SaveUpdatePlatFormTaxRequest;
use App\Models\Environment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SettingController extends Controller
{

    public function logo(Request $request)
    {
        try {
            $envirements = Environment::all();
            return view('settings.logo', compact('envirements'));
        } catch (\Exception $e) {
            Log::error('Getting error of display logo list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function plateFormFee(Request $request)
    {
        try {
            $envoirements = Environment::get();
            return view('settings.aggregator-commission', compact('envoirements'));
        } catch (\Exception $e) {
            Log::error('Getting error of display logo list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function splashScreenLogo(StoreUpdateSplashScreenLogo $request)
    {
        try {
            $imageName = UploadImage($request->splashScreenLogo, 'logo');
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                RemoveImage($settingData->value, 'logo');
                $settingData->value = $imageName;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $imageName
                ]);
            }
            return redirect()->back()->with('success', 'Logo ' . $message . ' Successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store logo :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function appLogo(StoreUpdateAppLogo $request)
    {
        try {
            $imageName = UploadImage($request->appLogo, 'logo');
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                RemoveImage($settingData->value, 'logo');
                $settingData->value = $imageName;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $imageName
                ]);
            }
            return redirect()->back()->with('success', 'Logo ' . $message . ' Successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store app logo :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function driverSplashLogo(StoreUpdateDriverSplashLogo $request)
    {
        try {
            $imageName = UploadImage($request->driverSplashLogo, 'logo');
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                RemoveImage($settingData->value, 'logo');
                $settingData->value = $imageName;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $imageName
                ]);
            }
            return redirect()->back()->with('success', 'Logo ' . $message . ' Successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store driver spalsh logo :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }


    public function driverAppLogo(StoreUpdateDriverAppLogo $request)
    {
        try {
            $imageName = UploadImage($request->driverAppLogo, 'logo');
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                RemoveImage($settingData->value, 'logo');
                $settingData->value = $imageName;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $imageName
                ]);
            }
            return redirect()->back()->with('success', 'Logo ' . $message . ' Successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store driver app logo :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function adminPanelLogo(StoreUpdateAdminPanelLogo $request)
    {
        try {
            $imageName = UploadImage($request->adminPanelLogo, 'logo');
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                RemoveImage($settingData->value, 'logo');
                $settingData->value = $imageName;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $imageName
                ]);
            }
            return redirect()->back()->with('success', 'Logo ' . $message . ' Successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store admin pannel logo :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function invoiceLogo(StoreUpdateInvoiceLogo $request)
    {
        try {
            $imageName = UploadImage($request->invoiceLogo, 'logo');
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                RemoveImage($settingData->value, 'logo');
                $settingData->value = $imageName;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $imageName
                ]);
            }
            return redirect()->back()->with('success', 'Logo ' . $message . ' Successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store invoice logo :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function mailLogo(StoreUpdateMailLogo $request)
    {
        try {
            $imageName = UploadImage($request->mailLogo, 'logo');
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                RemoveImage($settingData->value, 'logo');
                $settingData->value = $imageName;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $imageName
                ]);
            }
            return redirect()->back()->with('success', 'Logo ' . $message . ' Successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of save store invoice logo :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storePlateFormFee(Request $request)
    {
        try {
            foreach ($request->title as $key => $value) {
                $slug = Str::slug($value);
                $settingData = Environment::where('title', $slug)->first();
                if (!empty($settingData)) {
                    $settingData->value = $request->value[$key];
                    $settingData->save();
                } else {
                    Environment::create([
                        'title' => $slug,
                        'value' => $request->value[$key]
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Aggregator commission saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store plate form reqs :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function plateFormTax(Request $request)
    {
        try {
            $envoirements = Environment::get();
            return view('settings.taxes', compact('envoirements'));
        } catch (\Exception $e) {
            Log::error('Getting error of display plat form taxes :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storePlateFormTax(Request $request)
    {
        try {
            foreach ($request->title as $key => $value) {
                $slug = Str::slug($value);
                $settingData = Environment::where('title', $slug)->first();
                if (!empty($settingData)) {
                    $settingData->value = $request->value[$key];
                    $settingData->save();
                } else {
                    Environment::create([
                        'title' => $slug,
                        'value' => $request->value[$key]
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Plate form fee saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store plate form reqs :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function smtpCred(Request $request)
    {
        try {
            $envoirements = Environment::whereIn('title', [
                'smtppassword',
                'smtphost',
                'smtpport',
                'smtpusername',
                'fromaddress',
                'smtpauthentication',
                'fromname'
            ])->pluck('value', 'title');

            return view('settings.smtp', compact('envoirements'));
        } catch (\Exception $e) {
            Log::error('Getting error of display smtp details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storeSmtpCred(SaveUpdatePlatFormSmtpCredRequest $request)
    {
        try {
            foreach ($request->title as $key => $value) {
                $slug = Str::slug($value);
                $settingData = Environment::where('title', $slug)->first();
                if (!empty($settingData)) {
                    $settingData->value = $request->value[$key];
                    $settingData->save();
                } else {
                    Environment::create([
                        'title' => $slug,
                        'value' => $request->value[$key]
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Plate form fee saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store plate form reqs :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function sosNumbers(Request $request)
    {
        try {
            $sosNumber = Environment::where('title', 'sosnumber')->first();
            return view('settings.sos-number', compact('sosNumber'));
        } catch (\Exception $e) {
            Log::error('Getting error of display sos number details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storeSosNumbers(StoreUpdateSosNumberRequest $request)
    {
        try {
            $slug = Str::slug($request->title);
            $settingData = Environment::where('title', $slug)->first();
            $message = 'Saved';
            if (!empty($settingData)) {
                $settingData->value = $request->sosNumber;
                $settingData->save();
                $message = 'Updated';
            } else {
                Environment::create([
                    'title' => $slug,
                    'value' => $request->sosNumber
                ]);
            }
            return redirect()->back()->with('success', 'SOS number ' . $message . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store sos number form reqs :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function advanceBooking(Request $request)
    {
        try {
            $envoirements = Environment::get();
            return view('settings.booking-and-cancellation', compact('envoirements'));
        } catch (\Exception $e) {
            Log::error('Getting error of display advance booking :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storeAdvanceBooking(StoreUpdateAdvanceBokkingRequest $request)
    {
        try {
            foreach ($request->title as $key => $value) {
                $slug = Str::slug($value);
                $settingData = Environment::where('title', $slug)->first();
                if (!empty($settingData)) {
                    $settingData->value = $request->value[$key];
                    $settingData->save();
                } else {
                    Environment::create([
                        'title' => $slug,
                        'value' => $request->value[$key]
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Advance booking saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of advance booking time :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storeAdvanceBookingCancellationForm(StoreUpdateAdvanceBokkingRequest $request)
    {
        try {
            foreach ($request->title as $key => $value) {
                $slug = Str::slug($value);
                $settingData = Environment::where('title', $slug)->first();
                if (!empty($settingData)) {
                    $settingData->value = $request->value[$key];
                    $settingData->save();
                } else {
                    Environment::create([
                        'title' => $slug,
                        'value' => $request->value[$key]
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Advance booking cancellation saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of advance booking cancellation time :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function appUpdate(Request $request)
    {
        try {
            $envoirements = Environment::get();
            return view('settings.app-update', compact('envoirements'));
        } catch (\Exception $e) {
            Log::error('Getting error of display app update :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storeAppUpdate(Request $request)
    {
        try {
            $valueCount = $request->all()['value'];
            foreach ($request->title as $key => $value) {
                $slug = Str::slug($value);
                $settingData = Environment::where('title', $slug)->first();
                $valueData = $request->value[$key];
                if (!empty($settingData)) {
                    $settingData->title = $slug;
                    $settingData->value = $valueData;
                    $settingData->save();
                } else {
                    Environment::create([
                        'title' => $slug,
                        'value' => $valueData
                    ]);
                }
            }
            return redirect()->back()->with('success', 'App update saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store app update form reqs :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function settings(Request $request)
    {
        try {
            $envoirements = Environment::pluck('value', 'title');
            return view('settings.index', compact('envoirements'));
        } catch (\Exception $e) {
            Log::error('Getting error of display smtp details :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function saveSettings(Request $request)
    {
        try {
            $data = $request->except('_token');
            foreach ($data as $key => $value) {
                $slug = Str::slug($value);

                $settingData = Environment::where('title', $slug)->first();
                if (!empty($settingData)) {
                    $settingData->value = $value;
                    $settingData->save();
                } else {
                    Environment::create([
                        'title' => $key,
                        'value' => $value
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Changes saved successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store plate form reqs :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
