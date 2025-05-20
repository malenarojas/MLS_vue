<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class SpecialityDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?int $parent_id
    ) {}

    /**
     * Convierte el DTO a un arreglo.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
