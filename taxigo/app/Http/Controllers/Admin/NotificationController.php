<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotificationEnum;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notification.index');
    }

    public function list(Request $request)
    {
        try {
            $pageNumber = 1;
            $pageLength = 10;
            if (isset($request->start) && isset($request->length)) {
                $pageNumber = ($request->start / $request->length) + 1;
                $pageLength = $request->length;
            }
            $skip = ($pageNumber - 1) * $pageLength;
            $orderColumnIndex = $request->order[0]['column'] ?? '0';
            $orderBy = $request->order[0]['dir'] ?? 'desc';
            $columns = ['id'];
            $orderByColumn = $columns[$orderColumnIndex] ?? 'id';
            $search = $request->search ?? '';
            $query = Notification::query();
            if ($search) {
                $query->whereHas('bookingDetails.getDropTo', function ($q) use ($search) {
                    $q->where('name', 'Like', '%' . $search . '%');
                });
            }
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get();
            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                'data' => $list,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Getting error of display notification list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function markAllRead()
    {
        // Notification::where('user_id', NotificationEnum::FLEET_OPERATOR_ID)
        Notification::where('user_id', NotificationEnum::ADMIN_ID)
            ->where('status', NotificationEnum::UNREAD)
            ->update(['status' => NotificationEnum::READ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function delete($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found'], 404);
        }
        $notification->delete();
        return response()->json(['status' => true,'message' => 'Notification deleted successfully.']);
    }

    public function create()
    {
        return view('notification.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string|max:255',
        ]);
        try {
            $notification = new NotificationService();

            $title = $request->title;
            $body = $request->text;
            $link = $request->link;
            $showInApp = $request->has('show_in_app') ? 1 : 0;
            $data = [];
            if($request->has('image'))
            {
                $image = $request->file('image');
                $path = 'notificationimages';

                $fileName = UploadImage($image, $path);
                $imagePath = $path . '/' . $fileName;
                $data['image'] = $imagePath;
            }

            $notification->setAllUserToken();

            if($showInApp) {
                $notification->storeAllUserNotification($title, $body, $data, $link);
            }

            if($notification->sendBulkNotification($title, $body, $data))
            {
                return redirect()->back()->with('success', 'Notification sent successfully.');
            }
            else
            {
                return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
            }
        } catch (\Exception $e) {
            Log::error('Getting error of send notification :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }

    }
}
