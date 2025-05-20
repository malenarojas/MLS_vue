<?php

namespace App\Listeners;

use App\AuditConfigs\CommissionOptionAuditConfig;
use App\AuditConfigs\LocationAuditConfig;
use App\Events\ListingAuditRequested;
use App\Services\Audit\AuditLogService;
use App\Services\Audit\DefaultAuditFieldResolver;
use Illuminate\Support\Facades\Log;

class HandleCommissionOptionAudit
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
        $model = $event->listing->commission_option;
        $dtoData = $event->listingDto->commission_option?->all() ?? [];
        if (!$model || empty($dtoData)) return;

        $resolver = new DefaultAuditFieldResolver(
            CommissionOptionAuditConfig::foreignKeys(),
            CommissionOptionAuditConfig::valueLabels()
        );

        $service = new AuditLogService($resolver);

        $filtered = array_intersect_key($dtoData, array_flip(CommissionOptionAuditConfig::auditableFields()));

        $logs = $service->generateLogs($filtered, $model, CommissionOptionAuditConfig::fieldMap());

        if (!empty($logs)) {
            $event->listing->logs()->createMany($logs);
        }
    }
}
