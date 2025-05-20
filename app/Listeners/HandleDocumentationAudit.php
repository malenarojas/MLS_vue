<?php

namespace App\Listeners;

use App\Events\ListingAuditRequested;
use Illuminate\Support\Facades\Auth;

class HandleDocumentationAudit
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
        $dtoPublic = collect($event->listingDto->public_documentation ?? [])->pluck('id')->map(fn($id) => (int) $id);
        $dtoPrivate = collect($event->listingDto->private_documentation ?? [])->pluck('id')->map(fn($id) => (int) $id);
        $dtoDocumentationIds = $dtoPublic->merge($dtoPrivate)->unique()->sort()->values();

        $modelDocumentationIds = $event->listing->documentation->pluck('id')->map(fn($id) => (int) $id)->sort()->values();

        if (
            $dtoDocumentationIds->count() !== $modelDocumentationIds->count() ||
            !$dtoDocumentationIds->values()->all() === $modelDocumentationIds->values()->all()
        ) {
            $event->listing->logs()->create([
                'field_name' => 'Documentación',
                'old_value' => null,
                'new_value' => 'Cambios en la documentación',
                'notes' => 'Cambios en la documentación',
                'user_id' => Auth::id(),
            ]);
        }
    }
}
