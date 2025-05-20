<?php

namespace App\Jobs;

use App\Dtos\Excels\ListingMigrationDto;
use App\Models\Agent;
use App\Models\ExchangeRate;
use App\Models\Listing;
use App\Models\ListingTransfer;
use App\Models\Office;
use App\Models\Propiedad;
use App\Models\RemaxTitleToShow;
use App\Models\Transaction;
use App\Services\Listings\ListingService;
use App\Services\Transactions\TransactionService;
use App\Utils\StringUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;

class MigratePropiedadesChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Collection $propiedades;

    public function __construct(Collection $propiedades)
    {
        $this->propiedades = $propiedades;
    }

    public function handle()
    {
        $listingService = app(ListingService::class);
        $transactionService = app(TransactionService::class);
        $propertiesArray = $this->propiedades->toArray();
        $mlsIds = array_filter(array_column($propertiesArray, 'MLS_ID'));
        $keys = array_filter(array_column($propertiesArray, 'key'));
        $exchangeRate = ExchangeRate::orderBy('created_at', 'desc')->first();

        $listings = Listing::whereIn('MLSID', $mlsIds)
            ->orWhereIn('key', $keys)
            ->with('transactions')
            ->get()
            ->keyBy(fn($listing) => $listing->MLSID ?? $listing->key);

        $i = 1;

        foreach ($this->propiedades as $propiedad) {
            DB::beginTransaction();

            try {
                if ($propiedad->status === 1) {
                    Log::info(str_repeat("-", 30));
                    Log::info("$i Listing ya fue procesado: MLSID{$propiedad->MLS_ID}");
                    Log::info(str_repeat("-", 30));
                    $i++;
                    DB::commit();
                    continue;
                }

                if ($propiedad->{'Status_de_Captación'} === 'Activa') {
                    Log::info(str_repeat("-", 30));
                    $listing = Listing::where('MLSID', $propiedad->MLS_ID)->first();

                    if ($listing) {
                        Log::info("$i Existe el listing activo {$propiedad->MLS_ID}");
                    } else {
                        Log::info("$i No existe el listing activo {$propiedad->MLS_ID}");
                    }

                    Log::info(str_repeat("-", 30));
                    $i++;
                    DB::commit();
                    continue;
                }

                Log::info("Officina $propiedad->Nombre_Oficina");
                if ($propiedad->Nombre_Oficina === 'RE/MAX Bolivia' || $propiedad->Nombre_Oficina === 'Test Office Bolivia') {
                    Log::info(str_repeat("-", 30));
                    Log::info("$i Listing pertenece a Remax/Bolivia o Test Office Bolivia MLSID{$propiedad->MLS_ID}");
                    Log::info(str_repeat("-", 30));

                    $listing = Listing::where('MLSID', $propiedad->MLS_ID)->with([
                        'transactions',
                        'listing_information.rooms',
                    ])->first();

                    if ($listing) {
                        $listingService->deleteListing($listing);
                    } else {
                        Log::info("Listing de prueba no existe");
                    }
                    DB::commit();
                    continue;
                }

                $dto = ListingMigrationDto::from($propiedad->toArray());
                Log::info("$i Procesando propiedad MLS_ID: {$dto->mlsId}");

                // Insert or Update current listing
                $currentListing = $listings->get($dto->mlsId);

                if (!$currentListing) {
                    $currentListing = Listing::where('MLSID', $dto->mlsId)
                        ->orWhere('key', $dto->key)
                        ->first() ?? new Listing();
                }

                // $listingService->updateFromProperty($currentListing, $dto, $exchangeRate->id);

                // Verificar si es transferencia
                if ($dto->transferedMlsid) {
                    Log::info("→ Listing marcado como TRANSFERIDO desde MLS_ID {$dto->transferedMlsid} hacia MLS_ID {$dto->mlsId}");

                    $agent = Agent::with([
                        'user' => function ($query) use ($dto) {
                            $query->where('name_to_show', $dto->agentName);
                        },
                        'office:id,name',
                    ])->first();

                    $transferredOffice = Office::select('id', 'office_id', 'name')
                        ->where('office_id', $dto->transferredOfficeId)
                        ->first();

                    if (!$transferredOffice) {
                        Log::info("No existe officina (id $dto->transferredOfficeId)");
                    }

                    $listingTransfer = ListingTransfer::updateOrCreate(
                        [
                            'previous_mlsid' => $dto->transferedMlsid,
                            'new_mlsid' => $dto->mlsId,
                            'transferred_at' => $dto->transferedDate,
                        ],
                        [
                            'previous_office_id' => $dto->transferredOfficeId,
                            'previous_office_name' => $transferredOffice?->name,
                            'previous_agent_id' => $dto->transferredAgentId,
                            'previous_agent_name' => $dto->transferedAgentName,
                            'new_office_id' => $dto->officeId,
                            'new_office_name' => $agent?->office?->name,
                            'new_agent_id' => $dto->agentId,
                            'new_agent_name' => $agent?->user?->name_to_show,
                            'new_key' => $dto->key,
                        ]
                    );

                    Log::info("Listings transferido (id: $listingTransfer->id) (NewMlsid: $listingTransfer->new_mlsid) - (NewMlsid: $listingTransfer->previous_mlsid)");

                    $previousListing = $listings->firstWhere('MLSID', $dto->transferedMlsid);

                    if (!$previousListing) {
                        $previousListing = Listing::where('MLSID', $dto->transferedMlsid)
                            ->with('transactions')
                            ->first();
                    }

                    if ($previousListing) {
                        Log::info("→ Transfer listing detectado: MLS_ID {$previousListing->MLSID}");

                        if ($currentListing->exists && $currentListing->id) {
                            Log::info("→ El current listing ya está en el sistema. Transfiriendo transacciones...");

                            // Actualizar las transferidad
                            if ($agent->agent_internal_id) {
                                $transactionService->transferListingTransaction($previousListing, $currentListing, $agent->agent_internal_id);
                            }

                            // Eliminar listing anterior, ya tiene log y se tranfirieron las transactions
                            $listingService->deleteListing($previousListing);

                            $listingService->updateFromProperty($currentListing, $dto, $exchangeRate->id);
                        } else {
                            Log::info("→ El current listing NO está en el sistema. Solo actualizando referencias.");

                            $transactionService->updateAgentIdInPaymentsAndCommission($previousListing->id, $agent->agent_internal_id);
                            $transactionService->updateMlsid($previousListing->id, $dto);
                            $listingService->updateFromProperty($previousListing, $dto, $exchangeRate->id);
                        }
                    } else {
                        Log::warning("⚠️ No se encontró el Listing anterior transferido MLS_ID {$dto->transferedMlsid}.");
                        $listingService->updateFromProperty($currentListing, $dto, $exchangeRate->id);
                    }
                } else {
                    Log::info("→ Listing sin transferencia. Insertando o actualizando directamente MLS_ID {$dto->mlsId}");
                    $listingService->updateFromProperty($currentListing, $dto, $exchangeRate->id);
                }

                $propiedad->status = 1;
                $propiedad->save();

                Log::info(str_repeat("-", 30));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error("❌ Error procesando propiedad MLS_ID: {$propiedad->MLS_ID} -> " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);

                $propiedad->status = 2;
                $propiedad->save();
            }

            $i++;
        }
    }

    /*
    public function handle()
    {
        $listingService = app(ListingService::class);
        $propertiesArray = $this->propiedades->toArray();
        $mlsIds = array_filter(array_column($propertiesArray, 'MLS_ID'));
        $keys = array_filter(array_column($propertiesArray, 'key'));
        $exchangeRate = ExchangeRate::orderBy('created_at', 'desc')->first();

        $listings = Listing::whereIn('MLSID', $mlsIds)
            ->orWhereIn('key', $keys)
            ->get()
            ->keyBy(function ($listing) {
                return $listing->MLSID ?? $listing->key;
            });

        $i = 1;

        foreach ($this->propiedades as $propiedad) {
            DB::beginTransaction();

            try {
                if ($propiedad->status === 1) {
                    Log::info(str_repeat("-", 30));
                    Log::info("$i Listing ya fue procesado: MLSID{$propiedad->MLS_ID}");
                    Log::info(str_repeat("-", 30));
                    continue;
                }

                if ($propiedad->{'Status_de_Captación'} === 'Activa') {
                    Log::info("$i Ignorar porque esta en estado activo MLSID{$propiedad->MLS_ID}");
                    continue;
                }

                $dto = ListingMigrationDto::from($propiedad->toArray());

                Log::info("$i Procesando propiedad MLS_ID: {$dto->mlsId}");

                $listing = $listings->get($dto->mlsId) ?? $listings->get($dto->key);

                if ($listing) {
                    Log::info("→ Listing existente encontrado: MLS_ID {$listing->MLSID} (ID {$listing->id}). Se actualizarán datos.");
                    $listingService->updateFromProperty($listing, $dto, $exchangeRate->id);
                } else {
                    Log::info("→ Listing NO encontrado: MLS_ID {$dto->mlsId}. Se creará un nuevo Listing.");
                    $listing = new Listing();
                    $listingService->updateFromProperty($listing, $dto, $exchangeRate->id);
                }

                if ($dto->transferedMlsid) {
                    Log::info("→ Listing marcado como TRANSFERIDO desde MLS_ID {$dto->transferedMlsid} hacia MLS_ID {$dto->mlsId}");

                    $transfer = ListingTransfer::updateOrCreate(
                        [
                            'previous_mlsid' => $dto->transferedMlsid,
                            'new_mlsid' => $dto->mlsId,
                            'transferred_at' => $dto->transferedDate,
                        ],
                        [
                            'previous_mlsid' => $dto->transferedMlsid,
                            'previous_agent_id' => $dto->transferedAgentName,
                            'new_mlsid' => $dto->mlsId,
                            'new_agent_id' => StringUtil::getFirstPart($dto->mlsId),
                            'transferred_at' => $dto->transferedDate,
                        ]
                    );

                    if ($transfer->wasRecentlyCreated) {
                        Log::info("→ Registro de transferencia creado correctamente.");
                    } else {
                        Log::info("→ Registro de transferencia actualizado.");
                    }

                    $previousListing = Listing::where('MLSID', $dto->transferedMlsid)
                        ->with('transactions')
                        ->first();

                    if ($previousListing) {
                        Log::info("→ Actualizar listring trasferido {$previousListing->MLSID} (ID {$previousListing->id}) (status: {$previousListing->listing_status_id}) .");

                        $currentListing = Listing::where('MLSID', $dto->transferedMlsid)
                            ->with('transactions')
                            ->first();

                        if ($currentListing) {
                        } else {
                        }
                    } else {
                        Log::warning("⚠️ No se encontró el Listing anterior transferido MLS_ID {$dto->transferedMlsid} para eliminarlo.");
                    }
                }

                // Marcamos como procesado exitosamente
                $propiedad->status = 1;
                $propiedad->save();

                DB::commit();
                Log::info(str_repeat("-", 30));
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error("❌ Error procesando propiedad MLS_ID: {$propiedad->MLS_ID} -> " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);

                // Marcamos como error
                $propiedad->status = 2;
                $propiedad->save();
            }
            $i++;
        }
    }
    */
}
