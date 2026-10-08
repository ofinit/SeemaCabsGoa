<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingEnum;
use App\Enums\Type;
use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\BookingDetails;
use App\Models\Cab;
use App\Models\CabRate;
use App\Models\City;
use App\Models\Driver;
use App\Models\Environment;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Services\NotificationService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $title = null;
        if (empty($status)) {
            $title = 'Booking Details';
        } else if ($status == 'completed') {
            $title = 'Completed Trips';
        } else if ($status == 'cancel') {
            $title = 'Cancelled Trips';
        } else if ($status == 'refund') {
            $title = 'Cancel & Refund';
        }

        return view('trip-management.booking-details', compact('title'));
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
            $status = $request->status ?? '';

            $query = BookingDetail::with(['getPickupFrom', 'getDropTo', 'cabRate', 'assignDriver.getCabDetails'])->where('payment_status', Type::ACTIVE);
            if (!empty($status)) {
                if ($status == 'completed') {
                    $query = $query->where('status', 4);
                    $query = $query->whereNull('refund');
                } else if ($status == 'cancel') {
                    $query = $query->where('status', 3);
                } else if ($status == 'refund') {
                    $query = $query->where('status', 4)->where('refund', '!=', null);
                } else {
                    $query = $query->where('status', 1);
                }
            } else {
                $query = $query->where('status', 1);
            }
            if ($search) {
                $query->where('booking_id', 'like', "%" . $search . "%")
                    ->orWhere('trip_type', 'like', "%" . $search . "%")
                    ->orWhere('part_payment', 'like', "%" . $search . "%")
                    ->orWhere('cash_with_driver', 'like', "%" . $search . "%")
                    ->orWhere('trip_type', 'like', "%" . $search . "%")
                    ->orWhereRaw("DATE_FORMAT(booking_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("DATE_FORMAT(pickup_date, '%d-%m-%Y') LIKE ?", ['%' . $search . '%'])
                    ->orWhereHas('getPickupFrom', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhereHas('getDropTo', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%");
                    })
                    ->orWhere('pickup_address', 'like', "%" . $search . "%")
                    ->orWhere('drop_of_address', 'like', "%" . $search . "%")
                    ->orWhereRaw("('Hatchback' LIKE CONCAT('%', ?, '%') AND cab_type = 1) OR ('Sedan' LIKE CONCAT('%', ?, '%') AND cab_type = 2) OR ('SUV' LIKE CONCAT('%', ?, '%') AND cab_type = 3)", [$search, $search, $search])
                    ->orWhereHas('user', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%")
                            ->orWhereRaw("('male' LIKE CONCAT('%', ?, '%') AND gender = 1) OR ('female' LIKE CONCAT('%', ?, '%') AND gender = 0) OR ('other' LIKE CONCAT('%', ?, '%') AND gender = 2)", [$search, $search, $search])
                            ->orWhere('phone_number', 'like', "%" . $search . "%")
                            ->orWhereHas('getStateDetails', function ($query) use ($search) {
                                $query->where('name', 'like', "%" . $search . "%");
                            })->orWhereHas('getCountryDetails', function ($query) use ($search) {
                                $query->where('name', 'like', "%" . $search . "%");
                            });
                    })
                    ->orWhereHas('assignDriver', function ($data) use ($search) {
                        $data->where('name', 'like', "%" . $search . "%")
                            ->orWhere('mobile', 'like', "%" . $search . "%")
                            ->orWhereHas('getCabDetails', function ($query) use ($search) {
                                $query->whereRaw("('Baleno, Swift or similar' LIKE CONCAT('%', ?, '%') AND model_id = 1) OR ('Dzire, Etios or similar' LIKE CONCAT('%', ?, '%') AND model_id = 2) OR ('Xylo, Ertiga or similar' LIKE CONCAT('%', ?, '%') AND model_id = 3)", [$search, $search, $search]);
                            })
                            ->orWhereHas('getCabDetails.getColorDetails', function ($query) use ($search) {
                                $query->where('name', 'like', "%" . $search . "%");
                            })
                            ->orWhereHas('getCabDetails', function ($query) use ($search) {
                                $query->where('number', 'like', "%" . $search . "%");
                            });
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
            Log::error('Getting error of display booking details list :' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong,Please try again latter.');
        }
    }

    // public function getDriverList()
    // {
    //     try {
    //         $driverList = Driver::with('getCabDetails')->get();
    //         return response()->json(['status' => true, 'Message' => 'List get successfull.', 'data' => $driverList]);
    //     } catch (\Throwable $th) {
    //         Log::error('Getting error of get driver list :' . $th->getMessage());
    //         return response()->json(['status' => false, 'Message' => 'Somthing went wrong, Please try again latter.']);
    //     }
    // }

    public function assignDriver(Request $request)
    {
        try {
             Log::info('Requested Driver drivers : ' . $request->driverId);
            $booking = BookingDetail::find($request->booking_id);
            $booking->assigned_driver_id = $request->driverId;
            $booking->status = 1;
            Log::info('Booking drivers : ' . $booking->assigned_driver_id);
            $booking->save();

            $driverDetail = isset($booking->assignDriver) ? $booking->assignDriver : null;
            $driverName = isset($driverDetail) && $driverDetail != null ? $driverDetail->driver_name : '--';
            $cabColor = isset($booking->assignDriver->getCabDetails->getColorDetails->name) ? $booking->assignDriver->getCabDetails->getColorDetails->name : '--';
            $cabNumber = isset($booking->assignDriver->getCabDetails->number) ? $booking->assignDriver->getCabDetails->number : '--';
            $trip_otp = isset($booking->trip_otp) ? $booking->trip_otp : '--';
            $bookingId = isset($booking->booking_id) ? $booking->booking_id : '--';
            $model = '--';
            if (isset($booking->assignDriver->getCabDetails->model)) {
                switch ($booking->assignDriver->getCabDetails->model) {
                    case 1:
                        $model = 'Baleno, Swift or similar';
                        break;
                    case 3:
                        $model = 'Dzire, Etios or similar';
                        break;
                    case 4:
                        $model = 'Xylo, Ertiga or similar';
                        break;
                    default:
                        $model = '--';
                        break;
                }
            }

            $userFcmToken = UserFcmToken::where('user_id', $booking->customer_id)->first();
            $notification = new NotificationService();
            $pickupDateRaw = Carbon::parse($bookingDetail->pickup_date ?? null)->format('d M, Y');
            $pickupTimeRaw = Carbon::parse($bookingDetail->pickup_time ?? null)->format('H:i A');

            $title = "Your Ride is Confirmed!";
            $body = "Your ride $bookingId on $pickupDateRaw at $pickupTimeRaw is assigned to $driverName.\n\nCab: $cabColor  $model - $cabNumber. OTP: $trip_otp";
            // $body = "Driver: " . $driverName . "Cab: " . $cabColor . " " . $model . " ($cabNumber)" . "OTP: " . $trip_otp . "(Share only with the driver to start the trip).";

            $data = ['booking_id' => $bookingId];

            $notification->storeUserNotification($booking->customer_id, $title, $body, $data, "");
            if (!empty($userFcmToken)) {
                $notification->sendUserNotification($userFcmToken->token, $title, $body, $data, $booking->customer_id);
            }

            return redirect()->back()->with('success', 'Driver assigned successfully.');
        } catch (\Throwable $th) {
            Log::error('Getting error of store assign drivers :' . $th->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    public function getBookingDetails(Request $request)
    {
        try {
            $tripDetails = BookingDetail::find($request->id);
            $html = view('trip-management.bokking-details-model', compact('tripDetails'))->render();
            return response()->json(['status' => true, 'Message' => 'Data get successfull.', 'html' => $html]);
        } catch (\Throwable $th) {
            Log::error('Getting error of store assign drivers :' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Somthing went wrong, Please try again latter.']);
        }
    }
    public function getDriverList(Request $request)
    {
        try {
            $driverList = [];
            if ($request->cab_type) {
                $driverList = Driver::with('getCabDetails')->whereHas('getCabDetails', function ($query) use ($request) {
                    $query->where('type', $request->cab_type);
                })->get();
            }
            $bookingId = $request->get('booking_id');
            $html = view('trip-management.assign-driver-model', compact('driverList', 'bookingId'))->render();
            // return response()->json(['status' => true, 'Message' => 'Data get successfull.', 'data' => $driverList, 'bookingId' => $bookingId]);
            return response()->json(['status' => true, 'Message' => 'Data get successFull.', 'html' => $html]);
        } catch (\Throwable $th) {
            Log::error('Getting error of store assign drivers :' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Somthing went wrong, Please try again latter.']);
        }
    }

    public function changeDriver(Request $request)
    {
        try {
            $driverList = [];
            if ($request->cab_type) {
                $driverList = Driver::with('getCabDetails')->whereHas('getCabDetails', function ($query) use ($request) {
                    // $query->where('type', $request->cab_type);
                })->get();
            }
            $bookingId = $request->get('booking_id');
            $bookingDetails = BookingDetail::find($bookingId);
            $html = view('trip-management.change-driver-model', compact('driverList', 'bookingId', 'bookingDetails'))->render();
            // return response()->json(['status' => true, 'Message' => 'Data get successfull.', 'data' => $driverList, 'bookingId' => $bookingId]);
            return response()->json(['status' => true, 'Message' => 'Data get successFull.', 'html' => $html]);
        } catch (\Throwable $th) {
            Log::error('Getting error of store assign drivers :' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Somthing went wrong, Please try again latter.']);
        }
    }

    public function cancelBooking($id, $type)
    {
        try {
            $bookingDetail = BookingDetail::find($id);
            $message = 'canceled';
            if ($type == 'cancel') {
                $bookingDetail->status = BookingEnum::CANCEL;
            } else {
                $bookingDetail->status = BookingEnum::COMPLETE;
                $message = 'completed';
            }
            $bookingDetail->save();
            if ($bookingDetail->status == BookingEnum::CANCEL) {
                $bookingId = $bookingDetail->booking_id;
                $pickupDateRaw = isset($bookingDetail->pickup_date) ? Carbon::parse($bookingDetail->pickup_date)->format('d M, Y') : 'N/A';
                $pickupTimeRaw = Carbon::parse($bookingDetail->pickup_time ?? null)->format('H:i A');
                $pickFrom = '';
                $drop = '';
                if ($bookingDetail->air_port_drop == Type::AIRPORT_PICKUP) {
                    if ($bookingDetail->pickup_from == Type::AIRPORT_ONE_ID) {
                        $pickFrom = Type::AIRPORT_ONE;
                    } elseif ($bookingDetail->pickup_from == Type::AIRPORT_TWO_ID) {
                        $pickFrom = Type::AIRPORT_TWO;
                    } else {
                        $pickFrom = $bookingDetail->getPickupFrom ? $bookingDetail->getPickupFrom->name : 'N/A';
                    }
                } else {
                    $pickFrom = $bookingDetail->getPickupFrom ? $bookingDetail->getPickupFrom->name : 'N/A';
                }

                if ($bookingDetail->air_port_drop == Type::AIRPORT_DROP) {
                    if ($bookingDetail->drop_to == Type::AIRPORT_ONE_ID) {
                        $drop = Type::AIRPORT_ONE;
                    } elseif ($bookingDetail->drop_to == Type::AIRPORT_TWO_ID) {
                        $drop = Type::AIRPORT_TWO;
                    } else {
                        $drop = $bookingDetail->getDropTo ? $bookingDetail->getDropTo->name : '--';
                    }
                } else {
                    $drop = $bookingDetail->getDropTo ? $bookingDetail->getDropTo->name : '--';
                }

                //Send Notification to Customer
                $userFcmToken = UserFcmToken::where('user_id', $bookingDetail->customer_id)->first();
                $notification = new NotificationService();

                    $title = "Ride Cancelled";
                     $body = "Booking $bookingId for $pickupDateRaw, $pickupTimeRaw from $pickFrom to $drop has been cancelled by Seema Cabs.";
                    // $body = "We're sorry your cab from " . $pickFrom . " to " . $drop . " at " . $pickupTimeRaw . " isn't available due to high demand.";
                    $data = ['booking_id' => $bookingId];
                $notification->storeUserNotification($bookingDetail->customer_id, $title, $body, $data, "");
                if (!empty($userFcmToken)) {

                    $notification->sendUserNotification($userFcmToken->token, $title, $body, $data, $bookingDetail->customer_id);
                }
            }
            return response()->json(['status' => true, 'Message' => 'Booking ' . $message . ' successfully.']);
        } catch (\Throwable $th) {
            Log::error('Getting error of cancel booking :' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Something went wrong, Please try again latter.']);
        }
    }

    public function bookingDetail(Request $request)
    {
        try {
            $bookingDetail = BookingDetail::with(['assignDriver.getCabDetails'])->where('id', $request->id)->first();
            if (!$bookingDetail) {
                return response()->json(
                    [
                        'status' => false,
                        'Message' => 'Booking Detail Not Found'
                    ],
                    404
                );
            }
            $drop_to = null;
            $cab_type = null;

            $pickup = '';

            if ($bookingDetail->air_port_drop == Type::AIRPORT_PICKUP) {
                if ($bookingDetail->pickup_from == Type::AIRPORT_ONE_ID) {
                    $pickup = Type::AIRPORT_ONE;
                } else if ($bookingDetail->pickup_from == Type::AIRPORT_TWO_ID) {
                    $pickup = Type::AIRPORT_TWO;
                } else {
                    $pickup = $bookingDetail->getPickupFrom ? $bookingDetail->getPickupFrom->name : '--';
                }
            } else {
                $pickup = $bookingDetail->getPickupFrom ? $bookingDetail->getPickupFrom->name : '--';
            }


            if ($bookingDetail->air_port_drop == Type::AIRPORT_DROP) {
                if ($bookingDetail->drop_to == Type::AIRPORT_ONE_ID) {
                    $drop_to = Type::AIRPORT_ONE;
                } else if ($bookingDetail->drop_to == Type::AIRPORT_TWO_ID) {
                    $drop_to = Type::AIRPORT_TWO;
                } else {
                    $drop_to = $bookingDetail->getDropTo ? $bookingDetail->getDropTo->name : '--';
                }
            } else {
                $drop_to = $bookingDetail->getDropTo ? $bookingDetail->getDropTo->name : '--';
            }



            // if ($bookingDetail->drop_to == Type::AIRPORT_ONE_ID) {
            //     $drop_to = Type::AIRPORT_ONE;
            // } elseif ($bookingDetail->drop_to == Type::AIRPORT_TWO_ID) {
            //     $drop_to = Type::AIRPORT_TWO;
            // } else {
            //     $drop_to = City::where('id', $bookingDetail->drop_to)->first()->name;
            // }

            if ($bookingDetail->cab_type == Type::CAB_HATCHBACK_ID) {
                $cab_type = Type::CAB_HATCHBACK;
            } elseif ($bookingDetail->cab_type == Type::CAB_SEDAN_ID) {
                $cab_type = Type::CAB_SEDAN;
            } elseif ($bookingDetail->cab_type == Type::CAB_SUV_ID) {
                $cab_type = Type::CAB_SUV;
            }
            $bookingDetail->booking_date = Carbon::parse($bookingDetail->booking_date)->format('d-m-Y');
            $bookingDetail->trip_type = $bookingDetail->trip_type;
            $bookingDetail->pickup_address = $bookingDetail->pickup_address;
            $bookingDetail->pickup_date = Carbon::parse($bookingDetail->pickup_date)->format('d-m-Y');
            $bookingDetail->pickup_time = Carbon::parse($bookingDetail->pickup_time)->format('H:i A');
            $bookingDetail->pickup_from = $pickup; // isset($bookingDetail->pickup_from) ? City::where('id',$bookingDetail->pickup_from)->first()->name : '';
            $bookingDetail->drop_to = $drop_to;
            $bookingDetail->cab_type = $cab_type;
            $view = view('trip-management.view-booking-detail-modal', compact('bookingDetail'))->render();
            $data = [
                'status' => true,
                'message' => 'Modal Loaded Successfully',
                'view' => $view,
            ];
            return response()->json($data);
        } catch (\Throwable $th) {
            Log::error('Getting error of cancel booking :' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Something went wrong, Please try again latter.'], 500);
        }
    }

    public function updateCallStatus(Request $request)
    {
        try {
            $status = $request->status == 'true' ? true : false;
            $bookingDetail = BookingDetail::where('id', $request->id)->first();
            if (!$bookingDetail) {
                return response()->json(
                    [
                        'status' => false,
                        'Message' => 'Booking Detail Not Found'
                    ],
                    404
                );
            }

            $bookingDetail->update([
                'call_status' => $status,
            ]);

            $data = [
                'status' => true,
                'message' => 'Call status has been updated successfully',
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            Log::error('Getting error of cancel booking :' . $th->getMessage());
            return response()->json(['status' => false, 'Message' => 'Something went wrong, Please try again latter.'], 500);
        }
    }
}
