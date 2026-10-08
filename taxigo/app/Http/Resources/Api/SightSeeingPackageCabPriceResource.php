<?php

namespace App\Http\Resources\Api;

use App\Enums\Type;
use App\Models\Environment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SightSeeingPackageCabPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
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
                'price' => (string)round($this->hatchback_price),
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
                'price' => (string)round($this->sedan_price),
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
                'price' => (string)round($this->suv_price),
            );
        }
        return $cabs;
    }
}
