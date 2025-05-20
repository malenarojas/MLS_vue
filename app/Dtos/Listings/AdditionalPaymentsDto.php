<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class AdditionalPaymentDto extends Data
{
    public function __construct(
        public float $amount,
        public string $payment_term,
        public ?string $note,
        public int $type_additional_payment_id,
        public int $listing_id
    ) {}
}
