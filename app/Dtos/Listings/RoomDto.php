<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class RoomDto extends Data
{
    public function __construct(
        public ?int $id = null,
        public string|Optional|null $description,
        public float|Optional|null $size,
        public float|Optional|null $dimension_x,
        public float|Optional|null $dimension_y,
        public int|Optional|null $room_type_id,
        public int|Optional|null $information_id,
    ) {}
}
