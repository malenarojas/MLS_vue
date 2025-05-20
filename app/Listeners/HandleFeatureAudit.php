<?php

namespace App\Listeners;

use App\Events\ListingAuditRequested;
use Illuminate\Support\Facades\Auth;

class HandleFeatureAudit
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
        $dtoFeatureIds = collect($event->listingDto->features ?? [])->map(fn($id) => (int) $id)->sort()->values();
        $modelFeatureIds = $event->listing->features->pluck('id')->map(fn($id) => (int) $id)->sort()->values();

        if (
            $dtoFeatureIds->count() !== $modelFeatureIds->count() ||
            !$dtoFeatureIds->values()->all() === $modelFeatureIds->values()->all()
        ) {
            $event->listing->logs()->create([
                'field_name' => 'Características',
                'old_value' => null,
                'new_value' => 'Cambios en las características',
                'notes' => 'Cambios en las características',
                'user_id' => Auth::id(),
            ]);
        }
    }
}
