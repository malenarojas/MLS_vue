<?php

namespace App\Traits;

use App\Dtos\Listings\LocationDto;
use App\Models\City;
use App\Models\Listing;
use App\Models\Office;
use App\Models\Province;
use App\Models\Zone;

trait HasLocationData
{
  /**
   * Obtiene los datos de ubicación optimizados para reducir condicionales y mejorar legibilidad.
   */
  public function getLocationData(LocationDto $locationDTO): array
  {
    $provinces = $locationDTO->state_id
      ? Province::select('id', 'name', 'state_id')->where('state_id', $locationDTO->state_id)->get()
      : collect();

    $cities = $locationDTO->province_id
      ? City::select(
        'id',
        'name',
        'latitude',
        'longitude',
        'province_id'
      )->where('province_id', $locationDTO->province_id)->get()
      : collect();

    $zones = $locationDTO->city_id
      ? Zone::select(
        'id',
        'name',
        'latitude',
        'longitude',
      )->where('city_id', $locationDTO->city_id)->get()
      : collect();

    return compact('provinces', 'cities', 'zones');
  }

  private function buildFullAddress(Listing $listing): string
  {
    $parts = array_filter([
      $listing->location?->first_address ?? null,
      $listing->location?->zone?->name ?? null,
      $listing->location?->city?->name ?? null,
      $listing->location?->city?->province?->name ?? null,
      $listing->location?->city?->province?->state?->name ?? null,
    ]);

    return implode(', ', $parts);
  }

  private function fullAddressFromOffice(Office $office): string
  {
    $parts = array_filter([
      $office->address ?? null,
      $office->city->name ?? null,
      $office->province->name ?? null,
      $office->province->state->name ?? null,
    ]);

    return implode(', ', $parts);
  }
}
