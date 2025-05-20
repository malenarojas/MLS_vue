<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class RemaxTitleDto extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public int $user_type_id
    ) {}

    /**
     * Convierte el DTO en un array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
