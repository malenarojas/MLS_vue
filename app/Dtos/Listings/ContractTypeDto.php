<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class ContractTypeDto extends Data
{
    public function __construct(
        public string $name
    ) {}
}
