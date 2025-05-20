<?php

namespace App\Listeners;

use App\AuditConfigs\ListingAuditConfig;
use App\Events\ListingAuditRequested;
use App\Services\Audit\AuditLogService;
use App\Services\Audit\DefaultAuditFieldResolver;
use Illuminate\Support\Facades\Log;

class HandleListingAudit
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
        $resolver = new DefaultAuditFieldResolver(
            ListingAuditConfig::foreignKeys(),
            ListingAuditConfig::valueLabels(),
        );
        $auditService = new AuditLogService($resolver);

        $dtoData = $event->listingDto->all();
        $filtered = array_intersect_key($dtoData, array_flip(ListingAuditConfig::auditableFields()));

        $logs = $auditService->generateLogs($filtered, $event->listing, ListingAuditConfig::fieldMap());

        if (!empty($logs)) {
            $event->listing->logs()->createMany($logs);
        }
    }
}
