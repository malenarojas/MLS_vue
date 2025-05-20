<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;
class AgentDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $agent_internal_id,
        public ?int $office_id,
        public ?int $region_id,
        public ?int $user_id,
        public ?string $date_joined,
        public ?string $date_termination,
        public ?int $agent_status_id,
        public ?string $studies,
        public ?string $additional_education,
        public ?string $image_name,
        public ?string $previous_occupation,
        public ?string $license_type,
        public ?string $license_department,
        public ?string $year_obtained_license,
        public ?string $expiration_date_license,
        public ?string $license_number,
        public ?string $address,
        public ?string $landline_phone,
        public ?string $marketing_slogan,
        public ?string $website_descripction,
        public ?string $countries_interested,
        public ?string $meta_tag_description,
        public ?string $bullet_point_one,
        public ?string $bullet_point_two,
        public ?string $bullet_point_three,
        public ?string $meta_tag_keywords,
		public ?string $rejectionReason,
        public ?string $deactivation_date,
        public ?float $commission_percentage,
        public ?string $nro_internacional_remax,
        public ?string $id_business_agent,
        public ?int $contact_id,
        public ?string $url_alterno,

    ) {}

    public function toArray(): array
    {
        return get_object_vars($this); // ← incluye todos, incluso los que son null
    }

}
