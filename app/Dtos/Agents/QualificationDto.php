<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class QualificationDto extends Data
{ public function __construct(
    public ?int $id,
    public float $qualification,
    public string $comment,
    public string $reference_type,
    public int $agent_id,
) {}

/**
 * Convierte el DTO en un array.
 */
public function toArray(): array
{
    return get_object_vars($this);
}
}
