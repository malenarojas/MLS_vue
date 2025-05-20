<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class AchievementDto extends Data
{ public function __construct(
    public ?int $id,
    public string $name_achievements,
    public string $achievement_description
) {}

/**
 * Convierte el DTO en un array.
 */
public function toArray(): array
{
    return get_object_vars($this);
}
}
