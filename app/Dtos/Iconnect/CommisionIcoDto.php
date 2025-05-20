<?php

namespace App\Dtos\Iconnect;

use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class CommisionIcoDto extends Data
{
  public function __construct(
    #[Computed]
    public ?int $recruitment_commission = null,
    #[Computed]
    public ?string $type_recruitment_commission = null,
    #[Computed]
    public ?int $sales_commission = null,
    #[Computed]
    public ?string $sales_commission_type = null,

    // Comision de captacion
    public ?int $ComTotalPct = null,
    public ?int $ComTotalFix = null,

    // Comisión Venta
    public ?int $ComBuyAgentPct = null,
    public ?int $ComBuyAgentFix = null
  ) {
    // Comision de captación (Fija o Porcentaje)
    if (!is_null($this->ComTotalPct)) {
      $this->recruitment_commission = $this->ComTotalPct;
      $this->type_recruitment_commission = 'P';
    } elseif (!is_null($this->ComTotalFix)) {
      $this->recruitment_commission = $this->ComTotalFix;
      $this->type_recruitment_commission = 'C';
    } else {
      Log::error("Error no hay Commision de captacion");
    }

    // Comision de venta (Fija o Porcentaje)
    if (!is_null($this->ComBuyAgentPct)) {
      $this->sales_commission = $this->ComBuyAgentPct;
      $this->sales_commission_type = 'P';
    } elseif (!is_null($this->ComBuyAgentFix)) {
      $this->sales_commission = $this->ComBuyAgentFix;
      $this->sales_commission_type = 'C';
    } else {
      Log::error("Error no hay Commision de venta");
    }
  }
}
