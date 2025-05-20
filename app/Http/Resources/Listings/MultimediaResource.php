<?php

namespace App\Http\Resources\Listings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultimediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'link' => $this->link,
            'description' => $this->description,
            'default' => $this->default,
            'type' => $this->multimedia_type->name,
            'room' => new RoomResource($this->room),
        ];
    }
}
