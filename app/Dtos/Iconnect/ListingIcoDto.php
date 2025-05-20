<?php

namespace App\Dtos\Iconnect;

use App\Models\PriceType;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ListingIcoDto extends Data
{
  public function __construct(
    #[MapInputName('Key')]
    public string $key,
    #[MapInputName('AgentId')]
    public int $agent_internal_id,
    public string $MLSID,

    // Date
    #[MapInputName('Dates.ListingDate')]
    public ?string $date_of_listing,
    #[MapInputName('Dates.ExpiryDate')]
    public ?string $contract_end_date,

    // #[MapInputName('ListingDescription')]
    // public ?string $description_website,
    // #[MapInputName('ListingTitle')]
    // public ?string $title,
    // public ?string $marketing_description, // Description

    // public ?string $location_information,
    #[MapInputName('TransactionTypeID')]
    public ?int $transaction_type_id,
    // #[MapInputName('TransactionType')]
    // public ?string $transaction_type,
    #[MapInputName('ContractTypeID')]
    public ?int $contract_type_id,
    #[MapInputName('ContractType')]
    public ?string $contract_type,
    #[MapInputName('ListingStatusID')]
    public ?int $status_listing_id,
    #[MapInputName('ListingStatus')]
    public ?string $status_listing,
    #[MapInputName('CommPropertyTypeID')]
    public ?int $com_id,
    #[MapInputName('CommPropertyType')]
    public ?string $com,

    #[MapInputName('PriceDetails.PriceTypeID')]
    public ?int $price_type_id,
    #[MapInputName('PriceDetails.PriceType')]
    public ?string $price_type,
    #[MapInputName('CommercialResidential')]
    public ?int $area_id,
    #[MapInputName('IsForeignProperty')]
    public ?int $is_external,

    // Datos en transacciones
    #[MapInputName('Dates.SoledDate')]
    public ?string $sold_date,
    #[MapInputName('Dates.PossessionDate')]
    public ?string $posession_date,

    #[MapInputName('History.CreatedDate')]
    public ?string $create_at,
    #[MapInputName('History.ModifiedDate')]
    public ?string $update_at,

    // Aux
    #[MapInputName('PropertyCategoryID')]
    public ?int $property_category_id,
    #[MapInputName('PropertyCategory')]
    public ?string $property_category,
  ) {
    $this->key = strtoupper($this->key);

    $this->contract_type_id = match ($this->contract_type_id) {
      25 => 1, // Exclusivo
      30 => 2, // Exclusivo cliente
      29 => 3, // No exclusivo
      default => null,
    };

    // Log::info('ListingIcoDto Tipo de transaction ' . $this->transaction_type_id);

    if ($this->property_category_id === 2613) {
      // Log::info('ListingIcoDto Anticretico');
      $this->transaction_type_id = 3; // Anticretico
    } else {
      $this->transaction_type_id = match ($this->transaction_type_id) {
        261 => 1, // En venta
        260 => 2, // Alquiler
        default => 261,
      };
    }

    // Log::info('ListingIcoDto Tipo de transaction ' . $this->transaction_type_id);

    $this->status_listing_id = match ($this->status_listing_id) {
      160 => 2, // Activo
      161 => 6, // Cancelada
      162 => 3, // Expirado
      167 => 7, // Alquilado
      169 => 3, // Venta aceptada, vendida
      168 => 4, // Oferta reserva
      default => 1,
    };

    // $this->status_listing_id = 2; // Todo en activo

    if ($this->price_type) {
      $priceType = PriceType::firstOrCreate([
        'name' => trim($this->price_type),
      ]);
      $this->price_type_id = $priceType->id;
    } else {
      $this->price_type_id = null;
    }

    // $this->price_type_id = match ($this->price_type_id) {
    //   2620 => 1, // Arriendo Mensual
    //   5525 => 2, // Negociable
    //   2719 => 3, // Ofertas alrededor de
    //   1900 => 4, // Ofertas sobre
    //   2989 => 5, // Precio de Referencia
    //   1901 => 6, // Precio Fijo
    //   2619 => 7, // Precio Solicitado
    //   2007 => 8, // Precios empiezan en
    //   default => null,
    // };
  }

  private function truncateString(?string $value, int $maxLength): ?string
  {
    return $value !== null ? mb_substr($value, 0, $maxLength) : null;
  }
}
