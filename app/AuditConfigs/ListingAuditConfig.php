<?php

namespace App\AuditConfigs;

use App\Models\Area;
use App\Models\StatusListing;
use App\Models\ContractType;
use App\Models\ListingTransactionType;
use App\Models\PriceType;
use App\Models\RentTimeframe;

class ListingAuditConfig extends BaseAuditConfig
{
  public static function fieldMap(): array
  {
    return [
      // Campos simples
      'cancellation_date' => 'Fecha de cancelación',
      'cancellation_reason' => 'Motivo de cancelación',
      'contract_end_date' => 'Fin de contrato',
      'date_of_listing' => 'Fecha de creación',
      'description_website' => 'Descripción Web',
      'is_published' => 'Publicado',
      'reference' => 'Referencia',
      'property_registration_number' => 'Número de registro de propiedad',
      'financial_note' => 'Nota financiera',
      'title' => 'Título',
      'marketing_description' => 'Descripción de marketing',
      'location_information' => 'Información de ubicación',
      'is_external' => 'Captación externa',
      // 'translations' => 'Descripción en otro idiomas',

      // Campos relacionados
      'area_id' => 'Zona',
      'contract_type_id' => 'Tipo de contrato',
      'price_type_id' => 'Tipo de precio',
      'project_id' => 'Proyecto',
      'status_listing_id' => 'Estado del listing',
      'transaction_type_id' => 'Tipo de transacción',
      'rent_timeframe_id' => 'Periodo de renta',
    ];
  }

  public static function foreignKeys(): array
  {
    return [
      'area_id' => [Area::class, 'name'],
      'contract_type_id' => [ContractType::class, 'name'],
      'price_type_id' => [PriceType::class, 'name'],
      // 'project_id' => [Project::class, 'name'],
      'status_listing_id' => [StatusListing::class, 'name'],
      'transaction_type_id' => [ListingTransactionType::class, 'name'],
      'rent_timeframe_id' => [RentTimeframe::class, 'name'],
    ];
  }
}