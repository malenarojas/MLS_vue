<?php

namespace App\Services;

use App\Dtos\MarketAnalysisDto;
use App\Models\Feature;
use App\Models\Listing;
use App\Models\ListingPrice;
use App\Models\MarketAnalysis;
use App\Services\Agents\AreaService;
use App\Services\Agents\AreaSpecialityService;
use App\Services\Agents\CityService;
use App\Services\Agents\OfficeService;
use App\Services\Agents\RegionService;
use App\Services\Agents\SpecialityService;
use App\Services\Listings\ContractTypeService;
use App\Services\Listings\ListingInformationService;
use App\Services\Listings\ListingService;
use App\Services\Listings\ListingPriceService;
use App\Services\Listings\StatusListingService;
use App\Services\Listings\SubtypePropertyService;
use App\Services\Listings\ListingTransactionTypeService;
use App\Services\Listings\StatePropertyService;
use App\Services\Listings\MarketStatusService;
use App\Services\Listings\FeatureService;
use App\Services\Location\ProvinciaService;
use App\Services\Listings\PropertyCategoryService;
use App\Services\LocationService;
use App\Services\Location\StateService;
use App\Models\Currency;
use App\Services\Location\ZoneService;
use App\Services\Transactions\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarketAnalysisService
{

    protected $officeService;
    protected $regionService;
    protected $specialityService;
    protected $AreaSpecialityService;

    protected $AreaService;
    protected $CityService;
    protected $ListingPriceService;
    protected $ZoneService;
    protected $StateService;
    protected $ProvinciaService;
    protected $ListingService;
    protected $LocationService;
    protected $statusListingService;
    protected $statePropertyService;

    protected $contractTypeService;
    protected $listingTransactionTypeService;
    protected $marketStatusService;
    protected $SubtypePropertyService;
    protected $PropertyCategoryService;
    protected $FeatureService;


    public function __construct(
        OfficeService $officeService,
        RegionService $regionService,
        SpecialityService $specialityService,
        AreaSpecialityService $AreaSpecialityService,
        CityService $CityService,
        ZoneService $ZoneService,
        StateService $StateService,
        ProvinciaService $ProvinciaService,
        ListingService $ListingService,
        ListingPriceService $ListingPrice,
        LocationService $LocationService,
        StatusListingService $statusListingService,
        AreaService $AreaService,
        ContractTypeService $contractTypeService,
        StatePropertyService $statePropertyService,
        ListingTransactionTypeService $listingTransactionTypeService,
        MarketStatusService $marketStatusService,
        SubtypePropertyService $SubtypePropertyService,
        PropertyCategoryService $PropertyCategoryService,
        FeatureService $FeatureService,


    ) {
        $this->officeService = $officeService;
        $this->regionService = $regionService;
        $this->specialityService = $specialityService;
        $this->AreaSpecialityService = $AreaSpecialityService;
        $this->CityService = $CityService;
        $this->ZoneService = $ZoneService;
        $this->StateService = $StateService;
        $this->ProvinciaService = $ProvinciaService;
        $this->ListingService = $ListingService;
        $this->statusListingService = $statusListingService;
        $this->LocationService = $LocationService;
        $this->AreaService = $AreaService;
        $this->contractTypeService = $contractTypeService;
        $this->statePropertyService = $statePropertyService;
        $this->listingTransactionTypeService= $listingTransactionTypeService;
        $this->marketStatusService= $marketStatusService;
        $this->SubtypePropertyService = $SubtypePropertyService;
        $this->PropertyCategoryService =$PropertyCategoryService;
        $this->FeatureService = $FeatureService;
    }

    public function getFormOptions(): array
    {
        return [
            'offices' => $this->officeService ? $this->officeService->getAll() : [],
            'regions' => $this->regionService ? $this->regionService->getRegionsWithOfficesAndTeamStatuses() : [],
            'areas' => $this->AreaService ? $this->AreaService->getAreasWithSpecialities() : [],
            'city' => $this->CityService ? $this->CityService->getAll() : [],
            'listing_prices' => $this->ListingPriceService ? $this->ListingPriceService->getAll() : [],
            'status_listing' => $this->statusListingService ? $this->statusListingService->getAll() : [],
            'zone' => $this->ZoneService ? $this->ZoneService->getAll() : [],
            'state' => $this->StateService ? $this->StateService->getAll() : [],
            'province' => $this->ProvinciaService ? $this->ProvinciaService->getAll() : [],
            //'location' => $this->LocationService ? $this->LocationService->getAll() : [],
            'contract_type' => $this->contractTypeService ? $this->contractTypeService->getAll() : [],
            'state_property' => $this->statePropertyService ? $this->statePropertyService->getAll() : [],
            'listing_transaccion_type' => $this->listingTransactionTypeService ? $this->listingTransactionTypeService->getAll() : [],
            'market_status' => $this->marketStatusService ? $this->marketStatusService->getAll() : [],
            'subtype_property' => $this->SubtypePropertyService ? $this->SubtypePropertyService->getAll() : [],
            'category_property' => $this->PropertyCategoryService ? $this->PropertyCategoryService->getAll() : [],
            'feactures' => $this->FeatureService ? $this->FeatureService->getAll() : [],
            'currencies' => Currency::all(),
        ];
    }

    public function index(): array
    {
        try {

            $formOptions = $this->getFormOptions();

            return [
                'options' => $formOptions,
            ];
        } catch (\Exception $e) {

            throw $e; // Opcional: puedes lanzar la excepción para manejarla en el controlador
        }
    }
    public function findById(string $id): Listing
    {
        return Listing::with(['agent', 'listing_information', 'location'])->findOrFail($id);
    }

    public function search(MarketAnalysisDto $dto): Collection
    {
        try {
            // Llamar al procedimiento almacenado combinado
            $results = DB::select('CALL CombinedFilter( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $dto->status,
                $dto->start_date,
                $dto->end_date,
                $dto->minimum_price,
                $dto->maximum_price,
                $dto->market_segment,
                $dto->transaction_type,
                $dto->property_status,
                $dto->contract_type,
                $dto->region_id,
                $dto->state_id,
                $dto->province_id,
                $dto->city_id,
                $dto->zone_id,
                $dto->street_name,
                $dto->street_number,
                $dto->address,
                $dto->district,
                $dto->postal_code,
                $dto->only_office, // Asegúrate de que este campo está presente en el DTO
            ]);

            // Extraer los IDs de los resultados
            $commonIds = collect($results)->pluck('id')->unique();

            if ($commonIds->isEmpty()) {
                return collect(); // Retorna una colección vacía si no hay coincidencias
            }

            // Obtener los listados finales con relaciones necesarias
            $listings = Listing::with([
                'status_listing',
                'listing_prices',
                'multimedias.multimedia_type'
            ])
            ->whereIn('id', $commonIds)
            ->get();

            return $listings;
        } catch (\Exception $e) {
            // Registrar el error internamente
            Log::error('Error en la búsqueda de listados: ' . $e->getMessage());

            // Propagar la excepción para que el controlador maneje la respuesta
            throw $e;
        }
    }

}
