<?php

namespace App\Dtos\Iconnect;

use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

// Pasar PriceDetails
class PriceIcoDto extends Data
{
  public $listing_id;
  public $price_usd;
  public $sold_price_usd;
  public $sold_price_euro;
  public $sold_price_cnd;

  public function __construct(
    #[MapInputName('ListingPrice')]
    public ?string $price,
    #[MapInputName('ListingPriceEuro')]
    public ?string $price_euro,
    #[MapInputName('ListingCurrency')]
    public ?string $currency,
    #[MapInputName('SoldPrice')]
    public ?string $sold_price,
    #[MapInputName('SoldPriceCurrency')]
    public ?string $sold_price_currency,
    #[Computed]
    public ?int $currency_id,
  ) {
    $this->price = $this->price !== null ? (float) $this->price : null;
    $this->sold_price = $this->sold_price !== null ? (float) $this->sold_price : null;

    $this->currency_id = 1; // Todos en BOB

    // Precio de captacion, convertir a BOB
    $this->price = match ($this->currency) {
      'USD' => $this->price * 6.96,
      'EUR' => $this->price * 9,
      'CND' => $this->price, // No convertir
      default => $this->price
    };

    /*
      Precio de venta, convertirt a BOB
    */
    if ($this->sold_price_currency === 'USD') {
      $this->sold_price_usd = $this->sold_price;
      $this->sold_price = $this->sold_price * 6.96;
    } else if ($this->sold_price_currency === 'EUR') {
      $this->sold_price_euro = $this->sold_price;
      $this->sold_price = $this->sold_price * 9;
    } else if ($this->sold_price_currency === 'CND') {
      // Excepcion
      $this->sold_price_usd = $this->sold_price;
      $this->sold_price = $this->sold_price;
    }
  }
}
