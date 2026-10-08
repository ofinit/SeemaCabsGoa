<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RenewAdvertisementRequest;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpiredController extends Controller
{
    public function index()
    {
        return view('advertisements.expired-ads');
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
            $query = Advertisement::where('end_date', '<', $currentDate)->with('user', 'country', 'state', 'location')->withCount('advertisementUserClicks');
            if ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('users.name', 'like', "%" . $search . "%");
                })
                    // ->where('billing_company_name', 'like', "%" . $search . "%")
                    ->orWhere('banner_url', 'like', "%" . $search . "%")
                    ->OrWhereRaw("DATE_FORMAT(start_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->OrWhereRaw("DATE_FORMAT(end_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%']);
            }
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get();

            if ($list) {
                foreach ($list as $advertisement) {
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
            Log::error('Getting error of display expired advertisement list :' . $e->getMessage());
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
}
