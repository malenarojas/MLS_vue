<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CommissionOptionDto extends Data
{
    public function __construct(
        public int|Optional|null $id = null,
        public float|Optional|null $recruitment_commission,
        public string|Optional|null $type_recruitment_commission,
        public float|Optional|null $sales_commission,
        public string|Optional|null $sales_commission_type,
        public int|Optional|null $listing_id,
    ) {}
}
