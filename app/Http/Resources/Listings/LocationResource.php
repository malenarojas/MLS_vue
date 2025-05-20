<?php

namespace App\Http\Resources\Listings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'number' => $this->number,
            'unit_department' => $this->unit_department,
            'first_address' => $this->first_address,
            'second_address' => $this->second_address,
            'zip_code' => $this->zip_code,
            'district' => $this->district,
            'access_number' => $this->access_number,
            'show_addres_on_website' => $this->show_addres_on_website,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'state_id' => $this->city->province->state->id ?? null,
            'province_id' => $this->city->province->id ?? null,
            'city_id' => $this->city->id ?? null,
            'zone_id' => $this->zone->id ?? null,
            // Position
            'city_lat' => $this->city->latitud ?? null,
            'city_lng' => $this->city->longitud ?? null,
            'zone_lat' => $this->zone->latitud ?? null,
            'zone_lng' => $this->zone->longitud ?? null,
            // Relations
            'type_floor_id' => $this->type_floor_id ?? null,
        ];
    }
}
