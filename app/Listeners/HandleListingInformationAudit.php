<?php

namespace App\Listeners;

use App\AuditConfigs\ListingInformationAuditConfig;
use App\Events\ListingAuditRequested;
use App\Services\Audit\AuditLogService;
use App\Services\Audit\DefaultAuditFieldResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HandleListingInformationAudit
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
            ListingInformationAuditConfig::foreignKeys(),
            ListingInformationAuditConfig::valueLabels()
        );
        $auditService = new AuditLogService($resolver);

        $dtoData = $event->listingDto->listing_information->all();
        $filtered = array_intersect_key($dtoData, array_flip(ListingInformationAuditConfig::auditableFields()));
        $logs = $auditService->generateLogs($filtered, $event->listing->listing_information, ListingInformationAuditConfig::fieldMap());

        if (!empty($logs)) {
            $event->listing->logs()->createMany($logs);
        }

        // Logs Rooms
        $dtoRooms = collect($event->listingDto->rooms ?? []);
        $modelRooms = $event->listing->listing_information->rooms()->get()->keyBy('id');

        // dd($dtoRooms, $modelRooms);

        $hasRoomChanges = false;

        foreach ($dtoRooms as $dtoRoom) {
            if (!isset($dtoRoom->id)) {
                $hasRoomChanges = true; // habitación nueva
                break;
            }

            $modelRoom = $modelRooms->get($dtoRoom->id);
            if (!$modelRoom) {
                $hasRoomChanges = true; // habitación eliminada
                break;
            }

            // Compara solo los campos relevantes
            $fieldsToCompare = ['description', 'size', 'dimension_x', 'dimension_y', 'room_type_id'];

            foreach ($fieldsToCompare as $field) {
                $oldValue = (string) $modelRoom->{$field};
                $newValue = (string) ($dtoRoom->{$field} ?? null);

                if ($oldValue !== $newValue) {
                    $hasRoomChanges = true;
                    break 2; // salta ambos foreach
                }
            }
        }

        $dtoIds = $dtoRooms->pluck('id')->filter()->values()->all();
        $modelIds = $modelRooms->keys()->all();

        if (count(array_diff($modelIds, $dtoIds)) > 0) {
            $hasRoomChanges = true;
        }

        if ($hasRoomChanges) {
            $event->listing->logs()->create([
                'field_name' => 'Habitaciones',
                'old_value' => null,
                'new_value' => 'Cambios en las habitaciones',
                'notes' => 'Cambios en las habitaciones',
                'user_id' => Auth::id(),
            ]);
        }
    }
}
