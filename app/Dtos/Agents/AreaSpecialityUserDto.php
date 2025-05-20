<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class AreaSpecialityUserDto extends Data
{

    public function __construct(
        public ?int $id,
        public int $area_speciality_id,
        public int $user_id,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
