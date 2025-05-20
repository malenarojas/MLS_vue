<?php

namespace App\Listeners;

use App\AuditConfigs\ListingPriceAuditConfig;
use App\Events\ListingAuditRequested;
use App\Services\Audit\AuditLogService;
use App\Services\Audit\DefaultAuditFieldResolver;
use Illuminate\Support\Facades\Log;

class HandleListingPriceAudit
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ListingAuditRequested $event): void
    {
        $price = $event->listing?->listing_prices()?->first();

        if (!$event->listingDto->amount || !$price) {
            return;
        }

        $resolver = new DefaultAuditFieldResolver(
            ListingPriceAuditConfig::foreignKeys(),
            ListingPriceAuditConfig::valueLabels()
        );
        $auditService = new AuditLogService($resolver);

        $logs = $auditService->generateLogs(
            ['amount' => $event->listingDto->amount],
            $price,
            ListingPriceAuditConfig::fieldMap()
        );

        if (!empty($logs)) {
            $event->listing->logs()->createMany($logs);
        }
    }
}
