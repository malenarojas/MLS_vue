<?php

namespace App\Dtos\Excels;

use App\Models\PropertyCategory;
use Spatie\LaravelData\Data;

class ListingExcelDto extends Data
{
  public function __construct(
    public ?string $OfficeID,
    public ?string $AgentID,
    public ?string $ListingKey,
    public ?string $mlsId,
    public ?float $SoldPrice,
    public ?string $SoldPriceCurrency,
    public ?float $CurrentListingPrice,
    public ?string $CurrentListingCurrency,
    public ?string $PropertyType,
    public ?string $TransactionType,
    public ?string $City,
    public ?string $Province,
    public ?string $Region,
    public ?string $TotalArea,
    public ?string $LotSize,
    public ?string $Key,
    public ?string $cancellation_reason,
    public ?string $cancellation_date,
    public ?string $date_of_listing,
    public ?string $updated_at,
    public ?string $contract_end_date,
    public ?string $property_category,
    public ?string $property_category_id,
  ) {
    $this->TransactionType = $this->TransactionType === 'For Sale' ? 1 : 2;

    if ($this->property_category) {
      $property_category = PropertyCategory::firstOrCreate([
        'name_properties_categories' => $this->property_category,
      ], [
        'name_properties_categories' => $this->property_category,
        'type_property_id' => null,
      ]);
      $this->property_category_id = $property_category->id;
    } else {
      $this->property_category_id = null;
    }
  }
}
