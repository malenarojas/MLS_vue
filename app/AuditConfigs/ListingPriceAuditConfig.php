<?php

namespace App\AuditConfigs;

use App\Models\Currency;

class ListingPriceAuditConfig extends BaseAuditConfig
{
  public static function fieldMap(): array
  {
    return [
      // Campos simples
      'amount' => 'Precio',
      // Campos relacionados
      'currency_id' => 'Moneda',
    ];
  }

  public static function foreignKeys(): array
  {
    return [
      'currency_id' => [Currency::class, 'name'],
    ];
  }
}
