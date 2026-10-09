<?php

namespace App\Http\Resources\Api;

use App\Enums\Type;
use App\Models\Environment;
use App\Services\Pricing\FareBreakdown;
use App\Services\Pricing\FareCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SightSeeingPackageCabPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * `price` is what the customer pays (internal markup + GST included), so
     * existing apps that charge `price` stay correct; `fare` / `tax_amount`
     * break it down. Booking re-calculates the same figures server-side.
     */
    private function amounts($basePrice): array
    {
        $fare = FareCalculator::fromDatabase()->package((float) $basePrice);

        return [
            'price' => FareBreakdown::display($fare->total),
            'fare' => FareBreakdown::display($fare->fare),
            'tax_amount' => FareBreakdown::display($fare->gst),
            'gst_rate' => $fare->gstRate,
        ];
    }

    public function toArray(Request $request): array
    {
        $cabs = [];

        if ($this->hatchback_price) {
            $cabs[] = array(
                'cab_type' => Type::CAB_HATCHBACK_ID,
                'cab_name' => getCabType(Type::CAB_HATCHBACK_ID),
                'image' => asset('cabs/hatchback.png'),
                'model' => 'Baleno, Swift or similar',
                'baggage' => '2 Baggage',
                'seat' => 4,
                'ac' => 'Ac',
                ...$this->amounts($this->hatchback_price),
            );
        }
        if ($this->sedan_price) {
            $cabs[] = array(
                'cab_type' => Type::CAB_SEDAN_ID,
                'cab_name' => getCabType(Type::CAB_SEDAN_ID),
                'image' => asset('cabs/sedan.png'),
                'model' => 'Dzire, Etios or similar',
                'baggage' => '3 Baggage',
                'seat' => 4,
                'ac' => 'Ac',
                ...$this->amounts($this->sedan_price),
            );
        }
        if ($this->suv_price) {
            $cabs[] = array(
                'cab_type' => Type::CAB_SUV_ID,
                'cab_name' => getCabType(Type::CAB_SUV_ID),
                'image' => asset('cabs/suv.png'),
                'model' => 'Xylo, Ertiga or similar',
                'baggage' => '3 Baggage',
                'seat' => 6,
                'ac' => 'Ac',
                ...$this->amounts($this->suv_price),
            );
        }
        return $cabs;
    }
}
