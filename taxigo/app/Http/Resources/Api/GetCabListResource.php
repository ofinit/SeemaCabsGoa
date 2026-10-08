<?php

namespace App\Http\Resources\Api;

use App\Enums\Type;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Environment;
use Illuminate\Support\Facades\Log;

class GetCabListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $airportPickupPercentage = Environment::where('title', Type::AIRPORT_PICKUP_PERCENTAGE)->first();

        $surgeDetails = Environment::where('title', Type::SURGE_PRICE)->first();
        $gst = Environment::where('title', Type::GstTitle)->first();
        $tds = Environment::where('title', Type::TdsTitle)->first();
        $totalCommission = Environment::where('title', Type::TotalCommission)->first();
        $companyCommission = Environment::where('title', Type::CompanyCommission)->first();
        $price = (float) $this->base_fare;

        if($this->cabRate->tab == Type::AIRPORT_PICKUP)
        {

            $airportPickupPercentage = $airportPickupPercentage ? $airportPickupPercentage->value : 0 ;
            //calculate price according to the airport pick up percentage
            $price = $price + ($price * (double)$airportPickupPercentage/100);
        }

        $price = round($price);

        $surgePrice = 0;
        // check surge price
        if ($surgeDetails != null) {
            $surgeData = json_decode($surgeDetails->value, true);

            if (isset($surgeData['surge_enable']) && $surgeData['surge_enable'] == Type::SURGE_PRICE_ENABLED) {
                date_default_timezone_set('Asia/Kolkata');
                $currentDateTime = time();

                if (!empty($request->pickup_date) && !empty($request->pickup_time)) {
                    $currentDateTime = strtotime($request->pickup_date . ' ' . $request->pickup_time);
                }

                $surgePrice = 0;
                $startDates = $surgeData['surge_start_date'];
                $endDates = $surgeData['surge_end_date'];
                $startTimes = $surgeData['surge_start_time'];
                $endTimes = $surgeData['surge_end_time'];

                Log::info("surge logic");

                for ($i = 0; $i < count($startDates); $i++) {
                    $surgeStartDateTime = strtotime($startDates[$i] . ' ' . $startTimes[$i]);
                    $surgeEndDateTime = strtotime($endDates[$i] . ' ' . $endTimes[$i]);
                     Log::info("surge logic start time " . $startDates[$i] . ' ' . $startTimes[$i]);
                     Log::info("surge logic end time " . $endDates[$i] . ' ' . $endTimes[$i]);

                    Log::info("surge logic calculate $currentDateTime :: $surgeStartDateTime :: $surgeEndDateTime");


                    Log::info( "Current Date & Time: " . date("Y-m-d H:i:s", $currentDateTime));
                    Log::info( "Surge Start Date & Time: " . date("Y-m-d H:i:s", $surgeStartDateTime));
                    Log::info( "Surge End Date & Time: " . date("Y-m-d H:i:s", $surgeEndDateTime));

                    if ($surgeStartDateTime <= $currentDateTime && $surgeEndDateTime >= $currentDateTime) {

                        $surgePrice = (float)$surgeData['surge_percentage'][$i]  ? ($price * (float) $surgeData['surge_percentage'][$i]) / 100  : 0;
                        break;
                    }
                }
            }
        }

        $totalCommissionValue = (float) ($totalCommission ? $totalCommission->value : 0.00);

        $gstPriceTotal = 0;
        $tdsPriceTotal = 0;
        $commissionPrice = 0;
        $price = ($price + ($surgePrice));

        $price = round($price);
        if ($gst) {
            $gstPriceTotal = ($price * ($gst->value / 100));
        }
        if ($tds) {
            $tdsPriceTotal = ($price * ($tds->value / 100));
        }
        if ($totalCommission) {
            $commissionPrice = ($price * ($totalCommissionValue / 100));
        }
        // $taxAmount = $gstPriceTotal + $tdsPriceTotal;
        $commissionGstPriceTotal = 0;
        if ($gst) {
            $commissionGstPriceTotal = ($commissionPrice * ($gst->value / 100));
        }
        $partPayment = $commissionPrice + $commissionGstPriceTotal;
        //dd($partPayment, )
        $fullPayment = round($price + $gstPriceTotal);
        $remainPayment = $fullPayment - $partPayment;
        $companyCommissionValue = $companyCommission ? $companyCommission->value : 0.00;
        $company_payment = (string)round(($price * ($companyCommissionValue / 100)));
        $fleet_operator_payment = $partPayment - ($price * ($companyCommissionValue / 100));
        $data['cab_id'] = $this->id;
        $data['id'] = $this->cab_rate_id;
        $data['cab_name'] = getCabType($this->cab_type);
        $data['cab_type'] = $this->cab_type;
        $data['price'] = (string)round($price);
        $data['company_payment'] = $company_payment;
        $data['fleet_operator_payment'] = (string)round($fleet_operator_payment);
        $data['tax_amount'] = (string)round($gstPriceTotal);
        $data['part_payment'] = (string)round($partPayment);
        $data['full_payment'] = (string)round($fullPayment);
        $data['remain_payment'] = (string)round($remainPayment);
        $data['additional_km_charges'] = $this->additional_km_charges;
        $data['base_km'] = $this->cabRate ? $this->cabRate->base_km : '';
        $data['ac'] = 'Ac';
        $data['surge_price']  = (float)$surgePrice > 0 ? (string) ($surgePrice) : '0.00';
        if ($data['cab_name'] == 'Hatchback') {
            $data['image'] = asset('cabs/hatchback.png');
            $data['model'] = 'Baleno, Swift or similar';
            $data['baggage'] = '2 Baggage';
            $data['seat'] = 4;
        } elseif ($data['cab_name'] == 'Sedan') {
            $data['image'] = asset('cabs/sedan.png');
            $data['model'] = 'Dzire, Etios or similar';
            $data['baggage'] = '3 Baggage';
            $data['seat'] = 4;
        } elseif ($data['cab_name'] == 'SUV') {
            $data['image'] = asset('cabs/suv.png');
            $data['model'] = 'Xylo, Ertiga or similar';
            $data['baggage'] = '3 Baggage';
            $data['seat'] = 6;
        }
        return $data;
    }
}
