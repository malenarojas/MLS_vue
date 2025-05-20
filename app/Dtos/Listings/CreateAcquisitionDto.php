<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class CreateAcquisitionDto extends Data
{
    public function __construct(
        public ?int $office_id,
        public ?int $transaction_type_id,
        public ?int $area_id,
        public ?int $agent_id,
        public ?int $subtype_property_id,
    ) {}
}
