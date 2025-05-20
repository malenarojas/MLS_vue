<?php

namespace App\Dtos\Iconnect;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class DescriptionIcoDto extends Data
{
  public function __construct(
    #[MapInputName('LanguageCode')]
    public ?string $languageCode,
    #[MapInputName('Description')]
    public ?string $description,
    #[MapInputName('TypeOfDescription')]
    public ?string $descriptionType = null,
  ) {}
}
