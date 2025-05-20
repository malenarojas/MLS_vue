<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;

class OfficeDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $region_id,
        public ?int $office_id,
        public ?int $international_code,
        public ?string $name,
        public ?string $city,
        public ?string $province,
        public ?int $city_id,
        public ?int $province_id,
        public ?string $unit,
        public ?string $country,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $office_start_date,
        public ?string $first_updated_to_web,
        public ?bool $access_ilist_net,
        public ?bool $succeed_certified,
        public ?int $is_regional_office,
        public ?bool $is_satellite_office,
        public ?string $first_year_licenced,
        public ?bool $is_commercial,
        public ?bool $is_collection,
        public ?string $date_time_stamp,
        public ?bool $active_office,
        public ?bool $is_external,
        public ?string $office_iconnect_id,
        public ?string $office_intl_id,
        public ?string $macro_office,
        public ?string $office_type,
         public ?bool $hide_office_from_web,
         public ?bool $show_whatsapp,
         public ?string $phone,
         public ?string $email,
         public ?string $image,
         public ?string $cell_phone,
         public ?string $number,
         public ?string $address,
         public ?string $address2,
         public ?string $postal_code,
         public ?string $floor,
         public ?int $state_id,
         public ?int $zone_id,
         // Licencia
         public ?string $expiration_date,
         public ?string $first_year_licensed,
         public ?string $license_number,
         public ?string $license_department,

         // Marketing
         public ?string $marketing_slogan,
         public ?string $website_description,
         public ?string $closure,
         public ?string $short_link,
         public ?string $office_website,

         // Bullet points
         public ?string $bullet_point_one,
         public ?string $bullet_point_two,
         public ?string $bullet_point_three,
         public ?string $bullet_point_four,

         // SEO
         public ?string $meta_tag_keywords,
         public ?string $meta_tag_description,
         public ?string $schedule_weekdays,
         public ?string $schedule_saturday,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
