<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data['id'] = $this->id;
        $data['banner_image'] = $this->add_banner_image;
        $data['banner_url'] = $this->banner_url;
        $screens = json_decode($this->screens, true);
        $data['screens'] = getScreenTitleList($screens ?? 0);
        return $data;
    }
}
