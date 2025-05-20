<?php

namespace App\AuditConfigs;

class CommissionOptionAuditConfig extends BaseAuditConfig
{
  public static function fieldMap(): array
  {
    return [
      'recruitment_commission' => 'Comisión por captación',
      'type_recruitment_commission' => 'Tipo de comisión por captación',
      'sales_commission' => 'Comisión por venta',
      'sales_commission_type' => 'Tipo de comisión por venta',
    ];
  }

  public static function valueLabels(): array
  {
    return [
      'type_recruitment_commission' => [
        'P' => 'Porcentaje',
        'C' => 'Cantidad',
      ],
      'sales_commission_type' => [
        'P' => 'Porcentaje',
        'C' => 'Cantidad',
      ],
    ];
  }
}
