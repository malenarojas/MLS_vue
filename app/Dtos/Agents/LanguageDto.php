<?php

namespace App\Dtos\Agents;

use Spatie\LaravelData\Data;

class LanguageDto extends Data
{  public function __construct(
    public ?int $id,
    public string $name_languages,
    public ?string $created_at = null,
    public ?string $updated_at = null
) {}

public function toArray(): array
{
    return get_object_vars($this);
}
}
