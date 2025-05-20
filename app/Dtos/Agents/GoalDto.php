<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class GoalDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?float $commision_goal,
        public ?int $contact_goal,
        public ?int $agent_id,
    ) {}

    /**
     * Convierte el DTO en un arreglo.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
