<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ListingInformationDto extends Data
{
    public function __construct(
        public int|Optional|null $id,
        public string|Optional|null $available_date,
        public string|Optional|null $year_construction,
        public int|Optional|null $parking_slots,
        public int|Optional|null $plant_numbers,
        public int|Optional|null $number_departments,
        public int|Optional|null $sale_sign,
        public float|Optional|null $total_area,
        public float|Optional|null $cubic_volume,
        public float|Optional|null $land_m2,
        public float|Optional|null $land_x,
        public float|Optional|null $land_y,
        public float|Optional|null $construction_area_m,
        public int|Optional|null $total_number_rooms,
        public int|Optional|null $number_bathrooms,
        public int|Optional|null $number_bedrooms,
        public int|Optional|null $number_toiletrooms,
        public string|Optional|null $youtube_link,
        public string|Optional|null $external_link,
        public string|Optional|null $virtual_link,
        public string|Optional|null $virtual_viewer,

        // Foreign keys
        public int|Optional|null $subtype_property_id,
        public int|Optional|null $market_status_id,
        public int|Optional|null $state_property_id,
        public int|Optional|null $property_category_id,
        public int|Optional|null $land_use_id,
        public int|Optional|null $land_category_id,
        public int|Optional|null $parking_type_id,
        public int|Optional|null $listing_id,
    ) {}
}
