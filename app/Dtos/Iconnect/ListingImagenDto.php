<?php

namespace App\Dtos\Iconnect;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ListingImagenDto extends Data
{
  public $multimedia_type_id;
  public function __construct(
    #[MapInputName('WebImageURL')]
    public string $link,
    public ?int $listing_id = null,
    #[Computed]
    public ?int $is_default,
    public bool $Default,
  ) {
    $this->multimedia_type_id = 3;
    $this->is_default = $this->Default ? 1 : 0;
  }
}
