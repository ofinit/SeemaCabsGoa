<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function success($data = [], $message = "")
    {
        return response()->json(['status' => true, 'data' => $data, 'message' => $message], 200);
    }

    public function error($message = "", $status = 200)
    {
        return response()->json(['status' => false, 'message' => $message], $status);
    }
}
