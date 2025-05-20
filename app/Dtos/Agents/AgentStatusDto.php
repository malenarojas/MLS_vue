<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class AgentStatusDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $name,
    ) {}

    /**
     * Convierte el DTO en un arreglo.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
