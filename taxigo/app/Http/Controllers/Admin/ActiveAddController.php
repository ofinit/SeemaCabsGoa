<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportLeadDetails;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RenewAdvertisementRequest;
use App\Models\Advertisement;
use App\Models\AdvertisementUserClick;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ActiveAddController extends Controller
{
    public function index()
    {
        return view('advertisements.active-ads');
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
            $currentDate = date('Y-m-d');
            $query = Advertisement::where('start_date', '<=', $currentDate)->where('end_date', '>=', $currentDate)->with('user', 'country', 'state', 'location')
                ->withCount('advertisementUserClicks')
                ->withSum('impressions as impressions_total', 'views');
            if ($search) {
                // Grouped so the search can't pull in ads outside the active date range.
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($query) use ($search) {
                        $query->where('users.name', 'like', "%" . $search . "%");
                    })
                        ->orWhere('banner_url', 'like', "%" . $search . "%")
                        ->orWhereRaw("DATE_FORMAT(start_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                        ->orWhereRaw("DATE_FORMAT(end_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%']);
                });
            }
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get();
            $advertisement['screen_list'] = "--";
            if (!empty($list)) {
                foreach ($list as $advertisement) {
                    $views = (int) ($advertisement->impressions_total ?? 0);
                    $advertisement['views'] = $views;
                    $advertisement['ctr'] = $views > 0
                        ? round($advertisement->advertisement_user_clicks_count * 100 / $views, 2) . '%'
                        : '--';
                    if (!empty($advertisement->screens)) {
                        $listId = json_decode($advertisement->screens, true);
                        $advertisement['screen_list'] = getScreenTitleList($listId);
                    }
                }
            }
            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                'data' => $list,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Getting error of display active advertisement list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function renew(RenewAdvertisementRequest $request, Advertisement $advertisement)
    {
        try {
            $data = $request->all();
            $data['start_date'] = Carbon::parse($data['start_date'])->format('Y-m-d');
            $data['end_date'] = Carbon::parse($data['end_date'])->format('Y-m-d');
            $advertisement->fill($data);
            $advertisement->save();
            return redirect()->back()->with('success', 'Add renew successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of renew add :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    /** Pause a running ad (stops serving immediately) or resume it. */
    public function toggleStatus(Request $request, Advertisement $advertisement)
    {
        try {
            $paused = $advertisement->approval_status === Advertisement::PAUSED;
            $advertisement->approval_status = $paused ? Advertisement::APPROVED : Advertisement::PAUSED;
            $advertisement->status_changed_by = auth()->id();
            $advertisement->status_changed_at = now();
            $advertisement->status_note = $request->input('note');
            $advertisement->save();

            return response()->json([
                'status' => true,
                'approval_status' => $advertisement->approval_status,
                'message' => $paused ? 'Ad resumed.' : 'Ad paused.',
            ]);
        } catch (\Exception $e) {
            Log::error('Getting error of toggle ad status :' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong, Please try again later.'], 500);
        }
    }

    public function viewLeads(Request $request)
    {
        try {
            $advertisement = Advertisement::where('id',$request->id)->first();
            if(!$advertisement)
            {
                return response()->json(
                [
                    'status' => false,
                    'message' => 'Advertisement Not Found'
                ],404);
            }
            $view = view('advertisements.view-leads',compact('advertisement'))->render();
            $data = [
                'status' => true,
                'view' => $view,
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Getting error of view leads add :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function viewLeadsList(Request $request,$id)
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

            $query  = AdvertisementUserClick::with('user')->where('advertisement_id',$id);
            if ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->user->name ?? '-',
                        'mobile' => $item->user->phone_number ?? '-',
                        'email' => $item->user->email ?? '-',
                        'date' => Carbon::parse($item->created_at)->format('d-m-Y'),
                    ];
                });
            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsTotal,
                'data' => $list,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Getting error of view leads list add :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function exportLeadDetails(Request $request)
    {
        $query  = AdvertisementUserClick::with('user')->where('advertisement_id',$request->id);
        $data = $query->get()->map(function ($item, $index) {
            return [
                'No' => $index + 1, // 👈 Add serial number
                'Date' => Carbon::parse($item->created_at)->format('d-m-Y'),
                'Name' => $item->user->name ?? '-',
                'Mobile Number' => $item->user->phone_number ?? '-',
                'Email ID' => $item->user->email ?? '-',
            ];
        });

        return Excel::download(new ExportLeadDetails($data), 'LeadDetails.xlsx');
    }
}
