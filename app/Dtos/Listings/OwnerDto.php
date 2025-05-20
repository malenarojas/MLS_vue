<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class OwnerDto extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $last_name,
        public ?string $email,
        public ?string $mobile,
    ) {}
}
