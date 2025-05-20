<?php

namespace App\Services\Listings;

use App\Constants\ListingStatusId;
use App\Dtos\Excels\ListingMigrationDto;
use App\Dtos\Listings\ListingDto;
use App\Dtos\Listings\ListingEditParamDto;
use App\Dtos\Listings\LocationDto;
use App\Events\ListingAuditRequested;
use App\Http\Requests\ListingController\ParamsListing;
use App\Models\Agent;
use App\Models\Buyer;
use App\Models\CancellationReason;
use App\Models\City;
use App\Models\Commission;
use App\Models\Contact;
use App\Models\ContractType;
use App\Models\DocumentationType;
use App\Models\ExchangeRate;
use App\Models\LandCategory;
use App\Models\LandUse;
use App\Models\Language;
use App\Models\Listing;
use App\Models\MarketStatus;
use App\Models\Multimedia;
use App\Models\Payment;
use App\Models\PriceType;
use App\Models\PropertyCategory;
use App\Models\Propiedad;
use App\Models\RemaxTitleToShow;
use App\Models\RentTimeframe;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\State;
use App\Models\StateProperty;
use App\Models\StatusListing;
use App\Models\SubtypeProperty;
use App\Models\TypeFloor;
use App\Models\Transaction;
use App\Models\Zone;
use App\Services\Agents\AgentService;
use App\Services\Agents\OfficeService;
use App\Services\Agents\RegionService;
use App\Services\Agents\AreaService;
use App\Services\ImageService;
use App\Services\Listings\ContractTypeService;
use App\Services\Listings\ListingTransactionTypeService;
use App\Services\Listings\StatusListingService;
use App\Services\Listings\SubtypePropertyService;
use App\Traits\AutenticationTrait;
use App\Traits\FormatsListingData;
use App\Traits\GenerateMlsid;
use App\Traits\HasLocationData;
use App\Utils\StringGenerateKey;
use App\Utils\StringUtil;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ListingService
{
    use FormatsListingData, HasLocationData, AutenticationTrait, GenerateMlsid;

    const LISTING_STATUS_DRAFT = 1;
    const LISTING_STATUS_ACTIVE = 2;
    const LISTING_STATUS_REVIEW = 9;
    const LISTING_STATUS_REJECT = 10;

    public function __construct(
        private AgentService $agentService,
        private AreaService $areaService,
        private ContractTypeService $contractTypeService,
        private FeatureService $featureService,
        private ListingTransactionTypeService $listingTransactionTypeService,
        private OfficeService $officeService,
        private StatusListingService $statusListingService,
        private SubtypePropertyService $subtypePropertyService,
        private RegionService $regionService,
        private RoomService $roomService,
        private ImageService $imageService,
        private DocumentationService $documentationService,
        private DocumentationTypeService $documentationTypeService,
    ) {}

    public function getIndexData(ParamsListing $request): array
    {
        $user = $this->getAuthenticate();
        $filters = $request->validated();  // Filtros de listings
        $office_id = $filters['office_id'] ?? null;

        if ($user->hasDirectPermission('listing.show_offices')) {
            // Si el usuario tiene permisos para ver todas las oficinas, se muestra el filtro de oficinas
            $offices = $this->officeService->getAll();
            $agents = $office_id ? $this->agentService->filterAgents(compact('office_id')) : [];
        } else {
            // Si el usuario no tiene permisos para ver todas las oficinas, se filtra por la oficina a la que pertenece
            $office_id = $user->agent?->office_id;
            $filters['office_id'] = $office_id;
            // if (!isset($filters['agent_id'])) {
            //     $filters['agent_id'] = $user->agent?->id;
            // }
            $offices = [];
        }

        $agents = [];
        if ($user->hasDirectPermission('listing.show_agents')) {
            // Filtrar agentes por oficina
            if ($office_id) {
                $agents = $this->agentService->filterAgents(compact('office_id'));
            }
        } else {
            // Solo los listings del agente autenticado
            $filters['agent_id'] = $user->agent?->id;
        }

        Log::info('Listings filters', $filters);

        return [
            'filters' => $filters,
            'listings' => $this->getAllWithPagination($filters),
            'offices' => $offices,
            'agents' => $agents,
            'areas' => $this->areaService->getAllBase(),
            'transaction_types' => $this->listingTransactionTypeService->getAll(),
            'subtype_properties' => SubtypeProperty::all(),
            'status_listings' => $this->statusListingService->getAll(),
        ];
    }

    public function getEditData(string $key, ?LocationDto $locationDto, ?ListingEditParamDto $paramDto): array
    {
        $user = $this->getAuthenticate();
        $listing = $this->findByKey($key);

        $availableStatus = [];
        if ($user->hasDirectPermission('listing.show_status')) {
            $availableStatus = StatusListing::select('id', 'name')->get();
        } else {
            $availableStatus = StatusListing::where('id', $listing->status_listing_id)->get();
        }

        $location = $listing->location;
        if ($locationDto->state_id === null && $locationDto->province_id === null && $locationDto->city_id === null) {
            // No ha modificado la ubicación, se mantiene la ubicación actual
            $province = $location?->city?->province;

            if ($province !== null) {
                $state_id = $province->state_id;
                $province_id = $province->id;

                $locationDto = LocationDto::from([
                    'state_id' => $state_id,
                    'province_id' => $province_id,
                    'city_id' => $listing->location?->city_id,
                    'zone_id' => $listing->location?->zone_id,
                ]);

                $location->state_id = $state_id;
                $location->province_id = $province_id;
            }
        }
        $locationData = $this->getLocationData($locationDto);

        $contacts = collect(); // Inicializar lista vacía
        if (!empty($paramDto?->contact_search)) {
            $agent_id = Agent::where('user_id', $user->id)->value('id');
            $contactSearch = $paramDto->contact_search;

            $contacts = Contact::select(
                'id',
                'name',
                'last_name',
                'email',
                'mobile',
                DB::raw("CONCAT(name, ' ', last_name) AS full_name")
            )
                ->where('agent_id', $agent_id) // Solo contactos del agente o broker
                ->where(function ($query) use ($contactSearch) {
                    $query->where('name', 'like', "%{$contactSearch}%")
                        ->orWhere('last_name', 'like', "%{$contactSearch}%")
                        ->orWhere(DB::raw("CONCAT(name, ' ', last_name)"), 'like', "%{$contactSearch}%")
                        ->orWhere('email', 'like', "%{$contactSearch}%")
                        ->orWhere('mobile', 'like', "%{$contactSearch}%");
                })
                ->orderBy('name')
                ->orderBy('last_name')
                ->limit(20)
                ->get();
        }

        return [
            'listing' => $this->formatListingData($listing),
            'feature_ids' => $listing->features->pluck('id')->toArray(),
            // Location
            'states' => State::select('id', 'name')->get(),
            'provinces' => $locationData['provinces'],
            'cities' => $locationData['cities'],
            'zones' => $locationData['zones'],
            'areas' => $this->areaService->getAllBase(),
            'contract_types' => ContractType::select('id', 'name')->get(),
            'features' => $this->featureService->getAll(),
            'land_categories' => LandCategory::select('id', 'land_category_name')->get(),
            'land_uses' => LandUse::select('id', 'land_use_name')->get(),
            'market_statuses' => MarketStatus::select('id', 'name_market_status')->get(),
            'price_types' => PriceType::select('id', 'name')->get(),
            'property_categories' => PropertyCategory::select('id', 'name_properties_categories')->get(),
            'transaction_type' => $this->listingTransactionTypeService->getAll(),
            'available_status' => $availableStatus,
            // 'status_listings' => $this->statusListingService->getAll(),
            'price_types' => PriceType::select('id', 'name')->get(),
            'subtype_properties' => SubtypeProperty::all(),
            'offices' => $this->officeService->getAll(),
            'features' => $this->featureService->getAll(),
            'languages' => Language::select('id', 'code', 'name', 'is_default')->get(),
            'room_types' => RoomType::select('id', 'name')->get(),
            'rooms' => $this->roomService->getAll(['listing_id' => $listing->id]),
            'state_properties' => StateProperty::select('id', 'name_state_properties')->get(),
            'status_listings' => $availableStatus,
            'subtype_properties' => SubtypeProperty::all(),
            'transaction_type' => $this->listingTransactionTypeService->getAll(),
            'type_floors' => TypeFloor::select('id', 'name')->get(),
            'private_documents' => $this->documentationTypeService->getAllPrivate(),
            'public_documents' => $this->documentationTypeService->getAllPublic(),
            'contacts' => $contacts ?? [],
            'rent_timeframes' => RentTimeframe::select('id', 'name')->get(),
            'cancellation_reasons' => CancellationReason::select('id', 'name')->get(),
        ];
    }

    public function updateListing(string $key, ListingDto $dto, bool $isDraft = false): Listing
    {
        DB::beginTransaction();

        try {
            // $user = $this->getAuthenticate();
            $listing = Listing::where('key', $key)->firstOrFail();

            if (!$isDraft) {
                event(new ListingAuditRequested($listing, $dto));
            }

            // if (!$user->hasDirectPermission('listing.show_status') && !$isDraft) {
            //     $dto->status_listing_id = self::LISTING_STATUS_REVIEW;
            // }

            // if ($dto->is_draft) {
            //     $dto->status_listing_id = self::LISTING_STATUS_DRAFT;
            // } else {
            // }

            $listing->update($dto->toArray());

            if ($dto->amount) {
                // Get last exchange rate
                $exchangeRate = ExchangeRate::orderBy('created_at', 'desc')->first();

                $listing->listing_prices()->updateOrCreate(
                    ['listing_id' => $listing->id],
                    [
                        'amount' => $dto->amount,
                        'currency_id' => 1, // Always BOB
                        'exchange_rate_id' => $exchangeRate->id ?? null,
                    ]
                );
            }

            if (isset($dto->commission_option)) {
                // dd($dto->commission_option);
                $listing->commission_option()->updateOrCreate(
                    ['listing_id' => $listing->id],
                    $dto->commission_option->toArray()
                );
            }

            if ($dto->listing_information) {
                $listing->listing_information()->updateOrCreate(
                    ['listing_id' => $listing->id],
                    $dto->listing_information->toArray()
                );

                $newRooms = [];

                foreach ($dto->rooms as $roomDto) {
                    if (isset($roomDto->id)) {
                        $listing->listing_information->rooms()->where('id', $roomDto->id)->update($roomDto->toArray());
                        $newRooms[] = $roomDto->id;
                    } else {
                        $room =  $listing->listing_information->rooms()->create($roomDto->toArray());
                        $newRooms[] = $room->id;
                    }
                }

                // $listing->listing_information->rooms()->whereNotIn('id', $newRooms)->delete();

                // dd($listing->listing_information->rooms()->whereNotIn('id', $newRooms)->get());
                $roomsToDelete = $listing->listing_information->rooms()
                    ->whereNotIn('id', $newRooms)
                    ->pluck('id')
                    ->toArray();

                if (!empty($roomsToDelete)) {
                    // $updated = DB::table('multimedias')
                    //     ->whereIn('room_id', $roomsToDelete)
                    //     ->update(['room_id' => null]);

                    // if ($updated === 0) {
                    //     Log::warning('No se actualizaron registros. Verifica si room_id acepta null.');
                    // } else {
                    //     Log::info('Se actualizaron ' . $updated . ' registros.');
                    // }

                    // dd($updated);

                    // $multimedias = Multimedia::whereIn('room_id', $roomsToDelete)->get();
                    // foreach ($multimedias as $multimedia) {
                    //     $multimedia->room_id = null;
                    //     $multimedia->save();
                    // }

                    $listing->listing_information->rooms()->whereIn('id', $roomsToDelete)->delete();
                    // Room::whereIn('id', $roomsToDelete)->delete();
                }
            }

            if ($dto->features) {
                $listing->features()->sync($dto->features);
            }

            if ($dto->location) {
                $dto->location->listing_id = $listing->id;

                $listing->location()->updateOrCreate(
                    ['listing_id' => $listing->id],
                    $dto->location->toArray()
                );
            }

            if ($dto->translations) {
                $data = $dto->translations;
                $languages = Language::all()->where('is_default', 0)->pluck('code')->toArray();

                $translatableFields = $listing->getTranslatableFields();
                $translations = $listing->translations ?? [];

                // // Actualizar las traducciones
                foreach ($languages as $language) {
                    foreach ($translatableFields as $field) {
                        if (isset($data[$language][$field])) {
                            $translations[$language][$field] = $data[$language][$field];
                        }
                    }
                }
            }

            $newMultimedias = [];
            $multimediasCount = $listing->multimedias()->count();
            $isInitDefault = false;
            // dd($multimediasCount);

            // Procesar imágenes enviadas
            foreach ($dto->multimedias as $multimediaDto) {
                $roomId = Arr::get($multimediaDto, 'room_id');
                $multimediaDto['room_id'] = $roomId && Room::find($roomId) ? $roomId : null;

                if (!isset($multimediaDto['is_new'])) {
                    // Actualizar imagen existente
                    $listing->multimedias()->where('id', $multimediaDto['id'])->update($multimediaDto);
                    $newMultimedias[] = $multimediaDto['id'];
                } else {
                    // Crear imagen nueva
                    $link = $this->imageService->uploadImageFromFile($multimediaDto['file'], "listings/{$listing->key}");
                    // Redimensionar imagenes
                    $this->imageService->resizeImage($link, 125, 83, "listings/{$listing->key}/ThumbnaiImageURL");
                    $this->imageService->resizeImage($link, 1000, 667, "listings/{$listing->key}/LargeImageURL");
                    $this->imageService->resizeImage($link, 2000, 1333, "listings/{$listing->key}/ExtraLargeImageURL");
                    $this->imageService->resizeImage($link, 500, 333, null);

                    $multimediaDto['id'] = null;
                    $multimediaDto['link'] = $link;
                    if (!$isInitDefault && $multimediasCount === 0) {
                        $multimediaDto['is_default'] = 1;
                        $isInitDefault = true;
                    }
                    $newMultimedias[] = $listing->multimedias()->create($multimediaDto)->id;
                }
            }

            // Obtener las imágenes que se eliminarán antes de borrarlas
            $deletedMultimedias = $listing->multimedias()
                ->whereNotIn('id', $newMultimedias)
                ->get(['id', 'link']);

            // Eliminar físicamente las imágenes
            foreach ($deletedMultimedias as $deletedMultimedia) {
                $this->imageService->deleteImage($deletedMultimedia->link);

                $pathInfo = pathinfo($deletedMultimedia->link);
                $folder = $pathInfo['dirname'];  // "listings/{key}"
                $fileName = $pathInfo['basename'];

                foreach (Multimedia::TYPE_IMAGEN as $typeImage) {
                    $newPath = $folder . '/' . $typeImage . '/' . $fileName;
                    $this->imageService->deleteImage($newPath);
                }
            }

            // Eliminar de la base de datos
            $listing->multimedias()->whereNotIn('id', $newMultimedias)->delete();

            // Agregar documentación
            $this->documentationService->create($dto->private_documentation ?? [], $listing, true);
            $this->documentationService->create($dto->public_documentation ?? [], $listing, false);

            $owners = $dto->owners ?? [];
            $contactIds = [];

            // Agregar owners
            foreach ($owners as $owner) {
                if (isset($owner['id'])) {
                    // Es un contacto existente, agregamos su ID
                    $contactIds[] = $owner['id'];
                } else {
                    // Es un contacto nuevo, lo registramos primero
                    $newContact = Contact::create([
                        'name' => $owner['name'] ?? 'No especificado',
                        'last_name' => $owner['last_name'] ?? 'No especificado',
                        'email' => $owner['email'] ?? null,
                        'mobile' => $owner['mobile'] ?? null,
                    ]);
                    $contactIds[] = $newContact->id;
                }
            }

            // Sincronizar contactos con el listing (esto eliminará los que no fueron enviados)
            $listing->owners()->sync($contactIds);

            DB::commit();
            return $listing;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function dataFilter()
    {
        return [
            'offices' => $this->officeService->getAll(),
            'status_listings' => $this->statusListingService->getAll(),
        ];
    }

    public function cloneListing(string $key, array $data)
    {
        DB::beginTransaction();
        try {
            $listing = Listing::where('key', $key)->with([
                'listing_information' => function ($query) {
                    $query->with([
                        'rooms',
                    ]);
                },
                'multimedias',
                'location',
                'features',
                'agents' => function ($query) {
                    $query->select('agents.id', 'agents.office_id', 'agents.user_id')
                        ->with([
                            'office' => function ($query) {
                                $query->select('id', 'office_id', 'name');
                            },
                            'user' => function ($query) {
                                $query->select('id', 'name_to_show');
                            }
                        ]);
                },
                'documentation',
                'owners',
                'listing_prices'
            ])->first();

            $newListing = $listing->replicate();
            $agent = $listing->agents()->first();

            if (!$agent) {
                throw new \Exception('El listing no tiene un agente asociado.');
            }

            $agent_id = $agent->agent_internal_id ?? null;

            $newListing->key = StringGenerateKey::generateKey();
            $newListing->MLSID = $this->getNextMLSIDByAgentInternalId($agent_id);
            $newListing->status_listing_id = self::LISTING_STATUS_DRAFT;
            $newListing->transaction_type_id = $data['transaction_type_id'] ?? null;
            $newListing->save();

            if ($agent_id) {
                DB::table('agent_listing')->insert([
                    'agent_id' => $agent->id,
                    'listing_id' => $newListing->id,
                ]);
            }

            $listingPrices = $listing->listing_prices()->first();
            if ($listingPrices) {
                $newListingPrices = $listingPrices->replicate();
                $newListingPrices->listing_id = $newListing->id;
                $newListingPrices->save();
            }

            $listingInformation = $listing->listing_information()->first();
            if ($listingInformation) {
                $newListingInformation = $listingInformation->replicate();
                $newListingInformation->listing_id = $newListing->id;

                foreach ($listingInformation->rooms as $room) {
                    $newRoom = $room->replicate();
                    $newRoom->information_id = $newListingInformation->id;
                    $newRoom->save();
                }

                $newListingInformation->save();
            }

            foreach ($listing->multimedias as $multimedia) {
                $newMultimedia = $multimedia->replicate();
                $newMultimedia->listing_id = $newListing->id;
                $newMultimedia->save();
            }

            if ($listing->location) {
                $newLocation = $listing->location->replicate();
                $newLocation->listing_id = $newListing->id;
                $newLocation->save();
            }

            if ($listing->features) {
                foreach ($listing->features as $feature) {
                    DB::table('feature_listing')->insert([
                        'feature_id' => $feature->id,
                        'listing_id' => $newListing->id,
                    ]);
                }
            }

            if ($listing->documentation) {
                foreach ($listing->documentation as $doc) {
                    $newDoc = $doc->replicate();
                    $newDoc->save();

                    DB::table('documentation_listing')->insert([
                        'documentation_id' => $newDoc->id,
                        'listing_id' => $newListing->id,
                    ]);
                }
            }


            if ($listing->owners) {
                foreach ($listing->owners as $owner) {
                    DB::table('owner')->insert([
                        'listing_id' => $newListing->id,
                        'contact_id' => $owner->id,
                    ]);
                }
            }
            DB::commit();
            return $newListing;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al clonar el listing: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAllWithPagination(array $data, int $perPage = 20)
    {
        $listings = Listing::query()
            ->select([
                'listings.id',
                'listings.key',
                'listings.MLSID',
                'listings.date_of_listing',
                'listings.contract_end_date',
                'listings.created_at',
                'listings.transaction_type_id',
                'listings.status_listing_id',
                'listings.area_id',
                'listings.contract_type_id',
                'listing_prices.amount as price',
                'currencies.symbol as currency_symbol',
                'listings_information.number_bedrooms as bedrooms',
                'listings_information.number_bathrooms as bathrooms',
                'subtype_properties.name as subtype_property_name',
                'listing_transaction_types.name as transaction_type_name',
                'status_listings.id as status_id',
                'status_listings.name as status_name',
                'areas.name as area_name',
                'offices.name as office_name',
                'users.name_to_show as agent_name',
                'cities.name as city_name',
                'zones.name as zone_name',
                'provinces.name as province_name',
                'contract_types.name as contract_type_name',
                'multimedias.link as default_image_link',
            ])
            ->leftJoin('listings_information', 'listings_information.listing_id', '=', 'listings.id')
            ->leftJoin('listing_transaction_types', 'listing_transaction_types.id', '=', 'listings.transaction_type_id')
            ->join('status_listings', 'status_listings.id', '=', 'listings.status_listing_id')
            ->join('areas', 'areas.id', '=', 'listings.area_id')
            ->leftJoin('listing_prices', function ($join) {
                $join->on('listing_prices.listing_id', '=', 'listings.id')
                    ->where('currency_id', 1);
            })
            ->leftJoin('currencies', 'currencies.id', '=', 'listing_prices.currency_id')
            ->leftJoin('subtype_properties', 'subtype_properties.id', '=', 'listings_information.subtype_property_id')
            ->leftJoin('contract_types', 'contract_types.id', '=', 'listings.contract_type_id')
            ->leftJoin('agent_listing', function ($join) {
                $join->on('agent_listing.listing_id', '=', 'listings.id')
                    ->where('agent_listing.type', 1);
            })
            ->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->leftJoin('users', 'users.id', '=', 'agents.user_id')
            ->leftJoin('offices', 'offices.id', '=', 'agents.office_id')
            ->leftJoin('locations', 'locations.listing_id', '=', 'listings.id')
            ->leftJoin('zones', 'zones.id', '=', 'locations.zone_id')
            ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id')
            ->leftJoin('multimedias', function ($join) {
                $join->on('multimedias.listing_id', '=', 'listings.id')
                    ->where('multimedias.is_default', 1);
            })
            ->with([
                'default_imagen:id,listing_id,link'
            ])
            ->when(
                isset($data['agent_id']),
                fn($q) =>
                $q->where('agents.id', $data['agent_id'])
            )
            ->when(
                isset($data['office_id']),
                fn($q) =>
                $q->where('agents.office_id', $data['office_id'])
            )
            ->when(
                isset($data['status_id']),
                fn($q) =>
                $q->where('listings.status_listing_id', $data['status_id'])
            )
            ->when(
                isset($data['search']),
                fn($q) =>
                $q->where('listings.MLSID', 'like', '%' . $data['search'] . '%')
            )
            ->orderBy('listings.date_of_listing', 'desc')
            // ->orderBy('offices.name')
            // ->orderBy('users.name_to_show')
            ->paginate($perPage);

        // Calcular dias en el mercado
        $listings->getCollection()->transform(function ($listing) {
            $listing->days_in_market = $this->calculateDaysInMarket($listing);
            return $listing;
        });

        // dd($listings);

        return $listings;
    }

    public function findByKey(string $key)
    {
        $listing = Listing::where('key', $key)
            ->with([
                'agents' => function ($query) {
                    $query->select('agents.id', 'user_id')
                        ->with([
                            'user' => function ($query) {
                                $query->select('users.id', 'name_to_show');
                            }
                        ]);
                },
                'commission_option',
                'listing_information' => function ($query) {
                    $query->with([
                        'subtype_property.type_property',
                        'rooms' => function ($roomQuery) {
                            $roomQuery->select(
                                'id',
                                'description',
                                'size',
                                'dimension_x',
                                'dimension_y',
                                'room_type_id',
                                'information_id'
                            )->with([
                                'room_type:id,name',
                            ]);
                        },

                    ]);
                },
                'features' => function ($query) {
                    $query->select('features.id');
                },
                'multimedias' => function ($query) {
                    $query->select('id', 'link', 'listing_id', 'is_default', 'room_id')
                        ->orderBy('is_default', 'desc');
                },
                'location' => function ($query) {
                    $query->with([
                        'city.province.state',
                    ]);
                },
                'documentation' => function ($query) {
                    $query->with([
                        'documentation_type' => function ($typeQuery) {
                            $typeQuery->select('id', 'name', 'parent_id');
                        }
                    ]);
                },
                'status_listing',
                'addition_payments',
                'contract_type',
                'price' => function ($query) {
                    $query->select('id', 'amount', 'currency_id', 'exchange_rate_id', 'listing_id')
                        ->with([
                            'currency' => function ($query) {
                                $query->select('id', 'symbol');
                            },
                            'exchange_rate' => function ($query) {
                                $query->select('id', 'amount');
                            },
                        ]);
                },
                'logs' => function ($query) {
                    $query->select('id', 'field_name', 'old_value', 'new_value', 'created_at', 'listing_id', 'user_id')
                        ->with([
                            'user' => function ($query) {
                                $query->select('id', 'name_to_show');
                            },
                        ]);
                },
                'transactions' => function ($query) {
                    $query->select('id', 'internal_id', 'listing_id')
                        ->whereNotIn('transaction_status_id', [1])
                        ->where('transaction_type_id', 1) // Lado Captador
                        ->first();
                },
                'quality_control' => function ($query) {
                    $query->select('id', 'comment', 'is_approve', 'user_id', 'listing_id')
                        ->with([
                            'user' => function ($query) {
                                $query->select('id', 'name_to_show');
                            },
                        ])->latest()->first();
                },
                'owners' => function ($query) {
                    $query->select(
                        'contacts.id',
                        'contacts.name',
                        'contacts.last_name',
                        DB::raw("CONCAT_WS(' ', contacts.name, contacts.last_name) AS full_name"),
                        'contacts.email',
                        'contacts.mobile'
                    );
                },
                'area' => function ($query) {
                    $query->select('id', 'name');
                },
                'transaction_type' => function ($query) {
                    $query->select('id', 'name');
                },
                'rent_timeframe' => function ($query) {
                    $query->select('id', 'name');
                },
            ])
            ->firstOrFail();

        $translations = $listing->translations;

        if (!isset($translations) || empty($translations)) {
            // No tiene datos traducidos, agregar campos traducibles con valores nulos
            $languages = Language::all()->where('is_default', 0)->pluck('code')->toArray(); // Obtener los idiomas que no son el idioma por defecto
            $translationFiels = $listing->getTranslatableFields(); // Obtener los campos traducibles
            $translations = [];
            foreach ($languages as $language) {
                foreach ($translationFiels as $field) {
                    // Inicializar los campos traducibles con valores nulos
                    $translations[$language][$field] = null;
                }
            }
            $listing->translations = $translations;
        }

        // Documentation
        $publicDocumentation = $listing->documentation->filter(function ($doc) {
            return $doc->documentation_type->parent_id === DocumentationType::ID_PUBLIC_DOCUMENTATION;
        });

        $privateDocumentation = $listing->documentation->filter(function ($doc) {
            return $doc->documentation_type->parent_id === DocumentationType::ID_PRIVATE_DOCUMENTATION;
        });

        $listing->public_documentation = $publicDocumentation->values();
        $listing->private_documentation = $privateDocumentation->values();

        $listing->days_in_market = $this->calculateDaysInMarket($listing);

        return $listing;
    }

    // Calcular dias en el mercado (El modelo debe tener date_of_listing, status_listing_id, contract_end_date)
    private function calculateDaysInMarket($listing): int
    {
        // Si no existe fecha de captación (date_of_listing), no podemos calcular nada
        if (empty($listing->date_of_listing)) {
            return 0;
        }

        $captationDate = Carbon::parse($listing->date_of_listing);

        switch ($listing->status_listing_id) {
            case 2:
                // Activa: desde date_of_listing hasta la fecha actual
                return $captationDate->diffInDays(Carbon::now());

            case 3:
                // Expirada: desde date_of_listing hasta contract_end_date
                if (!empty($listing->contract_end_date)) {
                    $endDate = Carbon::parse($listing->contract_end_date);
                    return $captationDate->diffInDays($endDate);
                }
                return 0;
            case 4:
                if (!empty($listing->contract_end_date)) {
                    $endDate = Carbon::parse($listing->contract_end_date);
                    return $captationDate->diffInDays($endDate);
                }
                return 0;
            case 6:
                // Expirada: desde date_of_listing hasta cancellation_date
                if (!empty($listing->cancellation_date)) {
                    $cancellationDate = Carbon::parse($listing->cancellation_date);
                    return $captationDate->diffInDays($cancellationDate);
                }
                return 0;

            case 8:
                //vendida
                $soldDate = DB::table('transactions')
                    ->where('listing_id', $listing->id)
                    //->orderBy('sold_date', 'desc') // o 'asc' si quieres la primera vendida
                    ->value('sold_date');

                if ($soldDate) {
                    return $captationDate->diffInDays(Carbon::parse($soldDate));
                }
                return 0;
            case 7:
                $soldDate = DB::table('transactions')
                    ->where('listing_id', $listing->id)
                    //->orderBy('sold_date', 'desc') // o 'asc' si quieres la primera vendida
                    ->value('sold_date');

                if ($soldDate) {
                    return $captationDate->diffInDays(Carbon::parse($soldDate));
                }
                return 0;

            default:
                // Otros estados (Borrador=1, Alquilado=7, etc.) => 0
                return 0;
        }
    }


    public function getCaptacionesActivas($data)
    {
        $meses = $data['meses'];
        $anios = $data['anio'];
        $agent_id = $data['agent_id'];
        $office_id = $data['office_id'];
        $state_id = $data['states_id'];

        $captacionesActivas = [];

        foreach ($anios as $anio) {
            foreach ($meses as $mes) {

                $captacionesActivas[] = [
                    'mes' => $mes,
                    'anio' => $anio,
                    'captacionesActivas' =>
                    $this->getInventario([
                        'meses' => [$mes],
                        'anio' => [$anio],
                        'agent_id' => $agent_id,
                        'office_id' => $office_id,
                        'cantidad' => true,
                        'state_id' => $state_id,
                    ]),
                ];
            }
        }

        return $captacionesActivas;
    }

    public function updateExpireListing()
    {
        $listings = Listing::whereNotNull('contract_end_date')
            ->where('status_listing_id', ListingStatusId::ACTIVA)
            ->where('contract_end_date', '<', now())
            ->get();

        if ($listings->isEmpty()) {
            Log::info("No se encontraron listings para expirar.");
            return;
        }

        $ids = $listings->pluck('id')->toArray();

        Listing::whereIn('id', $ids)->update([
            'status_listing_id' => ListingStatusId::EXPIRADA,
        ]);

        // Construimos el log con MLSID y key
        $logItems = $listings->map(fn($l) => "MLSID: {$l->MLSID}, Key: {$l->key}")->toArray();
        $logMessage = "Listings expirados (" . count($logItems) . "): \n" . implode("\n", $logItems);

        Log::info($logMessage);
    }

    public function getInventario($data)
    {
        $meses = $data['meses'];
        $anios = $data['anio'];
        $agent_id = $data['agent_id'];
        $office_id = $data['office_id'];
        $state_id = isset($data['state_id']) ? $data['state_id'] : [];

        $query = Listing::leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->join('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->join('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->where(function ($subquery) {
                $subquery->whereNull('listing_prices.currency_id')
                    ->orWhere('listing_prices.currency_id', 1);
            });

        if ($agent_id && $agent_id != [''] && $office_id != []) {

            $query->whereIn('agent_listing.agent_id', $agent_id);
        } elseif (($office_id && $office_id != [''] && $office_id != []) ||
            ($state_id && $state_id != [''])
        ) {

            $query->join('agents', 'agents.id', '=', 'agent_listing.agent_id')
                ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id');

            if ($state_id && $state_id != ['']) {
                $query->leftJoin('provinces', 'provinces.id', '=', 'offices.province_id');
                if ($state_id[0] == 'other') {
                    $query->whereNotIn('provinces.state_id', [1, 2, 3]);
                } else {
                    $query->where('provinces.state_id', $state_id);
                }
            }

            if ($office_id && $office_id != [''] && $office_id != []) {
                $query->whereIn('offices.id', $office_id);
            }
        }

        $query->where(function ($subQuery) use ($meses, $anios) {

            foreach ($anios as $anio) {
                foreach ($meses as $mes) {

                    $fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                    $fechaFinal = Carbon::today()->format('Y-m-d') > $fechaInicio->copy()->endOfMonth() ? $fechaInicio->copy()->endOfMonth()->toDateString() : Carbon::today()->format('Y-m-d');
                    $lapso = [$fechaInicio->format('Y-m-d'), $fechaFinal];

                    $subQuery
                        // ->orWhere(function ($subSubQuery) use ($lapso) {
                        //     $subSubQuery->orWhereBetween('listings.date_of_listing', $lapso)
                        //         ->orWhereRaw('LEAST(
                        //         IFNULL(listings.contract_end_date, "9999-12-31"),
                        //         IFNULL(listings.cancellation_date, "9999-12-31"),
                        //         IFNULL(transactions.sold_date, "9999-12-31")
                        //         ) BETWEEN ? AND ?', [$lapso[0], $lapso[1]]);
                        // })
                        ->orWhere(function ($subSubQuery) use ($lapso) {
                            $subSubQuery->where('listings.date_of_listing', '<=', $lapso[1])
                                ->whereRaw('LEAST(
                            IFNULL(listings.contract_end_date, "9999-12-31"),
                            IFNULL(listings.cancellation_date, "9999-12-31"),
                            IFNULL(transactions.sold_date, "9999-12-31")
                            ) > ?', [$lapso[1]]);
                        });
                }
            }
        });
        if (isset($data['cantidad']) && $data['cantidad'] == 'true') {
            $query->selectRaw('DISTINCT(listings.id)')
                ->groupBy('listings.id');
            $captacionesActivas = count($query->get());
        } else {

            $queryResult = $query->select('listings.id', 'listing_prices.amount')->get();

            $queryAmount = $queryResult->sum('amount');
            $queryQuantity = count($queryResult);

            $result = [
                'amount' => $queryAmount,
                'quantity' => $queryQuantity,
            ];

            return $result;
        }


        return $captacionesActivas;
    }

    public function getDatosCaptaciones($data)
    {
        $captaciones = Listing::leftJoin('listings_information', 'listings.id', '=', 'listings_information.listing_id')
            ->leftJoin('subtype_properties', 'listings_information.subtype_property_id', '=', 'subtype_properties.id')
            ->leftJoin('type_properties', 'subtype_properties.type_property_id', '=', 'type_properties.id');

        if (
            isset($data['anios']) && !empty($data['anios']) &&
            isset($data['meses']) && !empty($data['meses'])
        ) {
            $captaciones->where(function ($query) use ($data) {
                foreach ($data['anios'] as $anio) {
                    foreach ($data['meses'] as $mes) {
                        $fechaInicio = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');
                        $fechaFinal = $fechaInicio->copy()->endOfMonth()->toDateString();

                        $query->orWhereBetween('listings.first_upload_date', [$fechaInicio->format('Y-m-d'), $fechaFinal]);
                        // $query->orWhere(function ($query) use($fechaFinal) {
                        //     $query->where('listings.contract_end_date', '>=', $fechaFinal)
                        //     ->where('listings.date_of_listing', '<=', $fechaFinal);
                        //     // ->where(function ($query) use ($fechaInicio) {
                        //     //     $query->where('listings.date_of_listing', '<' $fechaFinal);
                        //     // });
                        // });
                    }
                }
            });
        }

        if ($data['type'] == 'venta') {
            $captaciones->where('listings.transaction_type_id', 1);
        }

        if ($data['type'] == 'alquiler') {
            $captaciones->where('listings.transaction_type_id', 2);
        }

        if ($data['type'] == 'anticretico') {
            $captaciones->where('listings.transaction_type_id', 3);
        }

        if (isset($data['type_id']) && $data['type_id'] != null) {
            $captaciones->where('type_property_id', $data['type_id'])
                ->selectRaw('
                COUNT(listings.id) as total,
                subtype_properties.id,
                subtype_properties.name
            ')->groupBy('subtype_properties.id');
        } else {
            $captaciones->selectRaw('
                COUNT(listings.id) as total,
                type_properties.id,
                type_properties.name
            ')->groupBy('type_properties.id');
        }

        $resultado = $captaciones->orderBy('total', 'DESC')->get();

        return $resultado;
    }

    public function getPrecioPromedio($data)
    {
        $mesLimite = $data['meses'][count($data['meses']) - 1];
        $anioLimite = $data['anio'][count($data['anio']) - 1];

        $fecha = Carbon::createFromFormat('Y-m-d', $anioLimite . '-' . str_pad($mesLimite, 2, '0', STR_PAD_LEFT) . '-01')->endOfMonth()->toDateString();

        $queryCaptacionesTotales = $this->getQueryCaptacionesTotales($data, $fecha);

        $captacionesTotales = count($queryCaptacionesTotales->get());
        $totalPrecioCaptaciones = $queryCaptacionesTotales->sum('listing_prices.amount');


        return $captacionesTotales > 0 ? $totalPrecioCaptaciones / $captacionesTotales : 0;
    }

    public function getTiempoPromedio($data)
    {
        $mesLimite = $data['meses'][count($data['meses']) - 1];
        $anioLimite = $data['anio'][count($data['anio']) - 1];

        $fecha = Carbon::createFromFormat('Y-m-d', $anioLimite . '-' . str_pad($mesLimite, 2, '0', STR_PAD_LEFT) . '-01')->endOfMonth()->toDateString();

        $queryCaptacionesTotales = $this->getQueryCaptacionesTotales($data, $fecha);

        $captacionesTotales = count($queryCaptacionesTotales->get());

        $queryTiemposEnElMercado = Listing::selectRaw(
            'SUM(DATEDIFF(
                LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ),
                listings.date_of_listing
            )) AS dias_en_mercado_total'
        )
            ->where('listings.date_of_listing', '<=', $fecha)
            ->leftJoin('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->leftJoin('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->whereRaw(
                'LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ) <= ?',
                $fecha
            )
            ->where(function ($subQuery) {
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.contract_end_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.cancellation_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('transactions.sold_date');
                });
            })
            ->where('listing_prices.currency_id', 2);

        if (isset($data['agent_id']) && $data['agent_id'] != [''] && $data['agent_id'] != []) {
            $queryTiemposEnElMercado->whereIn('agent_listing.agent_id', $data['agent_id']);
        }

        if (isset($data['office_id']) && $data['office_id'] != [''] && $data['office_id'] != []) {
            $queryTiemposEnElMercado->whereIn('offices.id', $data['office_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryTiemposEnElMercado->leftJoin('locations', 'locations.listing_id', 'listings.id')
                ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
        }

        if (isset($data['provincies_id']) && $data['provincies_id'] != []) {
            $queryTiemposEnElMercado->whereIn('provinces.id', $data['provincies_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryTiemposEnElMercado->whereIn('provinces.state_id', $data['states_id']);
        }

        if (isset($data['cities_id']) && $data['cities_id'] != []) {
            $queryTiemposEnElMercado->whereIn('cities.id', $data['cities_id']);
        }

        if (isset($data['zones_id']) && $data['zones_id'] != []) {
            $queryTiemposEnElMercado->whereIn('locations.zone_id', $data['zones_id']);
        }

        if (isset($data['transaction_types_id']) && $data['transaction_types_id'] != []) {
            $queryTiemposEnElMercado->whereIn('listings.transaction_type_id', $data['transaction_types_id']);
        }

        $tiempoEnElMErcadoTotal = $queryTiemposEnElMercado->value('dias_en_mercado_total');

        //Query para obtener las captaciones las cuales no se han finalizado aun
        $queryCaptacionesNoFinalizadas = Listing::selectRaw(
            'MAX(DATEDIFF(
                LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ),
                listings.date_of_listing
            )) AS dias_en_mercado_total,
            listings.id,
            MAX(LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                )) as fecha_final'
        )
            ->where('listings.date_of_listing', '<=', $fecha)
            ->leftJoin('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->leftJoin('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->whereRaw(
                'LEAST(
                    IFNULL(listings.contract_end_date, "9999-12-31"),
                    IFNULL(listings.cancellation_date, "9999-12-31"),
                    IFNULL(transactions.sold_date, "9999-12-31")
                ) > ?',
                $fecha
            )
            ->where(function ($subQuery) {
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.contract_end_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('listings.cancellation_date');
                });
                $subQuery->orWhereNot(function ($notNullQuery) {
                    $notNullQuery->whereNull('transactions.sold_date');
                });
            })
            ->where('listing_prices.currency_id', 2);

        if (isset($data['agent_id']) && $data['agent_id']) {
            $queryCaptacionesNoFinalizadas->whereIn('agent_listing.agent_id', $data['agent_id']);
        }

        if (isset($data['office_id']) && $data['office_id']) {
            $queryCaptacionesNoFinalizadas->whereIn('offices.id', $data['office_id']);
        }


        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryCaptacionesNoFinalizadas->leftJoin('locations', 'locations.listing_id', 'listings.id')
                ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
        }

        if (isset($data['provincies_id']) && $data['provincies_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('provinces.id', $data['provincies_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('provinces.state_id', $data['states_id']);
        }

        if (isset($data['cities_id']) && $data['cities_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('cities.id', $data['cities_id']);
        }

        if (isset($data['zones_id']) && $data['zones_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('locations.zone_id', $data['zones_id']);
        }

        if (isset($data['transaction_types_id']) && $data['transaction_types_id'] != []) {
            $queryCaptacionesNoFinalizadas->whereIn('listings.transaction_type_id', $data['transaction_types_id']);
        }

        $captacionesNoFinalizadas = $queryCaptacionesNoFinalizadas->groupBy('listings.id')->get();

        foreach ($captacionesNoFinalizadas as $captacion) {
            $fechaFinal = Carbon::parse($captacion->fecha_final);
            $fecha = Carbon::parse($fecha);

            $diferencia = $fecha->diffInDays($fechaFinal);
            $tiempoEnElMErcadoTotal += $captacion->dias_en_mercado_total - $diferencia;
        }


        $tiempoPromedio = $captacionesTotales > 0 ? $tiempoEnElMErcadoTotal / $captacionesTotales : 0;

        return $tiempoPromedio;
    }

    private function getQueryCaptacionesTotales($data, $fechaLimite)
    {

        $queryCaptacionesTotales = Listing::select('listing_prices.amount')
            ->where('date_of_listing', '<=', $fechaLimite)
            ->leftJoin('listing_prices', 'listings.id', '=', 'listing_prices.listing_id')
            ->leftJoin('agent_listing', 'listings.id', '=', 'agent_listing.listing_id')
            ->leftJoin('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->where('listing_prices.currency_id', 2);

        if (isset($data['agent_id']) && $data['agent_id'] != [''] && $data['agent_id'] != []) {
            $queryCaptacionesTotales->whereIn('agent_listing.agent_id', $data['agent_id']);
        }

        if (isset($data['office_id']) && $data['office_id'] != [''] && $data['office_id'] != []) {
            $queryCaptacionesTotales->whereIn('offices.id', $data['office_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            $queryCaptacionesTotales->leftJoin('locations', 'locations.listing_id', 'listings.id')
                ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'cities.province_id');
        }

        if (isset($data['provincies_id']) && $data['provincies_id'] != []) {
            $queryCaptacionesTotales->whereIn('provinces.id', $data['provincies_id']);
        }

        if (isset($data['states_id']) && $data['states_id'] != []) {
            if ($data['states_id'][0] == 'other') {
                $queryCaptacionesTotales->whereNotIn('provinces.state_id', [1, 2, 3]);
            } else {
                $queryCaptacionesTotales->whereIn('provinces.state_id', $data['states_id']);
            }
        }

        if (isset($data['cities_id']) && $data['cities_id'] != []) {
            $queryCaptacionesTotales->whereIn('cities.id', $data['cities_id']);
        }

        if (isset($data['zones_id']) && $data['zones_id'] != []) {
            $queryCaptacionesTotales->whereIn('locations.zone_id', $data['zones_id']);
        }

        if (isset($data['transaction_types_id']) && $data['transaction_types_id'] != []) {
            $queryCaptacionesTotales->whereIn('listings.transaction_type_id', $data['transaction_types_id']);
        }

        return $queryCaptacionesTotales;
    }
    // Metodo especifico para el paso 6, ya que alli se manda el transaction_id
    public function updateBuyers($data)
    {
        $transaction = Transaction::where('internal_id', $data['transaction_id'])->first();

        $hasChanged = false;

        $contact_id_mapping = array_map(function ($contacto) {
            return $contacto['id'];
        }, $data['buyers']);

        $buyersToDelete = $transaction->listing->buyers->filter(function ($buyer) use ($contact_id_mapping) {
            return !in_array($buyer->id, $contact_id_mapping);
        });

        foreach ($buyersToDelete as $contact) {
            $hasChanged = true;
            $buyer = Buyer::where('contact_id', $contact->id)->first();
            $buyer->delete();
        }

        $actual_buyers_mapping_id = array_map(function ($contacto) {
            return $contacto['id'];
        }, $transaction->listing->buyers->toArray());

        foreach ($data['buyers'] as $buyer) {
            if ($buyer['id'] && !in_array($buyer['id'], $actual_buyers_mapping_id)) {
                $hasChanged = true;
                Buyer::create([
                    'listing_id' => $transaction->listing_id,
                    'contact_id' => $buyer['id']
                ]);
            }
        }

        if ($hasChanged && $transaction->transaction_status_id != 3) {
            $transaction->transaction_status_id = 4;
            $transaction->save();
        }

        return $contact_id_mapping;
    }
    public function deleteListing(Listing $listing)
    {
        Log::info("Eliminando listing con (MLS: $listing->MLSID )");
        if ($listing->listing_information) {
            Log::info("Existe listing information");

            if ($listing->listing_information->rooms()->exists()) {
                Log::info("Existe habitaciones");
                $listing->listing_information->rooms()->delete();
            }

            $listing->listing_information()->delete();
        }

        $listing->listing_prices()?->delete();
        $listing->location()?->delete();
        $listing->commission_option()?->delete();
        $listing->features()?->detach();
        $listing->multimedias()?->delete();
        $listing->documentation()?->delete();
        $listing->logs()?->delete();
        $listing->quality_control()?->delete();

        if ($listing->transactions()->exists()) {
            Log::info("Tiene transaction");
        }
        $listing->transactions()?->delete();

        // Relation belongs to many
        $listing->buyers()?->detach();
        $listing->owners()?->detach();
        $listing->agents()?->detach();

        $listing->delete();
    }
    /**
     * @param agent_id int
     * @return int
     *
     */
    public function updateFromProperty(Listing $listing, ListingMigrationDto $dto, int $exchange_rate_id)
    {
        $agent = Agent::with([
            'user' => function ($query) use ($dto) {
                $query->where('name_to_show', $dto->agentName);
            },
        ])->first();

        if (!$agent) {
            Log::info("⚠ No existe el agent_id $dto->agentId y el name $dto->agentName");
        }

        $key = null;
        if ($listing->key) {
            Log::info("Se queda con su key");
            $key = $listing->key;
        } else if ($dto->key) {
            Log::info("Se copia desde el excel");
            $key = $dto->key;
        } else {
            Log::info("Generar nueva key");
            $key = StringGenerateKey::generateKey();
        }

        $listing->key = $key;
        $listing->MLSID = $dto->mlsId;
        $listing->status_listing_id = $dto->statusId;
        $listing->transaction_type_id = $dto->transactionId;
        $listing->contract_type_id = $dto->contractId;
        $listing->date_of_listing = $dto->listingDate;
        $listing->updated_at = $dto->updatedAt;
        $listing->cancellation_date = $dto->cancellationDate;
        $listing->contract_end_date = $dto->contractEndDate;
        $listing->cancellation_reason_id = $dto->cancellationReasonId;
        if ($dto->areaId) {
            $listing->area_id = $dto->areaId;
        } else {
            Log::info("Area id no modificada, el subtype property (id: $dto->subTypePropertyId) no tiene area_id definida");
        }

        Log::info("Asignaciones correctas");

        if (!$listing->exists || !isset($listing->id)) {
            Log::info("No existe el listings con MLSID $listing->MLSID - key: $listing->key");
            $listing->save();
        }

        if ($agent) {
            $listing->agents()->syncWithoutDetaching([
                $agent->id => ['type' => 1],
            ]);

            if ($agent->user) {
                $agent->user->remax_title_to_show_id = $dto->remaxTitleToShowId;
                $agent->user->save();
            }
        } else {
            Log::warning("⚠️ No se encontró el agente con internal_id {$dto->agentId} para el listing MLSID {$listing->MLSID}.");
        }

        $price = match ($dto->currency) {
            'USD' => $dto->price * 6.96,
            'EUR' => $dto->price * 9,
            default => $dto->price,
        };

        $listing->listing_prices()->updateOrCreate(
            ['currency_id' => 1],
            [
                'amount' => $price,
                'currency_id' => 1, // por si acaso (aunque ya está en where)
                'exchange_rate_id' => $exchange_rate_id,
            ]
        );

        $location = $listing->location;
        if ($location) {
            $listing->location()->update(
                [
                    'city_id' => $dto->cityId,
                    'zone_id' => $dto->zoneId,
                    'first_address' => $dto->address,
                    'number' => $dto->number,
                    'zip_code' => $dto->zipCode,
                ]
            );
        } else {
            $listing->location()->create(
                [
                    'city_id' => $dto->cityId,
                    'zone_id' => $dto->zoneId,
                    'first_address' => $dto->address,
                    'number' => $dto->number,
                    'zip_code' => $dto->zipCode,
                    'show_addres_on_website' => $location?->show_addres_on_website ?? true, // Puede que no sea correcto
                ]
            );
        }

        $listing->commission_option()->updateOrCreate(
            [],
            [
                'recruitment_commission' => $dto->listingPercentage,
                'type_recruitment_commission' => 'P',
                'sales_commission' => $dto->soldPercentage,
                'sales_commission_type' => 'P',
            ]
        );

        if ($contact = Contact::where('contact_key', $dto->ownerId)->orWhere('name', $dto->ownerName)->first()) {
            $contact->update([
                'contact_key' => $dto->ownerId,
                'email' => $dto->ownerEmail,
                'mobile' => $dto->ownerCell,
                'home_phone' => $dto->ownerPhone,
            ]);
        } else {
            Contact::create([
                'contact_key' => $dto->ownerId,
                'name' => $dto->ownerName,
                'email' => $dto->ownerEmail,
                'mobile' => $dto->ownerCell,
                'home_phone' => $dto->ownerPhone,
            ]);
        }

        $listing->listing_information()->updateOrCreate(
            [],
            [
                'subtype_property_id' => $dto->subTypePropertyId,
                'property_category_id' => $dto->categoryId,
            ]
        );

        $listing->save();
    }

    public function getLasMLSID($agent_id)
    {
        $agent = Agent::find($agent_id);
        $listing = Listing::where('MLSID', 'like', '%' . $agent->agent_internal_id . '%')->orderBy('MLSID', 'desc')->first();
        $lastMLSID = $listing ? explode('-', $listing->MLSID)[1] : 0;
        return $lastMLSID;
    }
    public function downloadPdf(string $key)
    {
        $listing = Listing::select(
            'id',
            'key',
            'MLSID',
            'title',
            'description_website',
            'marketing_description',
            'status_listing_id',
            'transaction_type_id',
        )
            ->where('key', $key)
            ->with([
                'agents' => function ($query) {
                    $query->select('agents.id', 'agents.image_name', 'user_id', 'office_id')
                        ->with([
                            'user' => function ($query) {
                                $query->select('users.id', 'name_to_show', 'email', 'phone_number');
                            },
                            'office' => function ($query) {
                                $query->select(
                                    'offices.office_id',
                                    'offices.name',
                                    'city_id',
                                    'province_id',
                                )->with([
                                    'city' => function ($query) {
                                        $query->select('id', 'name');
                                    },
                                    'province' => function ($query) {
                                        $query->select('id', 'name')
                                            ->with('state');
                                    },
                                ]);
                            }
                        ]);
                },
                'transaction_type:id,name',
                'listing_information' => function ($query) {
                    $query->select(
                        'id',
                        'parking_slots',
                        'total_number_rooms',
                        'number_bathrooms',
                        'number_bedrooms',
                        'number_toiletrooms',
                        'construction_area_m',
                        'total_area',
                        'year_construction',
                        'land_m2',
                        'listing_id',
                        'subtype_property_id'
                    )->with([
                        'subtype_property.type_property',
                    ]);
                },
                'price' => function ($query) {
                    $query->select('id', 'amount', 'currency_id', 'listing_id')
                        ->with(['currency']);
                },
                'location' => function ($query) {
                    $query->select('id', 'first_address', 'listing_id', 'city_id')
                        ->with([
                            'city' => function ($query) {
                                $query->select('id', 'name', 'province_id')
                                    ->with(['province.state']);
                            },
                            'zone' => function ($query) {
                                $query->select('id', 'name');
                            },
                        ]);
                },
                'multimedias' => function ($query) {
                    $query->select('id', 'link', 'listing_id', 'is_default')
                        ->orderBy('is_default', 'desc')
                        ->limit(6);
                },
                'features'
            ])->first();

        if (!$listing) {
            throw new \Exception('Listing not found');
        }

        $symbol = $this->getCurrencySymbol($listing->price?->currency?->symbol ?? '');
        $formattedPrice = $symbol . ' ' . number_format($listing->price?->amount, 2);

        $pdf = Pdf::loadView('pdf.listing', compact('listing', 'formattedPrice'))
            ->setPaper('letter', 'portrait')
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0);

        return $pdf->download($listing->MLSID . '.pdf');

        return view(
            'pdf.listing',
            [
                'listing' => $listing,
                'formattedPrice' => $formattedPrice,
            ],
        );
    }

    private function getCurrencySymbol(string $symbol): string
    {
        return match ($symbol) {
            'BOB' => 'Bs',
            'USD' => '$',
            default => $symbol,
        };
    }

    public function generateExcel(array $filters): Spreadsheet
    {
        ini_set('memory_limit', '1024M');
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        Log::info("Generación Excel iniciada.");

        $spreadsheet = new Spreadsheet();

        Calculation::getInstance($spreadsheet)->disableCalculationCache();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $sheet = $spreadsheet->getActiveSheet();
        $headers = [
            'Departamento',
            'Nombre Oficina',
            'Nombre Agente Asociado',
            'Titulo a Enseñar',
            'MLS ID',
            'Integrador de ID',
            'Moneda',
            'Precio',
            'Ciudad',
            'Hora Local',
            'Dirección',
            'Numero en la Calle',
            'Código Postal',
            'Status de Captación',
            'Tipo de Transacción',
            'Tipo de Transacción',
            'Categoría de propiedad',
            'Tipo de Propiedad',
            'Borrar',
            'Fecha Captación',
            'Fecha Cargado a la Web',
            'Ultima Actualización',
            'Fecha Cancelado',
            'CancellationReason',
            'Fecha de Venta/Alquiler',
            'Fecha de Vencimiento',
            'Listing Percentage',
            // 'Tipo de Comisión Captación',
            'Porcentaje de Venta',
            // 'Tipo de Comisión Venta',
            'Precio Venta/Alquiler',
            'Comisión Total',
            'Nombre del dueño',
            'Id del Contacto',
            'Email del dueño',
            'Cell del dueño',
            'Casa del dueño',
            'Días en el Mercado',
            'Transferir Nombre Agente Asociado',
            'Transferir MLSID',
            'Transferir Date',
        ];

        $data = [$headers];
        $sheet = $spreadsheet->getActiveSheet();

        $row = 2;
        $listings = Listing::query()
            ->from('listings')
            ->select([
                'listings.id',
                'listings.MLSID',
                'listings.status_listing_id',
                'listings.contract_type_id',
                'listings.transaction_type_id',
                'listings.cancellation_reason_id',
                'listings.cancellation_date',
                'listings.date_of_listing',
                'listings.updated_at',
                'listings.contract_end_date',
                'regions.name as region_name',
                'agents.office_id',
                'users.name_to_show',
                'users.remax_title_to_show_id',
                'offices.name as office_name',
                'remax_titles.name as remax_title_name',
                'cities.name as city_name',
                'zones.name as zone_name',
                'locations.first_address',
                'locations.number',
                'locations.zip_code',
                'currencies.symbol as currency_symbol',
                'listing_prices.amount as price_amount',
                'status_listings.name as status_listing_name',
                'transaction_types.name as transaction_type_name',
                'contract_types.name as contract_type_name',
                'properties_category.name_properties_categories',
                'subtype_properties.name as subtype_property_name',
                'cancellation_reasons.name as cancellation_reason_name',
                'transactions.sold_date',
                'transactions.current_listing_price',
                'commissions_option.recruitment_commission',
                'commissions_option.type_recruitment_commission',
                'commissions_option.sales_commission',
                'commissions_option.sales_commission_type',
                'contacts.id as owner_id',
                'contacts.name as owner_name',
                'contacts.last_name as owner_last_name',
                'contacts.email as owner_email',
                'contacts.mobile as owner_mobile',
                'contacts.home_phone as owner_home_phone',
            ])
            ->join('agent_listing', function ($join) {
                $join->on('agent_listing.listing_id', '=', 'listings.id')
                    ->where('agent_listing.type', 1);
            })
            ->join('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->join('users', 'users.id', '=', 'agents.user_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->join('regions', 'regions.id', '=', 'offices.region_id')
            ->leftJoin('remax_titles', 'remax_titles.id', '=', 'users.remax_title_to_show_id')
            ->leftJoin('locations', 'locations.listing_id', '=', 'listings.id')
            ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
            ->leftJoin('zones', 'zones.id', '=', 'locations.zone_id')
            ->leftJoin('listing_prices', 'listing_prices.listing_id', '=', 'listings.id')
            ->leftJoin('currencies', 'currencies.id', '=', 'listing_prices.currency_id')
            ->join('status_listings', 'status_listings.id', '=', 'listings.status_listing_id')
            ->join('transaction_types', 'transaction_types.id', '=', 'listings.transaction_type_id')
            ->leftJoin('contract_types', 'contract_types.id', '=', 'listings.contract_type_id')
            ->leftJoin('cancellation_reasons', 'cancellation_reasons.id', '=', 'listings.cancellation_reason_id')
            ->leftJoin('commissions_option', 'commissions_option.listing_id', '=', 'listings.id')
            ->leftJoin('transactions', function ($join) {
                $join->on('transactions.listing_id', '=', 'listings.id')
                    ->where('transactions.transaction_type_id', 1)
                    ->where('transactions.transaction_status_id', 5)
                    ->limit(1);
            })
            ->leftJoin('owner', 'owner.listing_id', '=', 'listings.id')
            ->leftJoin('contacts', 'contacts.id', '=', 'owner.contact_id')
            ->join('listings_information', 'listings_information.listing_id', '=', 'listings.id')
            ->leftJoin('properties_category', 'properties_category.id', '=', 'listings_information.property_category_id')
            ->join('subtype_properties', 'subtype_properties.id', '=', 'listings_information.subtype_property_id')
            ->where('listings.is_external', '!=', 1)
            ->where('offices.active_office', '=', 1)
            ->when(
                isset($filters['status_id']),
                fn($q) =>
                $q->where('listings.status_listing_id', $filters['status_id'])
            )
            ->when(
                isset($filters['office_id']),
                fn($q) =>
                $q->where('agents.office_id', $filters['office_id'])
            )
            ->distinct()
            ->orderBy('offices.name')
            ->orderBy('users.name_to_show')
            ->orderBy('listings.date_of_listing', 'asc')
            ->get();

        foreach ($listings as $listing) {
            $data[] = [
                $listing->region_name,
                $listing->office_name,
                $listing->name_to_show,
                $listing->remax_title_name,
                $listing->MLSID,
                '', // Integrador Id
                $listing->currency_symbol,
                $listing->price_amount,
                $listing->city_name,
                $listing->zone_name,
                $listing->first_address,
                $listing->number,
                $listing->zip_code,
                $listing->status_listing_name,
                $listing->transaction_type_name,
                $listing->contract_type_name,
                $listing->name_properties_categories,
                $listing->subtype_property_name,
                'No',
                $listing->date_of_listing ? Date::PHPToExcel(new \DateTime($listing->date_of_listing)) : null,
                $listing->date_of_listing ? Date::PHPToExcel(new \DateTime($listing->date_of_listing)) : null, // Fecha cargado en la web
                $listing->updated_at ? Date::PHPToExcel(new \DateTime($listing->updated_at)) : null,
                $listing->cancellation_date ? Date::PHPToExcel(new \DateTime($listing->cancellation_date)) : null,
                $listing->cancellation_reason_name,
                $listing->sold_date ? Date::PHPToExcel(new \DateTime($listing->sold_date)) : null,
                $listing->contract_end_date ? Date::PHPToExcel(new \DateTime($listing->contract_end_date)) : null,
                $listing->recruitment_commission,
                // $this->getCommisionType($listing->type_recruitment_commission),
                $listing->sales_commission,
                // $this->getCommisionType($listing->sales_commission_type),
                $listing->current_listing_price,
                $listing->current_listing_price
                    ? $listing->current_listing_price * (($listing->recruitment_commission + $listing->sales_commission) / 100)
                    : null,
                $listing->owner_name . ' ' . $listing->owner_last_name,
                $listing->owner_id,
                $listing->owner_email,
                $listing->owner_mobile,
                $listing->owner_home_phone,
                $this->calculateDaysInMarket($listing),
                '', // Transferencia
                '',
                '',
            ];
        }
        $sheet->fromArray($data, null, 'A' . $row, true);
        $sheet->getStyle('A2:AM2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1F4E78'], // Azul oscuro
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2'], // Azul claro
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '8EA9DB'],
                ],
            ],
        ]);

        foreach ($data[0] as $columnIndex => $value) {
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        $startRow = 3;
        $endRow = $startRow + count($data) - 1; // número de filas con datos
        $dateColumns = ['T', 'U', 'V', 'W', 'Z']; // ejemplo: columnas de fechas
        $this->formatExcelDateColumns($sheet, $dateColumns, $startRow, $endRow);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        Log::info("Excel generado en " . round($endTime - $startTime, 2) . " segundos.");
        Log::info("Memoria utilizada: " . round(($endMemory - $startMemory) / 1024 / 1024, 2) . " MB.");

        return $spreadsheet;
    }

    // public function generateExcel(array $filters): Spreadsheet
    // {
    //     ini_set('memory_limit', '1024M');
    //     Log::info("Generación Excel iniciada.");

    //     $startTime = microtime(true);
    //     $startMemory = memory_get_usage(true);

    //     $spreadsheet = new Spreadsheet();
    //     Calculation::getInstance($spreadsheet)->disableCalculationCache();
    //     $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
    //     $sheet = $spreadsheet->getActiveSheet();

    //     $exporter = app(ListingExportService::class);
    //     $headers = $exporter->getHeaders();
    //     $sheet->fromArray([$headers], null, 'A2');

    //     $row = 3;
    //     $exporter->getListingsQuery($filters)
    //         ->select($exporter->getSelectColumns())
    //         ->get()
    //         ->each(function ($listing) use ($sheet, $exporter, &$row) {
    //             $sheet->fromArray([$exporter->mapListingToRow($listing, true)], null, 'A' . $row);
    //             $row++;
    //         });

    //     // Estilos de encabezado
    //     $sheet->getStyle('A2:AM2')->applyFromArray([
    //         'font' => [
    //             'bold' => true,
    //             'color' => ['rgb' => '1F4E78'],
    //         ],
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID,
    //             'startColor' => ['rgb' => 'D9E1F2'],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    //         ],
    //         'borders' => [
    //             'bottom' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                 'color' => ['rgb' => '8EA9DB'],
    //             ],
    //         ],
    //     ]);

    //     foreach (range(1, count($headers)) as $colIndex) {
    //         $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
    //     }

    //     $this->formatExcelDateColumns($sheet, ['T', 'U', 'V', 'W', 'Z'], 3, $row - 1);

    //     Log::info("Excel generado en " . round(microtime(true) - $startTime, 2) . " segundos.");
    //     Log::info("Memoria utilizada: " . round((memory_get_usage(true) - $startMemory) / 1024 / 1024, 2) . " MB.");

    //     return $spreadsheet;
    // }

    public function streamCsv(array $filters, $handle): void
    {
        $exporter = app(ListingExportService::class);
        $headers = $exporter->getHeaders();

        // Codificación BOM UTF-8 para compatibilidad con Excel
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($handle, $headers, ';');

        $exporter->getListingsQuery($filters)
            ->select($exporter->getSelectColumns())
            ->orderBy('offices.name')
            ->orderBy('users.name_to_show')
            ->orderBy('listings.date_of_listing')
            ->chunk(50000, function ($listings) use ($handle, $exporter) {
                foreach ($listings as $listing) {
                    fputcsv($handle, $exporter->mapListingToRow($listing, false), ';');
                }
            });
    }



    /*
    public function streamCsv(array $filters, $handle): void
    {
        $headers = [
            'Departamento',
            'Nombre Oficina',
            'Nombre Agente Asociado',
            'Titulo a Enseñar',
            'MLS ID',
            'Integrador de ID',
            'Moneda',
            'Precio',
            'Ciudad',
            'Hora Local',
            'Dirección',
            'Número en la Calle',
            'Código Postal',
            'Status de Captación',
            'Tipo de Transacción',
            'Tipo de Contrato',
            'Categoría de propiedad',
            'Tipo de Propiedad',
            'Borrar',
            'Fecha Captación',
            'Fecha Cargado a la Web',
            'Ultima Actualización',
            'Fecha Cancelado',
            'CancellationReason',
            'Fecha de Venta/Alquiler',
            'Fecha de Vencimiento',
            'Listing Percentage',
            'Porcentaje de Venta',
            'Precio Venta/Alquiler',
            'Comisión Total',
            'Nombre del dueño',
            'Id del Contacto',
            'Email del dueño',
            'Cell del dueño',
            'Casa del dueño',
            'Días en el Mercado',
            'Transferir Nombre Agente Asociado',
            'Transferir MLSID',
            'Transferir Date',
        ];

        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        fputcsv($handle, $headers, ';');

        Listing::query()
            ->from('listings')
            ->select([
                'listings.id',
                'listings.MLSID',
                'listings.date_of_listing',
                'listings.updated_at',
                'listings.contract_end_date',
                'regions.name as region_name',
                'offices.name as office_name',
                'users.name_to_show',
                'remax_titles.name as remax_title_name',
                'cities.name as city_name',
                'zones.name as zone_name',
                'locations.first_address',
                'locations.number',
                'locations.zip_code',
                'currencies.symbol as currency_symbol',
                'listing_prices.amount as price_amount',
                'status_listings.name as status_listing_name',
                'transaction_types.name as transaction_type_name',
                'contract_types.name as contract_type_name',
                'properties_category.name_properties_categories',
                'subtype_properties.name as subtype_property_name',
                'cancellation_reasons.name as cancellation_reason_name',
                'transactions.sold_date',
                'transactions.current_listing_price',
                'commissions_option.recruitment_commission',
                'commissions_option.sales_commission',
                'contacts.id as owner_id',
                'contacts.name as owner_name',
                'contacts.last_name as owner_last_name',
                'contacts.email as owner_email',
                'contacts.mobile as owner_mobile',
                'contacts.home_phone as owner_home_phone',
            ])
            ->join(
                'agent_listing',
                fn($join) =>
                $join->on('agent_listing.listing_id', '=', 'listings.id')
                    ->where('agent_listing.type', 1)
            )
            ->join('agents', 'agents.id', '=', 'agent_listing.agent_id')
            ->join('users', 'users.id', '=', 'agents.user_id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->join('regions', 'regions.id', '=', 'offices.region_id')
            ->leftJoin('remax_titles', 'remax_titles.id', '=', 'users.remax_title_to_show_id')
            ->leftJoin('locations', 'locations.listing_id', '=', 'listings.id')
            ->leftJoin('cities', 'cities.id', '=', 'locations.city_id')
            ->leftJoin('zones', 'zones.id', '=', 'locations.zone_id')
            ->leftJoin('listing_prices', 'listing_prices.listing_id', '=', 'listings.id')
            ->leftJoin('currencies', 'currencies.id', '=', 'listing_prices.currency_id')
            ->leftJoin('status_listings', 'status_listings.id', '=', 'listings.status_listing_id')
            ->leftJoin('transaction_types', 'transaction_types.id', '=', 'listings.transaction_type_id')
            ->leftJoin('contract_types', 'contract_types.id', '=', 'listings.contract_type_id')
            ->leftJoin('cancellation_reasons', 'cancellation_reasons.id', '=', 'listings.cancellation_reason_id')
            ->leftJoin('commissions_option', 'commissions_option.listing_id', '=', 'listings.id')
            ->leftJoin(
                'transactions',
                fn($join) =>
                $join->on('transactions.listing_id', '=', 'listings.id')
                    ->where('transactions.transaction_status_id', 5)
            )
            ->leftJoin('owner', 'owner.listing_id', '=', 'listings.id')
            ->leftJoin('contacts', 'contacts.id', '=', 'owner.contact_id')
            ->leftJoin('listings_information', 'listings_information.listing_id', '=', 'listings.id')
            ->leftJoin('properties_category', 'properties_category.id', '=', 'listings_information.property_category_id')
            ->leftJoin('subtype_properties', 'subtype_properties.id', '=', 'listings_information.subtype_property_id')
            ->where('listings.is_external', 0)
            ->where('offices.active_office', '=', 1)
            ->when($filters['status_id'] ?? null, fn($q, $v) => $q->where('listings.status_listing_id', $v))
            ->when($filters['office_id'] ?? null, fn($q, $v) => $q->where('agents.office_id', $v))
            ->distinct()
            ->orderBy('offices.name')
            ->orderBy('users.name_to_show')
            ->orderByDesc('listings.date_of_listing')
            ->chunk(50000, function ($listings) use ($handle) {
                foreach ($listings as $listing) {
                    fputcsv($handle, [
                        $listing->region_name,
                        $listing->office_name,
                        $listing->name_to_show,
                        $listing->remax_title_name,
                        $listing->MLSID,
                        '',
                        $listing->currency_symbol,
                        $listing->price_amount,
                        $listing->city_name,
                        $listing->zone_name,
                        $listing->first_address,
                        $listing->number,
                        $listing->zip_code,
                        $listing->status_listing_name,
                        $listing->transaction_type_name,
                        $listing->contract_type_name,
                        $listing->name_properties_categories,
                        $listing->subtype_property_name,
                        'No',
                        $listing->date_of_listing,
                        $listing->date_of_listing,
                        $listing->updated_at,
                        $listing->cancellation_date,
                        $listing->cancellation_reason_name,
                        $listing->sold_date,
                        $listing->contract_end_date,
                        $listing->recruitment_commission,
                        $listing->sales_commission,
                        $listing->current_listing_price,
                        $listing->current_listing_price
                            ? $listing->current_listing_price * (($listing->recruitment_commission + $listing->sales_commission) / 100)
                            : null,
                        $listing->owner_name . ' ' . $listing->owner_last_name,
                        $listing->owner_id,
                        $listing->owner_email,
                        $listing->owner_mobile,
                        $listing->owner_home_phone,
                        $this->calculateDaysInMarket($listing),
                        '',
                        '',
                        '',
                    ], ';');
                }
            });
    }
    */

    private function getCommisionType(?string $type)
    {
        return match ($type) {
            'P' => 'Porcentaje',
            'C' => 'Cantidad',
            default => null,
        };
    }

    function formatExcelDateColumns(Worksheet $sheet, array $columnLetters, int $startRow, int $endRow): void
    {
        foreach ($columnLetters as $col) {
            $range = "{$col}{$startRow}:{$col}{$endRow}";
            $sheet->getStyle($range)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        }
    }
}
