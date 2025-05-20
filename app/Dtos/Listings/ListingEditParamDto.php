<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class ListingEditParamDto extends Data
{
    public function __construct(
        public ?string $contact_search = null,
    ) {}
}
