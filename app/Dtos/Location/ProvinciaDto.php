<?php

namespace App\Dtos\Location;

use Spatie\LaravelData\Data;

class ProvinciaDto extends Data
{
    public function __construct(
        // Add your fields here
        public ?int $id = null,       // ID de la provincia
        public string $name,          // Nombre de la provincia
        public int $state_id          // ID del estado asociado
    ) {}
}