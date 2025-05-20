<?php

namespace App\Dtos\Iconnect;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class OwnerIcoDto extends Data
{
  public function __construct(
    #[MapInputName('OwnerName')]
    public ?string $name,
    #[MapInputName('OwnerEmail')]
    public ?string $email,
    #[MapInputName('OwnerPhone')]
    public ?string $mobile,
  ) {}
}
