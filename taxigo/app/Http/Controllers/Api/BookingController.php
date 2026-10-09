<?php

namespace App\Http\Controllers\Api;

use App\Enums\BookingEnum;
use App\Http\Requests\Api\SightSeeingBookingRegisterRequest;
use DateTime;
use Carbon\Carbon;
use App\Enums\Type;
use App\Models\City;
use App\Models\User;
use Razorpay\Api\Api;
use App\Models\Payment;
use App\Models\AdminToken;
use App\Models\Environment;
use App\Mail\Api\OrderMail;
use Illuminate\Http\Request;
use App\Models\FleetOperator;
use App\Models\BookingDetail;
use App\Enums\NotificationEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Models\UserFcmToken;
use App\Models\CabPriceType;
use App\Models\Invoice;
use App\Models\SightSeeingPackageCabPrice;
use App\Services\Invoicing\InvoiceService;
use App\Services\Payments\PaymentVerifier;
use App\Services\Pricing\FareBreakdown;
use App\Services\Pricing\FareCalculator;
use App\Support\Gstin;
use Illuminate\Support\Facades\Auth;

class BookingController extends ResponseController
{
    public function store(StoreBookingRequest $request)
    {
        try {
            Log::info($request->all());
            $advanceBooking = (int) Environment::where('title', Type::MIN_BOOKING_TIME)->first()?->value ?? 0;
            $maxAdvanceBooking = (int) Environment::where('title', Type::BOOKING_CANCELLATION)->first()?->value ?? 0;
            $advanceBooking = max($advanceBooking, 0);

            try {
                $pickupDateFormatted = Carbon::createFromFormat('d-m-Y', $request->pickup_date, 'Asia/Kolkata')->format('Y-m-d');
            } catch (\Exception $e) {
                return $this->error("Invalid date format for pickup date. Please use dd-mm-yyyy format.");
            }

            try {
                $pickupTimeFormatted = Carbon::createFromFormat('H:i', $request->pickup_time, 'Asia/Kolkata')->format('H:i');
            } catch (\Exception $e) {
                return $this->error("Invalid time format for pickup time. Please use HH:mm format.");
            }

            $pickupDateTime = Carbon::parse($pickupDateFormatted . ' ' . $pickupTimeFormatted, 'Asia/Kolkata');
            $now = now('Asia/Kolkata');
            $hoursDiff = $now->diffInHours($pickupDateTime);

            if($advanceBooking > 0){
                if ($hoursDiff < $advanceBooking) {
                    return $this->error("Minimum booking time is {$advanceBooking} hour(s) before pickup time.");
                }
                if ($hoursDiff > $maxAdvanceBooking) {
                    return $this->error("Maximum booking time is {$maxAdvanceBooking} hour(s) before pickup time.");
                }
            }

            $user = auth()->user();
            if ($user != null && $user->status == 0) {
                return response()->json(['status' => false, 'user_status' => false, 'message' => 'Account Suspended. Contact Support.']);
            }

            // Price is always recalculated here from the selected cab rate;
            // amounts sent by the client are only compared, never stored.
            $cabPrice = CabPriceType::with('cabRate')
                ->where('cab_rate_id', $request->cab_id)
                ->where('cab_type', $request->cab_type)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->first();
            if (!$cabPrice || !$cabPrice->cabRate) {
                return $this->error('The selected cab is no longer available. Please search again.');
            }

            $fare = FareCalculator::fromDatabase()->ride(
                (float) $cabPrice->base_fare,
                (int) $cabPrice->cabRate->tab,
                $pickupDateTime,
                now(),
            );
            if ($this->clientAmountsDiffer($request, $fare->total, $fare->advance)) {
                return $this->error('Prices have been updated. Please go back and select your cab again.');
            }

            $gstDetails = $this->businessGstDetails($request);
            if (is_string($gstDetails)) {
                return $this->error($gstDetails);
            }

            DB::beginTransaction();
            $data = $request->validated();
            $bookingId = $this->createNewBookingId();
            $randomOtp = mt_rand(1000, 9999);
            $data['customer_id'] = $user->id;
            $data['booking_date'] = date('Y-m-d');
            $data['booking_id'] = $bookingId;
            $data['cab_rate_id'] = $data['cab_id'];
            $data['status'] = Type::UNPAID;
            $data['payment_status'] = Type::INACTIVE;
            $data['pickup_date'] = Carbon::parse($data['pickup_date'])->format('Y-m-d');
            $data['trip_otp'] = $randomOtp;
            $data = array_merge($data, $fare->bookingAttributes(), $gstDetails);
            Schema::disableForeignKeyConstraints();
            $bookingDetails = BookingDetail::create($data);
            Schema::enableForeignKeyConstraints();
            if ($bookingDetails) {
                Payment::create([
                    'booking_id' => $bookingDetails->id,
                    'customer_id' => $user->id,
                    'date' => date('Y-m-d'),
                    'amount' => FareBreakdown::rupees($fare->advance),
                    'amount_settlement' => json_encode($fare->settlement()),
                    'status' => Type::UNPAID,
                    'transfer_amount_status' => Type::ACTIVE,
                ]);
            }



            // $transferAmount = $this->transferAmount($request->transaction_id, $request->fleet_operator_payment);
            // if (!$transferAmount) {
            //     $paymentData = Payment::where('booking_id', $bookingDetails->id)->first();
            //     $paymentData->transfer_amount_status = Type::INACTIVE;
            //     $paymentData->save();
            // }

            $this->updateCustomerProfile($user->id, $request, $gstDetails);

            DB::commit();


            $bookingData['booking_id'] = $bookingId;
            $bookingData['otp'] = $randomOtp;

            // Send notification
            // try {
            //     $adminTokens = AdminToken::pluck('token')->toArray();
            //     $notification = new NotificationService();
            //     $pickupFrom = City::find($request->pickup_from);
            //     $dropTo = City::find($request->drop_to);

            //     $pickupDate = $request->pickup_date ?? '';
            //     $pickupTimeRaw = $request->pickup_time ?? '';

            //     $pickupTimeFormatted = '';
            //     if ($pickupTimeRaw) {
            //         $dateObj = DateTime::createFromFormat('H:i', $pickupTimeRaw);
            //         if ($dateObj) {
            //             $pickupTimeFormatted = $dateObj->format('h:i A');
            //         } else {
            //             $pickupTimeFormatted = $pickupTimeRaw;
            //         }
            //     }

            //     $title = "New Booking";

            //     $body = "From: " . ($pickupFrom ? $pickupFrom->name : '') . " To: " . ($dropTo ? $dropTo->name : '') . "\n" .
            //         "Pickup Date: " . $pickupDate . " Pickup Time: " . $pickupTimeFormatted . "\n" .
            //         "Customer Name: " . ($user->name ?? '') . " Booking Id: " . ($bookingId ?? '');

            //     $data = ['booking_id' => $bookingId];

            //     $notification->sendNotification($adminTokens, $title, $body, $data, $request->user()->id);
            // } catch (\Throwable $th) {
            //     Log::info('Getting issue on send admin notification :' . $th->getMessage());
            // }

            return $this->success($bookingData, 'Booking successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Getting error of store booking details :' . $th->getMessage());
            Log::error('Getting error of store booking details :' . $th->getLine());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function updatePaymentStatus(Request $request)
    {
        try {
            $bookingId = $request->booking_id;
            $status = (bool) $request->status;
            $user = auth()->user();

            $bookingDetails = BookingDetail::where('booking_id', $bookingId)->first();
            if (!$bookingDetails || ($user && $bookingDetails->customer_id != $user->id)) {
                return $this->error('Booking not found.');
            }
            $paymentDetails = Payment::where('booking_id', $bookingDetails->id)->first();
            if (!$paymentDetails) {
                return $this->error('Payment record not found for this booking.');
            }

            $remainingAmount = round((float) $bookingDetails->total_payment - (float) $bookingDetails->part_payment, 2);
            $data = ['booking_id' => $bookingId, 'otp' => $bookingDetails->trip_otp, 'remaining_amount' => $remainingAmount];

            // Already confirmed (e.g. by the gateway webhook or a retried call): don't repeat side effects.
            if ($bookingDetails->payment_status == Type::ACTIVE) {
                if ($bookingDetails->status == Type::UNPAID) {
                    $bookingDetails->status = Type::PAID;
                    $bookingDetails->save();
                }
                return $status ? $this->success($data, 'Booking successfully') : $this->error('Booking is already paid.');
            }

            if (!$status) {
                return $this->error('Booking failed');
            }

            $isCashfree = ($request->payment_gateway === 'cashfree')
                || str_starts_with($request->transaction_id ?? '', 'cf_')
                || str_starts_with($request->pg_order_id ?? '', 'CF_');

            // Never trust the client's word that it paid: confirm with the gateway.
            $verification = app(PaymentVerifier::class)->verify(
                $bookingDetails,
                $isCashfree ? 'cashfree' : 'razorpay',
                $request->transaction_id,
                $request->pg_order_id,
                (float) $paymentDetails->amount,
            );
            if (!$verification['ok']) {
                Log::warning("Payment confirmation rejected for {$bookingId}: {$verification['message']}");
                return $this->error($verification['message']);
            }

            DB::beginTransaction();
            $bookingDetails->payment_status = Type::ACTIVE;
            $bookingDetails->status = Type::PAID;
            $bookingDetails->save();

            $paymentDetails->status = Type::PAID;
            $paymentDetails->transaction_id = $request->transaction_id;
            $paymentDetails->payment_gateway = $isCashfree ? 'cashfree' : 'razorpay';
            if ($request->filled('pg_order_id')) {
                $paymentDetails->pg_order_id = $request->pg_order_id;
            }

            // Transfer amount / split handling
            $amountSettlement = json_decode($paymentDetails->amount_settlement);

            if ($isCashfree) {
                // In Cashfree, vendor split is handled via Easy Split order_splits at order creation
                $paymentDetails->transfer_amount_status = Type::ACTIVE;
            } elseif (!empty($amountSettlement->company_commission)) {
                $transferAmount = $this->transferAmount($request->transaction_id, $amountSettlement->company_commission);
                if (!$transferAmount) {
                    $paymentDetails->transfer_amount_status = Type::INACTIVE;
                } else {
                    $paymentDetails->transfer_amount_status = Type::ACTIVE;
                }
            }

            $paymentDetails->save();

            DB::commit();

            try {
                app(InvoiceService::class)->receiptVoucher($bookingDetails->fresh(), $paymentDetails);
            } catch (\Throwable $th) {
                Log::error("Receipt voucher failed for {$bookingId}: " . $th->getMessage());
            }

            if ($status) {
                $user = $user ?? User::find($bookingDetails->customer_id);
                try {
                    $title = 'Your Booking is Confirmed by Seema Cabs Goa!';
                    Mail::to($user->email)->send(new OrderMail($bookingDetails, $title));
                } catch (\Throwable $th) {
                    Log::error('Getting error of send mail for place order :' . $th->getMessage());
                }

                // Send notification
                try {
                    $adminTokens = AdminToken::pluck('token')->toArray();
                    $notification = new NotificationService();
                    $pickupDate = $bookingDetails->pickup_date ?? '';
                    $pickupTimeRaw = Carbon::parse($bookingDetails->pickup_time ?? null)->format('H:i A');
                    $pickFrom = '';
                    $drop = '';
                    if ($bookingDetails->air_port_drop == Type::AIRPORT_PICKUP) {
                        if ($bookingDetails->pickup_from == Type::AIRPORT_ONE_ID) {
                            $pickFrom = Type::AIRPORT_ONE;
                        } elseif ($bookingDetails->pickup_from == Type::AIRPORT_TWO_ID) {
                            $pickFrom = Type::AIRPORT_TWO;
                        } else {
                            $pickFrom = $bookingDetails->getPickupFrom ? $bookingDetails->getPickupFrom->name : 'N/A';
                        }
                    } else {
                        $pickFrom = $bookingDetails->getPickupFrom ? $bookingDetails->getPickupFrom->name : 'N/A';
                    }

                    if ($bookingDetails->air_port_drop == Type::AIRPORT_DROP) {
                        if ($bookingDetails->drop_to == Type::AIRPORT_ONE_ID) {
                            $drop = Type::AIRPORT_ONE;
                        } elseif ($bookingDetails->drop_to == Type::AIRPORT_TWO_ID) {
                            $drop = Type::AIRPORT_TWO;
                        } else {
                            $drop = $bookingDetails->getDropTo ? $bookingDetails->getDropTo->name : '--';
                        }
                    } else {
                        $drop = $bookingDetails->getDropTo ? $bookingDetails->getDropTo->name : '--';
                    }
                    $title = "New Booking";

                    $body = "From: " . $pickFrom . " To: " . $drop . "\n" .
                        "Pickup Date: " . $pickupDate . " Pickup Time: " . $pickupTimeRaw . "\n" .
                        "Customer Name: " . ($user->name ?? '') . " Booking Id: " . ($bookingId ?? '');

                        $remainingAmount = $bookingDetails->total_payment - $bookingDetails->part_payment;

                    $data = ['booking_id' => $bookingId, 'otp' => $bookingDetails->trip_otp, 'remaining_amount' => $remainingAmount];
                    // $notification->sendNotification($adminTokens, $title, $body, $data, NotificationEnum::FLEET_OPERATOR_ID);
                    $notification->sendNotification($adminTokens, $title, $body, $data, NotificationEnum::ADMIN_ID);
                     $notification->sendVendorNotification($pickupDate );
                } catch (\Throwable $th) {
                    Log::info('Getting issue on send admin notification :' . $th->getMessage());
                }

                return $this->success($data, 'Booking successfully');
            } else {
                return $this->error('Booking failed');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Getting error of update payment status :' . $th->getMessage());
            return $this->error('Something went wrong, Please try again latter.');
        }
    }

    public function createNewBookingId()
    {
        $lastBooking = BookingDetail::latest('id')->first();

        if ($lastBooking && !empty($lastBooking->booking_id)) {
            $lastBookingId = $lastBooking->booking_id;
            $lastNumber = (int) substr($lastBookingId, 6);
            $newBookingId = 'SC-GOA' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newBookingId = 'SC-GOA0001';
        }

        return $newBookingId;
    }



    public function transferAmount($paymentId, $amount)
    {
        try {
            $fleetOperatorAccountDetails = FleetOperator::orderBy('id', 'ASC')->first()->razorpay_account;
            Log::info("Fleet operator details :" . $fleetOperatorAccountDetails);
            Log::info("Fleet operator details amount :" . $amount);
            $paymentKey = Environment::where('title', Type::PAYMENT_KEY)->first()->value;
            $paymentSecret = Environment::where('title', Type::PAYMENT_SECRETE)->first()->value;

            $payment = new Api(
                $paymentKey,
                $paymentSecret
            );

            // capcher payment
            $paymentCapcher = $payment->payment->fetch($paymentId);
            $paymentCapcher->capture(['amount' => $paymentCapcher->amount]);
            // Log::info('Amount Details :'.$paymentCapcher);

            // $transfer = $payment->payment->fetch($paymentId)->transfer([
            //     [
            //         'account' => $fleetOperatorAccountDetails,
            //         'amount' => $amount,
            //         'currency' => 'INR',
            //         'notes' => ['purpose' => 'Vendor payout'],
            //         'on_hold' => false,
            //     ]
            // ]);
            $transfer = $payment->payment->fetch($paymentId)->transfer([
                'transfers' => [
                    [
                        'account' => $fleetOperatorAccountDetails,
                        'amount' => intval($amount * 100),
                        'currency' => 'INR',
                        'notes' => ['purpose' => 'Vendor payout'],
                        'on_hold' => false,
                    ]
                ]
            ]);


            if ($transfer->status === 'error') {
                Log::info('Transfer amount failed : ' . $transfer->message);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::info('Transfer amount failed : ' . $e->getMessage());
            return false;
        }
    }

    public function cancelRide(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'booking_id' => 'required',
        ]);

        $bookingDetail = BookingDetail::where('booking_id', $data['booking_id'])->first();

        if (!$bookingDetail || !$this->canAccessBooking($user, $bookingDetail)) {
            return $this->error('Booking not found');
        }
        if($bookingDetail->status == BookingEnum::CANCEL){

            return $this->error('Booking already cancelled');
        }

        $bookingDetail->update([
           'status' => BookingEnum::CANCEL,
        ]);

        $bookingDetail->save();


        $cancelBy = $request['cancel_by'] ?? "Admin";
        //Send Notification to particular user
        $userFcmToken = UserFcmToken::where('user_id', $user->id)->first();


            $notification = new NotificationService();


            $bookingId = $bookingDetail->booking_id;
            $pickupTimeRaw = isset($bookingDetail->pickup_time) ?  Carbon::parse($bookingDetail->pickup_time)->format('H:i A') : 'N/A';
            $pickupDateRaw = isset($bookingDetail->pickup_date) ? Carbon::parse($bookingDetail->pickup_date)->format('d M, Y') : 'N/A';
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

            $title = "Ride Cancelled";
            // $body = "You have cancelled the ride from $pickFrom to $drop - $pickupDateRaw | $pickupTimeRaw";
            $body = $cancelBy == "Admin"?"Booking $bookingId for $pickupDateRaw, $pickupTimeRaw from $pickFrom to $drop has been cancelled by Seema Cabs.":"Booking $bookingId for $pickupDateRaw, $pickupTimeRaw from $pickFrom to $drop has been cancelled by You.";
            $data = ['booking_id' => $bookingId];
            $notification->storeUserNotification($bookingDetail->customer_id, $title, $body, $data, "");
            if(!empty($userFcmToken))
            {
                $notification->sendUserNotification($userFcmToken->token, $title, $body, $data, $user->id);
            }
        return $this->success($data,'Ride cancelled successfully');
    }

    public function sightSeeingBooking(SightSeeingBookingRegisterRequest $request)
    {
        try{
            $advanceBooking = (int) Environment::where('title', Type::MIN_BOOKING_TIME)->first()?->value ?? 0;
            $maxAdvanceBooking = (int) Environment::where('title', Type::BOOKING_CANCELLATION)->first()?->value ?? 0;
            $advanceBooking = max($advanceBooking, 0);

            try {
                $pickupDateFormatted = Carbon::createFromFormat('d-m-Y', $request->pickup_date, 'Asia/Kolkata')->format('Y-m-d');
            } catch (\Exception $e) {
                return $this->error("Invalid date format for pickup date. Please use dd-mm-yyyy format.");
            }

            try {
                $pickupTimeFormatted = Carbon::createFromFormat('H:i', $request->pickup_time, 'Asia/Kolkata')->format('H:i');
            } catch (\Exception $e) {
                return $this->error("Invalid time format for pickup time. Please use HH:mm format.");
            }

            $pickupDateTime = Carbon::parse($pickupDateFormatted . ' ' . $pickupTimeFormatted, 'Asia/Kolkata');
            $now = now('Asia/Kolkata');
            $hoursDiff = $now->diffInHours($pickupDateTime);

            if($advanceBooking > 0){
                if ($hoursDiff < $advanceBooking) {
                    return $this->error("Minimum booking time is {$advanceBooking} hour(s) before pickup time.");
                }
                if ($hoursDiff > $maxAdvanceBooking) {
                    return $this->error("Maximum booking time is {$maxAdvanceBooking} hour(s) before pickup time.");
                }
            }

            $user = Auth::user();
            if ($user != null && $user->status == 0) {
                return response()->json(['status' => false, 'user_status' => false, 'message' => 'Account Suspended. Contact Support.']);
            }

            // Package price comes from the package's price table, never from the client.
            $packagePrice = SightSeeingPackageCabPrice::where('sight_seeing_package_id', $request->sight_seeing_package_id)
                ->where('city_id', $request->pickup_from)
                ->first();
            $priceColumn = [
                Type::CAB_HATCHBACK_ID => 'hatchback_price',
                Type::CAB_SEDAN_ID => 'sedan_price',
                Type::CAB_SUV_ID => 'suv_price',
            ][(int) $request->cab_type] ?? null;
            if (!$packagePrice || !$priceColumn || empty($packagePrice->{$priceColumn})) {
                return $this->error('This cab is not available for the selected package and pickup city.');
            }

            $fare = FareCalculator::fromDatabase()->package((float) $packagePrice->{$priceColumn}, now());
            if (abs((float) $request->price - FareBreakdown::rupees($fare->total)) > 1) {
                return $this->error('Prices have been updated. Please go back and select your cab again.');
            }

            $gstDetails = $this->businessGstDetails($request);
            if (is_string($gstDetails)) {
                return $this->error($gstDetails);
            }

            DB::beginTransaction();
            $data = $request->validated();
            $bookingId = $this->createNewBookingId();
            $randomOtp = mt_rand(1000, 9999);
            $data['customer_id'] = $user->id;
            $data['booking_date'] = date('Y-m-d');
            $data['booking_id'] = $bookingId;
            $data['status'] = Type::UNPAID;
            $data['payment_status'] = Type::INACTIVE;
            $data['pickup_date'] = Carbon::parse($data['pickup_date'])->format('Y-m-d');
            $data['trip_otp'] = $randomOtp;
            $data = array_merge($data, $fare->bookingAttributes(), $gstDetails);
            Schema::disableForeignKeyConstraints();
            $bookingDetails = BookingDetail::create($data);
            Schema::enableForeignKeyConstraints();
            if ($bookingDetails) {
                Payment::create([
                    'booking_id' => $bookingDetails->id,
                    'customer_id' => $user->id,
                    'date' => date('Y-m-d'),
                    'amount' => FareBreakdown::rupees($fare->total),
                    'amount_settlement' => json_encode($fare->settlement()),
                    'status' => Type::UNPAID,
                    'transfer_amount_status' => Type::ACTIVE,
                ]);
            }


            $this->updateCustomerProfile($user->id, $request, $gstDetails);

            DB::commit();


            $bookingData['booking_id'] = $bookingId;
            $bookingData['otp'] = $randomOtp;

            return $this->success($bookingData, 'Booking successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Getting error of store booking details : ' . $e->getMessage());
            Log::info('Getting error of store booking details : ' . $e->getLine());
            return $this->error('Something went wrong, Please try again later.');
        }
    }

    public function getMyBooking() {
         $user = Auth::user();

         $bookingDetail = BookingDetail::where('customer_id', $user->id)->whereNull('customer_deleted_at')->orderBy('id', 'desc')->get();

            foreach ($bookingDetail as $booking) {
                // $cabName = 'Hatchback';
                $booking->ac = "AC";
                // if ($booking->type != null) {
                //     $cabName = 'Hatchback';
                //     if ($booking->type == 2) {
                //         $cabName = 'Sedan';
                //     } else if ($booking->type == 3) {
                //        $cabName = 'SUV';
                //     }
                // }
                // if ($cabName === 'Hatchback') {
                //     $booking->image = asset('cabs/hatchback.png');
                //     $booking->model = 'Baleno, Swift or similar';
                //     $booking->baggage = '2 Baggage';
                //     $booking->seat = 4;

                // } elseif ($cabName === 'Sedan') {
                //     $booking->image = asset('cabs/sedan.png');
                //     $booking->model = 'Dzire, Etios or similar';
                //     $booking->baggage = '3 Baggage';
                //     $booking->seat = 4;

                // } elseif ($cabName === 'SUV') {
                //     $booking->image = asset('cabs/suv.png');
                //     $booking->model = 'Xylo, Ertiga or similar';
                //     $booking->baggage = '3 Baggage';
                //     $booking->seat = 6;
                // }


                if ($booking->cab_type == Type::CAB_HATCHBACK_ID) {
                    $cab_type = Type::CAB_HATCHBACK;
                } elseif ($booking->cab_type == Type::CAB_SEDAN_ID) {
                    $cab_type = Type::CAB_SEDAN;
                } elseif ($booking->cab_type == Type::CAB_SUV_ID) {
                    $cab_type = Type::CAB_SUV;
                }
                $booking->cab_type = $cab_type;

                $booking->cab_name = getCabType($booking->cab_type);


                if ($booking->cab_name == 'Hatchback') {
                    $booking->image = asset('cabs/hatchback.png');
                    $booking->model = 'Baleno, Swift or similar';
                    $booking->baggage = '2 Baggage';
                    $booking->seat = 4;
                } elseif ($booking->cab_name == 'Sedan') {
                    $booking->image = asset('cabs/sedan.png');
                    $booking->model = 'Dzire, Etios or similar';
                    $booking->baggage = '3 Baggage';
                    $booking->seat = 4;
                } elseif ($booking->cab_name == 'SUV') {
                    $booking->image = asset('cabs/suv.png');
                    $booking->model = 'Xylo, Ertiga or similar';
                    $booking->baggage = '3 Baggage';
                    $booking->seat = 6;
                }

                $booking->driver  = isset($booking->assignDriver) ? $booking->assignDriver : null;

                if (isset($booking->getCabDetails)) {
                    $booking->getCabDetails->unsetRelation('getAssignedDriverDetails');
                    $booking->getCabDetails->unsetRelation('getDriverDetails');
                }

         }
         $data = ['bookings' => $bookingDetail];

         return $this->success($data, "Booking List Retrieved Successfully");
    }

    public function getMyCurrentBooking() {
         $user = Auth::user();

         $bookingDetail = BookingDetail::where('customer_id', $user->id)->whereNull('customer_deleted_at')->whereNotNull('assigned_driver_id')->where('status', 1)->orderBy('id', 'desc')->get();

            foreach ($bookingDetail as $booking) {
                $cabName = 'Hatchback';
                $booking->ac = "AC";
                // if ($booking->type != null) {
                //     $cabName = 'Hatchback';
                //     if ($booking->type == 2) {
                //         $cabName = 'Sedan';
                //     } else if ($booking->type == 3) {
                //        $cabName = 'SUV';
                //     }
                // }

                if ($booking->cab_type == Type::CAB_HATCHBACK_ID) {
                    $cab_type = Type::CAB_HATCHBACK;
                } elseif ($booking->cab_type == Type::CAB_SEDAN_ID) {
                    $cab_type = Type::CAB_SEDAN;
                } elseif ($booking->cab_type == Type::CAB_SUV_ID) {
                    $cab_type = Type::CAB_SUV;
                }
                $booking->cab_type = $cab_type;

                $booking->cab_name = getCabType($booking->cab_type);
                if ($cabName === 'Hatchback') {
                    $booking->image = asset('cabs/hatchback.png');
                    $booking->model = 'Baleno, Swift or similar';
                    $booking->baggage = '2 Baggage';
                    $booking->seat = 4;

                } elseif ($cabName === 'Sedan') {
                    $booking->image = asset('cabs/sedan.png');
                    $booking->model = 'Dzire, Etios or similar';
                    $booking->baggage = '3 Baggage';
                    $booking->seat = 4;

                } elseif ($cabName === 'SUV') {
                    $booking->image = asset('cabs/suv.png');
                    $booking->model = 'Xylo, Ertiga or similar';
                    $booking->baggage = '3 Baggage';
                    $booking->seat = 6;
                }
                $booking->driver  = isset($booking->assignDriver) ? $booking->assignDriver : null;

                if (isset($booking->getCabDetails)) {
                    $booking->getCabDetails->unsetRelation('getAssignedDriverDetails');
                    $booking->getCabDetails->unsetRelation('getDriverDetails');
                }
         }

         $data = ['bookings' => $bookingDetail];

         return $this->success($data, "Booking List Retrieved Successfully");
    }

    public function getSingleBooking($id) {
         $user = Auth::user();

         $bookingDetail = BookingDetail::where('id', $id)->first();
         if (!$bookingDetail || !$this->canAccessBooking($user, $bookingDetail)) {
             return $this->error('Booking not found');
         }
         $bookingDetail->invoice_documents = $this->invoiceLinks($bookingDetail);
         $cabName = 'Hatchback';
         $bookingDetail->ac = "AC";
                // if ($bookingDetail->type != null) {
                //     $bookingDetail->cab_name = 'Hatchback';
                //     if ($bookingDetail->type == 2) {
                //         $bookingDetail->cab_name = 'Sedan';
                //     } else if ($bookingDetail->type == 3) {
                //         $bookingDetail->cab_name = 'SUV';
                //     }
                // }
                if ($bookingDetail->cab_type == Type::CAB_HATCHBACK_ID) {
                    $cab_type = Type::CAB_HATCHBACK;
                } elseif ($bookingDetail->cab_type == Type::CAB_SEDAN_ID) {
                    $cab_type = Type::CAB_SEDAN;
                } elseif ($bookingDetail->cab_type == Type::CAB_SUV_ID) {
                    $cab_type = Type::CAB_SUV;
                }
                $bookingDetail->cab_type = $cab_type;

                $bookingDetail->cab_name = getCabType($bookingDetail->cab_type);
                if ($cabName === 'Hatchback') {
                    $bookingDetail->image = asset('cabs/hatchback.png');
                    $bookingDetail->model = 'Baleno, Swift or similar';
                    $bookingDetail->baggage = '2 Baggage';
                    $bookingDetail->seat = 4;

                } elseif ($cabName === 'Sedan') {
                    $bookingDetail->image = asset('cabs/sedan.png');
                    $bookingDetail->model = 'Dzire, Etios or similar';
                    $bookingDetail->baggage = '3 Baggage';
                    $bookingDetail->seat = 4;

                } elseif ($cabName === 'SUV') {
                    $bookingDetail->image = asset('cabs/suv.png');
                    $bookingDetail->model = 'Xylo, Ertiga or similar';
                    $bookingDetail->baggage = '3 Baggage';
                    $bookingDetail->seat = 6;
                }

         $data = ['details' => $bookingDetail];

         return $this->success($data, "Booking List Retrieved Successfully");
    }

    public function deleteBooking($id) {
        $user = Auth::user();

         $bookingDetail = BookingDetail::where('id', $id)->first();

         if (!$bookingDetail) {
            return $this->error([], 'Booking not found');
        }

         if($bookingDetail->customer_id == $user->id) {
            $bookingDetail->customer_deleted_at = now();
            $bookingDetail->save();
            return $this->success([], 'Booking deleted successfully');
         } else {
             return $this->error([], "You can't delete this booking");
         }
    }

    /** Customers may only see/act on their own bookings; staff users keep full access. */
    private function canAccessBooking($user, BookingDetail $booking): bool
    {
        if (!$user) {
            return false;
        }
        if ($user instanceof User && (int) $user->type === Type::CUSTOMER) {
            return (int) $booking->customer_id === (int) $user->id;
        }

        return true;
    }

    /** True if the client's quoted amounts are more than ₹1 off the server's. */
    private function clientAmountsDiffer(Request $request, int $totalPaise, int $advancePaise): bool
    {
        $clientTotal = (float) $request->input('total_payment', 0);
        $clientAdvance = (float) $request->input('part_payment', 0);

        return abs($clientTotal - FareBreakdown::rupees($totalPaise)) > 1
            || abs($clientAdvance - FareBreakdown::rupees($advancePaise)) > 1;
    }

    /**
     * Optional business GST details for a B2B invoice.
     *
     * @return array|string  booking attributes, or an error message
     */
    private function businessGstDetails(Request $request): array|string
    {
        $gstin = Gstin::normalize($request->input('customer_gstin'));
        if ($gstin === '') {
            return [];
        }
        if (!Gstin::isValid($gstin)) {
            return 'Please enter a valid 15-character GSTIN, or leave it empty.';
        }

        $legalName = trim((string) $request->input('customer_legal_name'));
        $address = trim((string) $request->input('customer_billing_address'));
        if ($legalName === '' || $address === '') {
            return 'Please enter the registered business name and billing address for the GST invoice.';
        }

        return [
            'customer_gstin' => $gstin,
            'customer_legal_name' => mb_substr($legalName, 0, 191),
            'customer_billing_address' => mb_substr($address, 0, 500),
        ];
    }

    private function updateCustomerProfile(int $userId, Request $request, array $gstDetails): void
    {
        $userDetails = User::find($userId);
        if (!$userDetails) {
            return;
        }

        foreach (['name', 'gender', 'country_id', 'state_id', 'phone_number'] as $field) {
            if (!empty($request->{$field})) {
                $userDetails->{$field} = $request->{$field};
            }
        }

        // Remember business GST details for the customer's next booking.
        if ($gstDetails !== []) {
            $userDetails->gstin = $gstDetails['customer_gstin'];
            $userDetails->gst_legal_name = $gstDetails['customer_legal_name'];
            $userDetails->gst_billing_address = $gstDetails['customer_billing_address'];
        }

        $userDetails->save();
    }

    /** Issued GST documents for a booking, with login-free signed links. */
    private function invoiceLinks(BookingDetail $booking): array
    {
        return Invoice::where('booking_id', $booking->id)
            ->where('status', Invoice::ISSUED)
            ->orderBy('id')
            ->get()
            ->map(fn (Invoice $invoice) => [
                'type' => $invoice->type,
                'title' => $invoice->label(),
                'number' => $invoice->number,
                'date' => optional($invoice->issue_date)->format('d-m-Y'),
                'total' => (float) $invoice->total,
                'url' => $invoice->publicUrl(),
            ])
            ->all();
    }
}
