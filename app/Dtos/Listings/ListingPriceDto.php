<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class ListingPriceDto extends Data
{
    public int $id;
    public float $amount;
    public int $listingId;
    public int $currencyId;

    /**
     * Constructor para el DTO.
     *
     * @param int $id
     * @param float $amount
     * @param int $listingId
     * @param int $currencyId
     */
    public function __construct(
        int $id,
        float $amount,
        int $listingId,
        int $currencyId,
        
    ) {
        $this->id = $id;
        $this->amount = $amount;
        $this->listingId = $listingId;
        $this->currencyId = $currencyId;
      
}
public static function fromModel(ListingPrice $listingPrice): self
{
    return new self(
        $listingPrice->id,
        (float) $listingPrice->amount,
        $listingPrice->listing_id,
        $listingPrice->currency_id,
    );
}
}