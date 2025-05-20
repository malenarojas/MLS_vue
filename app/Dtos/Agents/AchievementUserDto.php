<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class AchievementUserDto extends Data
{
    public function __construct(
        public ?int $id,
        public ? string $achievement_date,
        public ?int $enable_achievement,
        public ?int $achievement_id,
        public ?int $user_id
    ) {}

    /**
     * Convierte el DTO en un array.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
