<?php

namespace App\Listeners;

use App\AuditConfigs\ListingInformationAuditConfig;
use App\AuditConfigs\LocationAuditConfig;
use App\Events\ListingAuditRequested;
use App\Services\Audit\AuditLogService;
use App\Services\Audit\DefaultAuditFieldResolver;
use Illuminate\Support\Facades\Log;

class HandleLocationAudit
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
        $dtoData = $event->listingDto->location->all();
        $location = $event->listing?->location;
        if (!$location || !$dtoData) {
            return;
        }

        $resolver = new DefaultAuditFieldResolver(
            LocationAuditConfig::foreignKeys(),
            LocationAuditConfig::valueLabels()
        );
        $auditService = new AuditLogService($resolver);

        $filtered = array_intersect_key($dtoData, array_flip(LocationAuditConfig::auditableFields()));
        $logs = $auditService->generateLogs($filtered, $event->listing->location, LocationAuditConfig::fieldMap());

        if (!empty($logs)) {
            $event->listing->logs()->createMany($logs);
        }
    }
}
