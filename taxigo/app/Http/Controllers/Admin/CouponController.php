<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index()
    {
        return view('discount-coupon.discount-coupons');
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
            $query = Coupon::query();

            if ($search) {
                $query->where('code', 'like', "%" . $search . "%");
            }
            $recordsTotal = $query->count();
            $list = $query->orderBy($orderByColumn, $orderBy)
                ->skip($skip)
                ->take($pageLength)
                ->get();
            $recordsFiltered = $recordsTotal;

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                'data' => $list,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Getting error of display coupon list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function create()
    {
        return view('discount-coupon.create-discount-coupon');
    }

    public function storeUpdate(StoreUpdateCouponRequest $request)
    {
        try {
            $data = $request->validated();
            $data['status'] = 0;
            $data['no_expiry'] = 0;
            if (isset($request->status) && $request->status == 1) {
                $data['status'] = 1;
            }
            if (isset($request->no_expiry) && $request->no_expiry != null) {
                $data['no_expiry'] = 1;
            }
            $message = 'created';
            if (isset($request->id) && $request->id != null) {
                $FleetOperator = Coupon::findOrFail($request->id);
                $FleetOperator->fill($data);
                $FleetOperator->save();
                $message = 'updated';
            } else {
                Coupon::create($data);
            }
            return redirect()->route('admin.coupons.index')->with('success', 'Discount coupon ' . $message . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Getting error of store coupon :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function edit($id)
    {
        try {
            $coupon = Coupon::find($id);
            return view('discount-coupon.create-discount-coupon', compact('coupon'));
        } catch (\Throwable $th) {
            Log::error('Getting error of edit coupon :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function delete($id)
    {
        try {
            $coupon = Coupon::find($id);
            if (!empty($coupon)) {
                $coupon->delete();
            }
            return redirect()->back()->with('success', 'Discount coupon deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of delete coupon :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    public function change($id)
    {
        try {
            $coupon = Coupon::find($id);
            if (!empty($coupon)) {
                $change = ($coupon->show_app == 1) ? 0 : 1;
                $coupon->show_app = $change;
                $coupon->save();
            }
            return redirect()->back()->with('success', 'Changed successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of change show app coupon :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }
}
