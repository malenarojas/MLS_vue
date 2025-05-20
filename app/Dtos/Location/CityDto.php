<?php

namespace App\Dtos\Location;

use Spatie\LaravelData\Data;

class CityDto extends Data
{
    public function __construct(
        public ?int $id = null,       // ID de la ciudad
        public string $name,          // Nombre de la ciudad
        public int $province_id       // ID de la provincia asociada
    ) {}
}