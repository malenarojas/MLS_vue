<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class MarketAnalysisDto extends Data
{
     // Parámetros de FilterByCaptation
     public ?int $status;
     public ?string $start_date;
     public ?string $end_date;
     public ?float $minimum_price;
     public ?float $maximum_price;

     // Parámetros de FilterByDetails
     public ?int $market_segment;
     public ?int $transaction_type;
     public ?int $property_status;
     public ?int $contract_type;

     // Parámetros de FilterByLocation
     public ?int $region_id;
     public ?int $state_id;
     public ?int $province_id;
     public ?int $city_id;
     public ?int $zone_id;
     public ?string $street_name;
     public ?string $street_number;
     public ?string $address;
     public ?string $district;
     public ?string $postal_code;
     public ?int $only_office;

     public function __construct(array $data)
     {
         // Asignación de valores con valores predeterminados de null
         $this->status = $data['status'] ?? null;
         $this->start_date = $data['start_date'] ?? null;
         $this->end_date = $data['end_date'] ?? null;
         $this->minimum_price = $data['minimum_price'] ?? null;
         $this->maximum_price = $data['maximum_price'] ?? null;

         $this->market_segment = $data['market_segment'] ?? null;
         $this->transaction_type = $data['transaction_type'] ?? null;
         $this->property_status = $data['property_status'] ?? null;
         $this->contract_type = $data['contract_type'] ?? null;

         $this->country = $data['country'] ?? null;
         $this->region_id = $data['region_id'] ?? null;
         $this->state_id = $data['state_id'] ?? null;
         $this->province_id = $data['province_id'] ?? null;
         $this->city_id = $data['city_id'] ?? null;
         $this->zone_id = $data['zone_id'] ?? null;
         $this->street_name = $data['street_name'] ?? null;
         $this->street_number = $data['street_number'] ?? null;
         $this->address = $data['address'] ?? null;
         $this->district = $data['district'] ?? null;
         $this->postal_code = $data['postal_code'] ?? null;
         $this->only_office = $data['only_office'] ?? null;
     }
}
