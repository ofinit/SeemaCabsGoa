<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreenPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScreenPriceController extends Controller
{
    public function index()
    {
        try {
            $priceLists = ScreenPrice::all();
            return view('advertisements.add-pricing', compact('priceLists'));
        } catch (\Exception $e) {
            Log::error('Getting error of display screen price form :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function storeUpdate(Request $request)
    {
        try {
            if ($request->screen) {
                $message = 'created';
                foreach ($request->screen as $key => $value) {
                    $priceDetails = ScreenPrice::where('screen', $value)->first();
                    if ($priceDetails) {
                        $priceDetails->price_per_day = $request->value[$key];
                        $priceDetails->save();
                        $message = 'updated';
                    } else {
                        if (isset($request->value[$key]) && $request->value[$key] != null) {
                            ScreenPrice::create(
                                [
                                    'title' => $request->title[$key],
                                    'screen' => $value,
                                    'price_per_day' => $request->value[$key],
                                ]
                            );
                        }
                    }
                }
            }
            return redirect()->back()->with('success', 'Screen price ' . $message . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store screen price submit :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
