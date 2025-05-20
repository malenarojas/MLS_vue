<?php

namespace App\AuditConfigs;

use App\Models\LandCategory;
use App\Models\LandUse;
use App\Models\MarketStatus;
use App\Models\ParkingType;
use App\Models\PropertyCategory;
use App\Models\StateProperty;
use App\Models\SubtypeProperty;

class ListingInformationAuditConfig extends BaseAuditConfig
{
  public static function fieldMap(): array
  {
    return [
      // Campos simples
      'available_date' => 'Fecha de disponibilidad',
      'year_construction' => 'Año de construcción',
      'parking_slots' => 'Espacios de estacionamiento',
      'plant_numbers' => 'Número de plantas',
      'number_departments' => 'Número de departamentos',
      'sale_sign' => 'Cartel de venta',
      'cubic_volume' => 'Volumen cúbico',
      'land_m2' => 'Tamaño del terreno (m2)',
      'land_x' => 'Tamaño del terreno (X)',
      'land_y' => 'Tamaño del terreno (Y)',
      'construction_area_m' => 'Área de construcción (m2)',
      'total_area' => 'Área total (m2)',
      'total_number_rooms' => 'Número total de habitaciones',
      'number_bathrooms' => 'Número de baños',
      'number_bedrooms' => 'Número de dormitorios',
      'number_toiletrooms' => 'Número de medios baños',

      // Campos relacionados con IDs
      'subtype_property_id' => 'Subtipo de propiedad',
      'market_status_id' => 'Estado del mercado',
      'state_property_id' => 'Estado de la propiedad',
      'property_category_id' => 'Categoría de la propiedad',
      'land_use_id' => 'Uso del suelo',
      'land_category_id' => 'Categoría del terreno',
      'parking_type_id' => 'Tipo de estacionamiento',
    ];
  }

  public static function foreignKeys(): array
  {
    return [
      'subtype_property_id' => [SubtypeProperty::class, 'name'],
      'market_status_id' => [MarketStatus::class, 'name_market_status'],
      'state_property_id' => [StateProperty::class, 'name_state_properties'],
      'property_category_id' => [PropertyCategory::class, 'name_properties_categories'],
      'land_use_id' => [LandUse::class, 'name_land_use'],
      'land_category_id' => [LandCategory::class, 'name_land_category'],
      'parking_type_id' => ParkingType::class,
    ];
  }

  public static function valueLabels(): array
  {
    return [
      'sale_sign' => [
        1 => 'Con cartel',
        0 => 'Sin cartel',
      ],
    ];
  }
}
