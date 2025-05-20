<?php

namespace App\Dtos\Iconnect;

use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class LocationIcoDto extends Data
{
  public function __construct(

    #[MapInputName('StreetNumber')]
    public ?string $number,
    #[MapInputName('ApartmentNumber')]
    public ?string $unit_department,
    #[MapInputName('StreetName')]
    public ?string $first_address,
    #[MapInputName('AddressLine')]
    public ?string $second_address,
    #[MapInputName('PostalCode')]
    public ?string $zip_code,

    #[MapInputName('FloorNumberUID')]
    public ?string $type_floor_id,
    #[MapInputName('District')]
    public ?string $district,
    #[MapInputName('District')]
    public ?string $local_zone,
    // GeoData
    #[MapInputName('GeoData.LocalZoneID')]
    public ?string $zone_id,
    #[MapInputName('GeoData.LocalZone')]
    public ?string $zone,
    #[MapInputName('GeoData.CityId')]
    public ?string $city_id,
    #[MapInputName('GeoData.City')]
    public ?string $city,
    #[MapInputName('GeoData.ProvinceId')]
    public ?string $province_id,
    #[MapInputName('GeoData.Province')]
    public ?string $province,
    #[MapInputName('GeoData.CountryId')]
    public ?string $country_id,
    #[MapInputName('GeoData.Country')]
    public ?string $country,
    #[MapInputName('GeoCoordinates.Latitude')]
    public ?string $latitude,
    #[MapInputName('GeoCoordinates.Longitude')]
    public ?string $longitude,
    #[MapInputName('ShowFullAddress')]
    public ?string $show_addres_on_website,
  ) {
    $this->type_floor_id = match ($this->type_floor_id) {
      346 => 1,  // Sótano
      351 => 2,  // Planta Baja
      355 => 3,  // Mezzanine
      333 => 4,  // 1
      336 => 5,  // 2
      337 => 6,  // 3
      338 => 7,  // 4
      339 => 8,  // 5
      340 => 9,  // 6
      341 => 10, // 7
      342 => 11, // 8
      343 => 12, // 9
      335 => 13, // 10+
      334 => 14, // 1 - 20
      352 => 15, // Último Piso
      344 => 16, // Ático
      356 => 17, // Penthouse
      345 => 18, // Medio Piso
      353 => 19, // Principal
      354 => 20, // Maisonette
      357 => 21, // Casa de Servicio
      358 => 22, // Piso de Arriba
      359 => 23, // Apartamento en tres niveles
      360 => 24, // Todo el edificio
      349 => 25, // Galería
      348 => 26, // Apartamento Planta Baja
      350 => 27, // Desván
      347 => 28, // Duplex
      600 => 29, // 0
      // 5510 => 30, // Duplex (Esta duplicado)
      5511 => 30, // ElevatedFloor
      5512 => 31, // Semibasement
      default => null, // Si no hay un valor válido, se mantiene null
    };

    $this->show_addres_on_website = $this->show_addres_on_website ? 1 : 0;
  }
}
