<?php

namespace App\Dtos\Location;

use Spatie\LaravelData\Data;

class StateDto extends Data
{
    public function __construct(
        public ?int $id = null,       // ID del estado
        public string $name,          // Nombre del estado
    ) {}
}