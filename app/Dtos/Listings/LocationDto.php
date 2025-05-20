<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class LocationDto extends Data
{
    public function __construct(
        public string|Optional|null $number,
        public string|Optional|null $unit_department,
        public string|Optional|null $first_address,
        public string|Optional|null $second_address,
        public string|Optional|null $zip_code,
        public string|Optional|null $district,
        public string|Optional|null $access_number,
        public int|Optional|null $show_addres_on_website,
        public float|Optional|null $latitude,
        public float|Optional|null $longitude,
        public int|Optional|null $listing_id,
        public int|Optional|null $type_floor_id,
        public int|Optional|null $city_id,
        public int|Optional|null $zone_id,
        // Datos Optionales
        public int|Optional|null $state_id,
        public int|Optional|null $province_id,
    ) {}
}
