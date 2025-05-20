<?php

namespace App\Listeners;

use App\Events\ListingAuditRequested;
use Illuminate\Support\Facades\Auth;

class HandleListingMultimediaAudit
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
        $dtoMultimedias = $event->listingDto->multimedias ?? [];
        $currentMultimedias = $event->listing->multimedias;

        $dtoById = collect($dtoMultimedias)->keyBy('id');
        $modelById = $currentMultimedias->keyBy('id');

        $hasChanges = false;

        if ($dtoById->count() !== $modelById->count()) {
            $hasChanges = true;
        }

        foreach ($dtoById as $id => $dto) {
            if (!$modelById->has($id)) {
                $hasChanges = true;
                break;
            }

            $model = $modelById->get($id);
            if ((int) ($dto['is_default'] ?? 0) !== (int) $model->is_default) {
                $hasChanges = true;
                break;
            }
        }

        if ($hasChanges) {
            $event->listing->logs()->create([
                'field_name' => 'Multimedias',
                'old_value' => null,
                'new_value' => 'Cambios en las imágenes',
                'notes' => 'Cambios en las imágenes',
                'user_id' => Auth::id(),
            ]);
        }
    }
}
