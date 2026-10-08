<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportDeletedUserList;
use App\Exports\ExportUserList;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CustomerManagementController extends Controller
{
    public function index()
    {
        return view('customer-management.view-customers');
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
            $query = User::where('type', '3');

            if ($search) {
                $searchLower = strtolower($search);
                $query->where('name', 'like', "%" . $search . "%")
                    ->orWhereRaw("('male' LIKE CONCAT('%', ?, '%') AND gender = 1) OR ('female' LIKE CONCAT('%', ?, '%') AND gender = 0) OR ('other' LIKE CONCAT('%', ?, '%') AND gender = 2)", [$searchLower, $searchLower, $searchLower])
                    ->orWhereHas('getCountryDetails', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhereHas('getStateDetails', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhere('phone_number', 'like', "%" . $search . "%")
                    ->orWhere('email', 'like', "%" . $search . "%")
                    ->orWhereRaw("('active' LIKE CONCAT('%', ?, '%') AND status != 0) OR ('suspend' LIKE CONCAT('%', ?, '%') AND status = 0)", [$searchLower, $searchLower])
                    ->orWhere('device', 'like', "%" . $search . "%");
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
            Log::error('Getting error of display customers list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function changeStatus($id)
    {
        try {
            $user = User::find($id);
            if (!empty($user)) {
                $status = ($user->status == 1) ? 0 : 1;
                $user->status = $status;
                $user->save();
            }
            return redirect()->back()->with('success', 'Status changed successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of change user status :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function deleteUserDetails()
    {
        return view('customer-management.deleted-customers');
    }

    public function deleteUserDetailsList(Request $request)
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
            $query = User::onlyTrashed()->where('type', '3');

            if ($search) {
                $searchLower = strtolower($search);
                $query->where(function ($q) use ($search, $searchLower) {
                    $q->where('name', 'like', "%" . $search . "%")
                        ->orWhereRaw("('male' LIKE CONCAT('%', ?, '%') AND gender = 1) OR ('female' LIKE CONCAT('%', ?, '%') AND gender = 0) OR ('other' LIKE CONCAT('%', ?, '%') AND gender = 2)", [$searchLower, $searchLower, $searchLower])
                        ->orWhereHas('getCountryDetails', function ($data) use ($search) {
                            $data->where('name', 'like', "%" . $search . "%");
                        })
                        ->orWhereHas('getStateDetails', function ($data) use ($search) {
                            $data->where('name', 'like', "%" . $search . "%");
                        })
                        ->orWhere('phone_number', 'like', "%" . $search . "%")
                        ->orWhere('delete_reason', 'like', "%" . $search . "%")
                        ->orWhereRaw("DATE_FORMAT(deleted_at, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                        ->orWhere('email', 'like', "%" . $search . "%");
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
            Log::error('Getting error of display delete customers list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }


    public function exportUserList()
    {
        return Excel::download(new ExportUserList, 'UserList.xlsx');
    }

    public function exportDeletedUserList()
    {
        return Excel::download(new ExportDeletedUserList, 'DeletedUserList.xlsx');
    }
}
