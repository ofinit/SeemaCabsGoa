<?php

namespace App\Http\Resources\Api;

use App\Models\SightSeeingPackageHasImages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SightSeeingPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sightSeeingImg = $this->packageImages;
        $images = [];
        foreach ($sightSeeingImg as $key => $value) {
            $images[] = getFileUrl($value->image);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'start_time' => Carbon::parse($this->start_time)->format('h:i A'),
            'end_time' => Carbon::parse($this->end_time)->format('h:i A'),
            'location' => $this->location,
            'description' => $this->description,
            'terms_and_condition' => $this->terms_and_condition,
            'images' => $images,
        ];
    }
}
