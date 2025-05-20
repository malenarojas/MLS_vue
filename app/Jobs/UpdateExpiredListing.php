<?php

namespace App\Jobs;

use App\Services\Listings\ListingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateExpiredListing implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = app(ListingService::class);
        $service->updateExpireListing();
    }
}
