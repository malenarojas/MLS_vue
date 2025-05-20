<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class UserDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $first_name,
        public ?string $middle_name,
        public ?string $last_name,
        public ?string $name_to_show,
        public ?string $ci,
        public ?string $gender,
        public ?string $phone_number,
        public string $email,
        public ?string $url,
        public ?string $remax_start_date,
        public ?string $password,
        public string $username,
        public ?string $birthdate,
        public ?int $user_type_id,
        public ?int $remax_title_id,
        public ?int $remax_title_to_show_id,
        public ?int $team_status_id,
        public ?string $state_url,
        public ?int $customer_preference_id,
        public ?array $area_specialities = [], // IDs de especialidades
        public ?array $achievements = [],     // IDs de logros
        public ?array $area_services = [],    // IDs de servicios
    ) {}

}
