<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class DocumentationTypeDto extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $parent_id,
    ) {}
}
