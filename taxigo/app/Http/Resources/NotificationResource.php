<?php

namespace App\Http\Resources;

use App\Enums\NotificationEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = $this->data;

        if (isset($data) && isset($data['image']) && !empty($data['image'])) {
            $data['image'] = getFileUrl($data['image']);
        }

        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'title'      => $this->title,
            'text'       => $this->text,
            'data'       => $data,
            'link'       => $this->link,
            'status'     => $this->status,
        ];
    }
}
