<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrmPages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageContentController extends ResponseController
{
    public function getPageContent($slug)
    {
        try {
            $crmPage = CrmPages::where('type', $slug)->select('title', 'type', 'content')->first();
            if (empty($crmPage)) {
                return $this->error('Invalid slug.');
            }
            return $this->success($crmPage, 'Page content get successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of get page content :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }
}
