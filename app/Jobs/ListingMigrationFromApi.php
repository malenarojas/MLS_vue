<?php

namespace App\Jobs;

use App\Dtos\Listings\MigrationConfig;
use App\Services\Migrations\ListingMigrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ListingMigrationFromApi implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected MigrationConfig $migrationConfig,
    ) {}

    public function handle(): void
    {
        Log::info('ListingMigrationFromApi job started');
        Log::info('Migration config: ');
        $service = app(ListingMigrationService::class);

        if ($this->migrationConfig->fromDB) {
            $service->prepareMigrationFromDB(
                $this->migrationConfig->migrateAll,
                $this->migrationConfig->migrateFromFirst,
            );
        }
    }
}