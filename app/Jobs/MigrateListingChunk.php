<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Services\Migrations\ListingMigrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Batchable;

class MigrateListingChunk implements ShouldQueue
{
    use Queueable, Batchable;

    public $timeout = 600;

    public function __construct(
        public int $listingId,
        public string $token,
    ) {}

    public function handle(): void
    {
        $listing = Listing::find($this->listingId);

        if (!$listing || !$listing->key) {
            Log::warning("Listing ID {$this->listingId} no válido o sin key");
            return;
        }

        Log::info("Procesando Listing ID {$listing->id}");

        app(ListingMigrationService::class)->processData(
            $listing->MLSID,
            $listing->key,
            true,
            $this->token
        );
    }
}
