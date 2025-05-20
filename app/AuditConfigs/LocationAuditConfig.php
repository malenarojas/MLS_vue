<?php

namespace App\AuditConfigs;

use App\Models\City;
use App\Models\TypeFloor;
use App\Models\Zone;

class LocationAuditConfig extends BaseAuditConfig
{
  public static function fieldMap(): array
  {
    return [
      // Campos simples
      'number' => 'Número',
      'unit_department' => 'Unidad/Departamento',
      'first_address' => 'Primera Dirección',
      'second_address' => 'Segunda Dirección',
      'zip_code' => 'Código Postal',
      'district' => 'Distrito',
      'access_number' => 'Número de Acceso',
      'show_addres_on_website' => 'Mostrar Dirección en el Sitio Web',
      'latitude' => 'Latitud',
      'longitude' => 'Longitud',
      'city_id' => 'Ciudad',
      'zone_id' => 'Zona',
      'listing_id' => 'Listado',
      'type_floor_id' => 'Tipo de Piso',
    ];
  }

  public static function foreignKeys(): array
  {
    return [
      'city_id' => [City::class, 'name'],
      'zone_id' => [Zone::class, 'name'],
      'type_floor_id' => [TypeFloor::class, 'name'],
    ];
  }
}
