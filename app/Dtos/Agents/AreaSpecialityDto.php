<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class AreaSpecialityDto extends Data
{
    public function __construct(
        public int $area_id,
        public int $speciality_id
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
