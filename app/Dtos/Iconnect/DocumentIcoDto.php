<?php

namespace App\Dtos\Iconnect;

use App\Models\DocumentationType;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class DocumentIcoDto extends Data
{
  public function __construct(
    #[Computed]
    public ?int $documentation_type_id,
    #[MapInputName('DocumentType')]
    public ?string $documentation_type,
    #[MapInputName('DocumentUrl')]
    public ?string $link,
    #[MapInputName('DocumentDescription')]
    public ?string $description,
  ) {
    $documentation_type = DocumentationType::firstOrCreate(['name' => $documentation_type]);
    $this->documentation_type_id = $documentation_type->id;
  }
}
