<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Enums\Type;
use App\Exports\ExportFleetOperatorPayment;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FleetOperatorPaymentController extends Controller
{
    public function index()
    {
        $payment = Payment::count();
        $paymentPaid = Payment::where('status', Type::PAID)->whereHas('bookingDetails', function ($data) {
            $data->where('payment_status', Type::ACTIVE);
        })->count();
        $paymentUnpaid = Payment::where('status', Type::UNPAID)->count();
        $paymentRefund = Payment::where('status', Type::REFUND)->count();
        return view('reports.fleet-operator-payments', compact('payment', 'paymentPaid', 'paymentUnpaid', 'paymentRefund'));
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
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') ?? '';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') ?? '';
            $query = Payment::with(['bookingDetails.assignDriver', 'bookingDetails.getPickupFrom', 'bookingDetails.getDropTo']);
            if ((isset($request->start_date) && $request->start_date != null) && isset($request->end_date) && $request->end_date != null) {
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
            }
            if ($search) {
                $query->whereRaw("DATE_FORMAT(date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereHas('bookingDetails', function ($q) use ($search) {
                        $q->where('booking_id', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getCabDetails', function ($q) use ($search) {
                        $q->where('number', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getDropTo', function ($q) use ($search) {
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
            Log::error('Getting error of display fleet operator payment list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
    public function listPaid(Request $request)
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
            $query = Payment::where('status', Type::PAID)->with('bookingDetails.assignDriver');
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') ?? '';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') ?? '';
            if ((isset($request->start_date) && $request->start_date != null) && isset($request->end_date) && $request->end_date != null) {
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
            }
            if ($search) {
                $query->whereRaw("DATE_FORMAT(date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereHas('bookingDetails', function ($q) use ($search) {
                        $q->where('booking_id', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getCabDetails', function ($q) use ($search) {
                        $q->where('number', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getDropTo', function ($q) use ($search) {
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
            Log::error('Getting error of display fleet operator payment list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
    public function listUnpaid(Request $request)
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
            $query = Payment::where('status', Type::UNPAID)->with('bookingDetails.assignDriver');
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') ?? '';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') ?? '';
            if ((isset($request->start_date) && $request->start_date != null) && isset($request->end_date) && $request->end_date != null) {
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
            }
            if ($search) {
                $query->whereRaw("DATE_FORMAT(date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereHas('bookingDetails', function ($q) use ($search) {
                        $q->where('booking_id', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getCabDetails', function ($q) use ($search) {
                        $q->where('number', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getDropTo', function ($q) use ($search) {
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
            Log::error('Getting error of display fleet operator payment list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
    public function listRefund(Request $request)
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
            $query = Payment::where('status', Type::REFUND)->with('bookingDetails.assignDriver');
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') ?? '';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') ?? '';
            if ((isset($request->start_date) && $request->start_date != null) && isset($request->end_date) && $request->end_date != null) {
                $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
            }
            if ($search) {
                $query->whereRaw("DATE_FORMAT(date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereHas('bookingDetails', function ($q) use ($search) {
                        $q->where('booking_id', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getCabDetails', function ($q) use ($search) {
                        $q->where('number', 'Like', '%' . $search . '%');
                    })
                    ->orWhereHas('bookingDetails.getDropTo', function ($q) use ($search) {
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
            Log::error('Getting error of display fleet operator payment list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function export()
    {
        return Excel::download(new ExportFleetOperatorPayment, 'payment.xlsx');
    }
}
