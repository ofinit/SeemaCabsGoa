<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentManagementController extends Controller
{
    public function index($slug)
    {
        try {
            $details = CrmPages::where('type', $slug)->first();
            $inclusionDetails = CrmPages::where('type', 'inclusion')->first();
            $exclusionsDetails = CrmPages::where('type', 'exclusions')->first();
            $page = 'contents.' . $slug;
            return view('contents.pages', compact('slug', 'details', 'inclusionDetails', 'exclusionsDetails'));
        } catch (\Throwable $th) {
            Log::error('Getting error of display content management page :' . $th->getMessage());
            return redirect()->back()->with('error', 'Somthing went wrong, Please try again latter.');
        }
    }

    public function storeUpdate(Request $request)
    {
        try {
            $details = CrmPages::where('type', $request->type)->first();
            if ($details) {
                if ($request->inclusion) {
                    $inclusionDetails = CrmPages::where('type', $request->inclusion)->first();
                    if (!empty($inclusionDetails)) {
                        $inclusionDetails->content = $request->content;
                        $inclusionDetails->save();
                    } else {
                        CrmPages::create([
                            'type' => $request->inclusion,
                            'content' => $request->content
                        ]);
                    }
                } else {
                    $details->content = $request->content;
                    $details->save();
                }
            }
            return redirect()->back()->with('success', $details->title . ' content updated successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store coupon :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
