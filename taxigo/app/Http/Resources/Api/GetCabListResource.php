<?php

namespace App\Http\Resources\Api;

use App\Services\Pricing\FareBreakdown;
use App\Services\Pricing\FareCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetCabListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // All amounts come from FareCalculator — the same code the booking
        // endpoint uses, so a quote and the booking can never disagree. The
        // internal markup is already inside `price`; `tax_amount` is real GST
        // (0 until GST is switched on).
        $pickupAt = null;
        if (!empty($request->pickup_date) && !empty($request->pickup_time)) {
            try {
                $pickupAt = Carbon::parse($request->pickup_date . ' ' . $request->pickup_time, 'Asia/Kolkata');
            } catch (\Throwable) {
                $pickupAt = null;
            }
        }

        $calculator = $request->attributes->get('fare_calculator');
        if (!$calculator) {
            $calculator = FareCalculator::fromDatabase();
            $request->attributes->set('fare_calculator', $calculator);
        }
        $fare = $calculator->ride((float) $this->base_fare, $this->cabRate ? (int) $this->cabRate->tab : null, $pickupAt);

        $data['cab_id'] = $this->id;
        $data['id'] = $this->cab_rate_id;
        $data['cab_name'] = getCabType($this->cab_type);
        $data['cab_type'] = $this->cab_type;
        $data['price'] = FareBreakdown::display($fare->fare);
        $data['company_payment'] = FareBreakdown::display($fare->platformFee);
        $data['fleet_operator_payment'] = FareBreakdown::display($fare->fleetOperatorPayment);
        $data['tax_amount'] = FareBreakdown::display($fare->gst);
        $data['gst_rate'] = $fare->gstRate;
        $data['cgst_amount'] = FareBreakdown::display($fare->cgst);
        $data['sgst_amount'] = FareBreakdown::display($fare->sgst);
        $data['part_payment'] = FareBreakdown::display($fare->advance);
        $data['full_payment'] = FareBreakdown::display($fare->total);
        $data['remain_payment'] = FareBreakdown::display($fare->balance);
        $data['additional_km_charges'] = $this->additional_km_charges;
        $data['base_km'] = $this->cabRate ? $this->cabRate->base_km : '';
        $data['ac'] = 'Ac';
        $data['surge_price'] = $fare->surge > 0 ? FareBreakdown::display($fare->surge) : '0.00';
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
