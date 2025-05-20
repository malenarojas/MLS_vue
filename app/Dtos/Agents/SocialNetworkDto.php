<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class SocialNetworkDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?int $state,
        public ?string $url,
        public ?int $agent_id // Nuevo campo agent_id

    ) {}

    /**
     * Convierte el DTO a un arreglo.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
