<?php

namespace App\Dtos\Iconnect;

use App\Models\MarketStatus;
use App\Models\PropertyCategory;
use App\Models\StateProperty;
use App\Models\SubtypeProperty;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ListingInformationIcoDto extends Data
{
  public function __construct(
    #[MapInputName('Dates.AvailableDate')]
    public ?string $available_date,
    #[MapInputName('Dates.YearBuilt')]
    public ?string $year_construction,
    #[MapInputName('PropertyStatusID')] // Estado de propiedad
    public ?int $state_property_id,
    #[MapInputName('PropertyStatus')]
    public ?string $state_property,
    #[MapInputName('PropertyCategoryID')] // Categoria de propiedad
    public ?int $property_category_id,
    #[MapInputName('PropertyCategory')]
    public ?string $property_category,
    #[MapInputName('PropertyTypeID')] // SubtypeProperty
    public ?int $subtype_property_id,
    #[MapInputName('PropertyType')]
    public ?string $subtype_property,
    #[MapInputName('MarketStatusID')]
    public ?int $market_status_id,
    #[MapInputName('MarketStatus')]
    public ?string $market_status,

    // Number
    #[MapInputName('Numbers.ParkingSpaces')]
    public ?int $parking_slots,

    #[MapInputName('Numbers.NumberOfApartmentsInBuilding')]
    public ?int $number_departments,
    public ?int $plant_numbers, // No hay numero de plantas
    public ?int $sale_sign, // No hay si tiene letrero de venta

    #[MapInputName('Area.TotalArea')]
    public ?int $total_area,
    #[MapInputName('Area.CubicVolume')]
    public ?int $cubic_volume,
    #[MapInputName('Area.LotSizeM2')]
    public ?int $land_m2,
    #[MapInputName('Area.LotSize')]
    public ?string $lot_size, // No hay tamaño de lote m x n
    #[MapInputName('Area.BuiltArea')]
    public ?int $construction_area_m,

    // Habitaciones
    #[MapInputName('Numbers.TotalNumOfRooms')]
    public ?int $total_number_rooms,
    #[MapInputName('Numbers.NumberOfBedrooms')]
    public ?int $number_bedrooms,
    #[MapInputName('Numbers.NumberOfBathrooms')]
    public ?int $number_bathrooms,
    #[MapInputName('Numbers.NumberOfToiletRooms')]
    public ?int $number_toiletrooms,

    public ?int $listing_id,
  ) {
    if ($this->subtype_property) {
      $subtype_property = SubtypeProperty::firstOrCreate([
        'name' => $this->subtype_property,
      ], [
        'name' => $this->subtype_property,
        'type_property_id' => null,
      ]);
      $this->subtype_property_id = $subtype_property->id;
    } else {
      $this->subtype_property_id = null;
    }

    if ($this->state_property) {
      $state_property = StateProperty::firstOrCreate([
        'name_state_properties' => $this->state_property,
      ], [
        'name_state_properties' => $this->state_property,
        'type_property_id' => null,
      ]);
      $this->state_property_id = $state_property->id;
    } else {
      $this->state_property_id = null;
    }

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

    if ($this->market_status) {
      $market_status = MarketStatus::firstOrCreate([
        'name_market_status' => $this->market_status,
      ], [
        'name_market_status' => $this->market_status,
        'type_property_id' => null,
      ]);
      $this->market_status_id = $market_status->id;
    } else {
      $this->market_status_id = null;
    }

    if ($this->lot_size !== null) {
      // Log::info('Existe Lot size: ' . $this->lot_size);
    }

    // Format to Date
    $this->available_date = $this->available_date !== null ? date('Y-m-d', strtotime($this->available_date)) : null;
    $this->year_construction = $this->year_construction !== null ? date('Y-m-d', strtotime($this->year_construction)) : null;
  }
}
