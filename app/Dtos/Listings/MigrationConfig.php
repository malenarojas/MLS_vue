<?php

namespace App\Dtos\Listings;

use Spatie\LaravelData\Data;

class MigrationConfig extends Data
{
  public function __construct(
    public bool $fromDB = false,
    public bool $fromExcel = false,
    public ?string $filePath = null,
    public bool $activar = false,
    public bool $migrateAll = false, // Por defecto ignora los que estan bien
    public bool $migrateFromFirst = false,
  ) {}
}
