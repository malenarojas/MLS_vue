<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class AreaDto extends Data
{

    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $description
    ) {}

    /**
     * Convierte el DTO a un arreglo.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
