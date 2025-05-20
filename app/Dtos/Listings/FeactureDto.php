<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class FeactureDto extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $feature_id,
    ) {}
}
