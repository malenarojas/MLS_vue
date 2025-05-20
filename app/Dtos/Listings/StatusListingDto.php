<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class StatusListingDto extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
