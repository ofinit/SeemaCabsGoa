<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController  extends ResponseController
{
    public function list(Request $request)
    {
        $auth_user = Auth::user();
        $notifications = Notification::where('user_id', $auth_user->id)->orderBy('id', 'desc')->get();
        $data = NotificationResource::collection($notifications);
        return $this->success($data, 'Notification list fetched successfully.');
    }

    public function delete($id)
    {
        Notification::where('id', $id)->delete();
        return $this->success([],'Notification deleted successfully.');
    }
}
