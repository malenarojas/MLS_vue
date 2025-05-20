<?php

namespace App\Http\Controllers;

use App\Dtos\Listings\LocationDto;
use App\Http\Controllers\Controller;
use App\Dtos\MarketAnalysisDto;
use App\Http\Requests\MarketAnalisisRequest;
use App\Models\Agent;
use App\Models\ExchangeRate;
use App\Models\Feature;
use App\Models\Listing;
use App\Models\ListingPrice;
use App\Services\MarketAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;



use App\Traits\HasLocationData;


class MarketAnalysisController extends Controller
{
    use HasLocationData;
    protected $MarketAnalysisService;

    public function __construct(MarketAnalysisService $MarketAnalysisService)
    {
        $this->MarketAnalysisService = $MarketAnalysisService;
    }


    public function index(Request $request)
    {

        try {
            Log::info(' Datos recibidos en la request:', $request->all());
            $state_id = request('state_id');
            $province_id = request('province_id'); // Obtener el province_id de la solicitud
            $city_id = request('city_id'); // Obtener el city_id de la solicitud
            $zone_id = request('zone_id'); // Obtener el city_id de la solicitud

            $locationDto = LocationDto::from([
                'state_id' => $state_id,
                'province_id' => $province_id,
                'city_id' => $city_id,
                'zone_id' => $zone_id,

            ]);
            // Obtener los datos de ubicación usando el trait
            $locationData = $this->getLocationData($locationDto);
            Log::info(' location actulizada:', $locationData);

            $data = $this->MarketAnalysisService->index();

            return Inertia::render("MarketAnalysis/index", [
                'options' => $data['options'], // Se envía a `index.vue`
                'provinces' => $locationData['provinces'],
                'cities' => $locationData['cities'],
                'zones' => $locationData['zones'],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado. Intente nuevamente.',
                'details' => $e->getMessage(),
            ]);
        }
    }

    public function showStep($step)
    {
        try {
            $data = $this->MarketAnalysisService->index();

            // Asegurar que solo se rendericen los steps permitidos
            $allowedSteps = ['step_one', 'step_two', 'step_three', 'step_four'];

            if (!in_array($step, $allowedSteps)) {
                abort(404, 'Step not found');
            }

            return Inertia::render("marketanalysis/steps/{$step}", [
                'options' => $data['options'], // Pasar opciones a todas las vistas dentro de steps
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado. Intente nuevamente.',
                'details' => $e->getMessage(),
            ]);
        }
    }



    public function generatePdfcaseone(Request $request)
    {
        $selectedProperties = $request->input('selectedProperties', []);
        $summary = $request->input('summary', []);
        $selectedCase = $request->input('selectedCase', '');
        $selectedCurrency = $request->input('selectedCurrency');



        // Obtener el agente logueado o asignar el agente con ID 5 por defecto
        $loggedInAgent = Auth::user();


        if (!$loggedInAgent) {
            return response()->json(['error' => 'El usuario logueado no está asociado a ningún agente.'], 403);
        }

        // Extraer los IDs de los listings seleccionados
        $ids = collect($selectedProperties)->pluck('id');

        // Consultar datos del modelo Listing
        $listings = Listing::with([
            'location' => function ($query) {
                $query->with(['city', 'zone']);
            },
            'features',
            'listing_information' => function ($query) {
                $query->with([
                    'subtype_property',
                    'marketStatus',
                    'stateProperty',
                    'propertyCategory',
                    'landUse',
                    'landCategory',
                ]);
            },
            'agents.user',
            'agents.office',
        ])->whereIn('id', $ids)->get();

        $enrichedProperties = collect($selectedProperties)->map(function ($property) use ($listings) {
            $listing = $listings->firstWhere('id', $property['id']);
            return [
                'id' => $property['id'] ?? null,
                'title' => $property['title'] ?? null,
                'description' => $property['description'] ?? null,
                'MLSID' => $listing->MLSID ?? null,
                'location' => [
                    'city' => $listing->location->city->name ?? null,
                    'zone' => $listing->location->zone->name ?? null,
                ],
                'prices' => $property['prices'] ?? [],
                //'multimedias' => $this->getMultimediastoday($property['id']),
                'multimedias' => isset($property['id']) ? $this->getMultimediastodaypath($property['id']) : [],
                'first_price' => match ($selectedCurrency['symbol'] ?? null) {
                    'USD', '$' => $property['prices']['price_in_dollars'] ?? $property['prices']['amount'] ?? 'Sin precio',
                    'BOB', 'Bs' => $property['prices']['price_in_bolivianos'] ?? $property['prices']['amount'] ?? 'Sin precio',
                    default => $property['prices']['amount'] ?? 'Sin precio',
                },
                'status' => $property['status'] ?? ($listing->listing_information->marketStatus->name ?? 'N/A'),
                'subtype_name' => $property['subtype_name'] ?? null,
                'usd_per_m2' => $property['usd_per_m2'] ?? null,
                'year_built' => $property['year_built'] ?? null,
                'days_in_market' => $property['days_in_market'] ?? 0,
                'features' => $listing->features ? $listing->features->pluck('name')->toArray() : [],
                'listing_information' => $listing->listing_information ? [
                    'year_construction' => $listing->listing_information->year_construction,
                    'parking_slots' => $listing->listing_information->parking_slots,
                    'total_area' => $listing->listing_information->total_area,
                    'total_rooms' => $listing->listing_information->total_number_rooms,
                    'construction_area_m' => $listing->listing_information->construction_area_m,
                    'number_bathrooms' => $listing->listing_information->number_bathrooms,
                    'number_bedrooms' => $listing->listing_information->number_bedrooms,
                    'market_status' => $listing->listing_information->marketStatus->name ?? null,
                    'state_property' => $listing->listing_information->stateProperty->name ?? null,
                    'property_categories' => $listing->listing_information->propertyCategory
                        ? [$listing->listing_information->propertyCategory->name_properties_categories]
                        : [],
                ] : null,
                'agents' => $listing->agents->map(function ($agent) {
                    return [
                        'id' => $agent->id,
                        'name' => $agent->user->name_to_show ?? null,
                        'email' => $agent->user->email ?? null,
                        'phone_number' => $agent->user->phone_number ?? null,
                        'office_name' => $agent->office->name ?? null,
                        'office_location' => [
                            'city' => $agent->office->city ?? null,
                            'province' => $agent->office->province ?? null,
                        ],
                        'type' => $agent->pivot->type,
                    ];
                }),
            ];
        })->toArray();

        // Datos del agente logueado
        $loggedInAgentData = [
            'id' => $loggedInAgent->id,
            'name' => $loggedInAgent->user->name_to_show ?? null,
            'email' => $loggedInAgent->user->email ?? null,
            'image_url' => $loggedInAgent->image_name
            ? public_path('storage/Agents/' . $loggedInAgent->image_name)
            : public_path('storage/Agents/agent-defaul.gif'),
            'phone_number' => $loggedInAgent->user->phone_number ?? null,
            'office_name' => $loggedInAgent->office->name ?? null,
            'office_location' => [
                'city' => $loggedInAgent->office->city ?? null,
                'province' => $loggedInAgent->office->province ?? null,
            ],
        ];

        $pdf = Pdf::loadView('pdf.marketanalisiscaseone', compact(
            'enrichedProperties',
            'summary',
            'selectedCase',
            'loggedInAgentData',
            'selectedCurrency'
        ))
        ->setPaper('a4', 'portrait') // o 'landscape' si querés horizontal
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        return $pdf->stream('market_analysis_report.pdf'); // o ->download()
    }

    public function  generatePdfcasetwo(Request $request)
    {
        $selectedProperties = $request->input('selectedProperties', []);
        $summary = $request->input('summary', []);
        $selectedCase = $request->input('selectedCase', '');

        // Obtener el agente logueado o asignar el agente con ID 5 por defecto
        $loggedInAgent = Auth::user();


        if (!$loggedInAgent) {
            return response()->json(['error' => 'El usuario logueado no está asociado a ningún agente.'], 403);
        }

        // Extraer los IDs de los listings seleccionados
        $ids = collect($selectedProperties)->pluck('id');

        $listings = Listing::with([
            'location' => function ($query) {
                $query->with(['city', 'zone']);
            },
            'features',
            'listing_information' => function ($query) {
                $query->with([
                    'subtype_property',
                    'marketStatus',
                    'stateProperty',
                    'propertyCategory',
                    'landUse',
                    'landCategory',
                ]);
            },
            'agents.user',
            'agents.office',
        ])->whereIn('id', $ids)->get();

        // Combinar datos del frontend con datos de Listing
        $enrichedProperties = collect($selectedProperties)->map(function ($property) use ($listings) {
            $listing = $listings->firstWhere('id', $property['id']);
            return [
                'id' => $property['id'] ?? null,
                'title' => $property['title'] ?? null,
                'description' => $property['description'] ?? null,
                'MLSID' => $listing->MLSID ?? null,
                'location' => [
                    'city' => $listing->location->city->name ?? null,
                    'zone' => $listing->location->zone->name ?? null,
                ],
                'prices' => $property['prices'] ?? [],
                'multimedias' => isset($property['id']) ? $this->getMultimediaspath($property['id']) : [],
                'first_price' => isset($property['prices'][0]['amount']) ? $property['prices'][0]['amount'] : 'Sin precio',
                'status' => $property['status'] ?? ($listing->listing_information->marketStatus->name ?? 'N/A'),
                'subtype_name' => $property['subtype_name'] ?? null,
                'usd_per_m2' => $property['usd_per_m2'] ?? null,
                'year_built' => $property['year_built'] ?? null,
                'days_in_market' => $property['days_in_market'] ?? 'N/A',
                'features' => $listing->features ? $listing->features->pluck('name')->toArray() : [],
                'listing_information' => $listing->listing_information ? [
                    'year_construction' => $listing->listing_information->year_construction,
                    'parking_slots' => $listing->listing_information->parking_slots,
                    'total_area' => $listing->listing_information->total_area,
                    'total_rooms' => $listing->listing_information->total_number_rooms,
                    'construction_area_m' => $listing->listing_information->construction_area_m,
                    'number_bathrooms' => $listing->listing_information->number_bathrooms,
                    'number_bedrooms' => $listing->listing_information->number_bedrooms,
                    'market_status' => $listing->listing_information->marketStatus->name ?? null,
                    'state_property' => $listing->listing_information->stateProperty->name ?? null,
                    'property_categories' => $listing->listing_information->propertyCategory
                        ? [$listing->listing_information->propertyCategory->name_properties_categories]
                        : [],
                ] : null,
                'agents' => $listing->agents->map(function ($agent) {
                    return [
                        'id' => $agent->id,
                        'name' => $agent->user->name_to_show ?? null,
                        'email' => $agent->user->email ?? null,
                        'phone_number' => $agent->user->phone_number ?? null,
                        'office_name' => $agent->office->name ?? null,
                        'office_location' => [
                            'city' => $agent->office->city ?? null,
                            'province' => $agent->office->province ?? null,
                        ],
                        'type' => $agent->pivot->type,
                    ];
                }),
            ];
        })->toArray();

        // Datos del agente logueado
        $loggedInAgentData = [
            'id' => $loggedInAgent->id,
            'name' => $loggedInAgent->user->name_to_show ?? null,
            'email' => $loggedInAgent->user->email ?? null,
            'image_url' => $loggedInAgent->image_name
            ? public_path('storage/Agents/' . $loggedInAgent->image_name)
            : public_path('storage/Agents/agent-defaul.gif'),
            'phone_number' => $loggedInAgent->user->phone_number ?? null,
            'office_name' => $loggedInAgent->office->name ?? null,
            'office_location' => [
                'city' => $loggedInAgent->office->city ?? null,
                'province' => $loggedInAgent->office->province ?? null,
            ],
        ];

        $pdf = Pdf::loadView('pdf.marketanalisiscasetwo', compact(
            'enrichedProperties',
            'summary',
            'selectedCase',
            'loggedInAgentData'
        ))
        ->setPaper('a4', 'portrait') // o 'landscape' si querés horizontal
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isRemoteEnabled', true);

        return $pdf->stream('market_analysis_report.pdf'); // o ->download()
    }




    public function filterListings2(Request $request)
    {
        //dd($request->all());
        try {
            Log::info('Solicitud recibida:', ['request_data' => $request->all()]);
            // Transformar datos antes de la validación
            $request->merge([
                'start_date' => $this->formatDate($request->input('start_date')),
                'end_date' => $this->formatDate($request->input('end_date')),

            ]);
            // Validar los datos de entrada
            $validated = $request->validate([
                'status' => 'nullable',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'minimum_price' => 'nullable|numeric',
                'maximum_price' => 'nullable|numeric',
                'market_segment' => 'nullable|integer',
                'transaction_type' => 'nullable|integer',
                'property_status' => 'nullable|integer',
                'contract_type' => 'nullable|integer',
                'region_id' => 'nullable|integer',
                'state_id' => 'nullable|integer',
                'province_id' => 'nullable|integer',
                'city_id' => 'nullable|integer',
                'zone_id' => 'nullable|integer',
                'street_name' => 'nullable|string',
                'street_number' => 'nullable|string',
                'address' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:50',
                'only_office' => 'nullable|boolean',
                'property_type_id' => 'nullable|integer',
                'market_status_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'min_rooms' => 'nullable|integer|min:0',
                'max_rooms' => 'nullable|integer|min:0',
                'min_bedrooms' => 'nullable|integer|min:0',
                'max_bedrooms' => 'nullable|integer|min:0',
                'min_toiletroom' => 'nullable|integer|min:0',
                'max_toiletroom' => 'nullable|integer|min:0',
                'min_bathrooms' => 'nullable|integer|min:0',
                'max_bathrooms' => 'nullable|integer|min:0',
                'min_parking' => 'nullable|integer|min:0',
                'max_parking' => 'nullable|integer|min:0',
                'min_total_sqm' => 'nullable|numeric|min:0',
                'max_total_sqm' => 'nullable|numeric|min:0',
                'min_year_built' => 'nullable|integer|min:1900',
                'max_year_built' => 'nullable|integer|min:1900',
                'min_floors' => 'nullable|integer|min:0',
                'max_floors' => 'nullable|integer|min:0',
                'characteristics' => 'nullable',
            ]);
            // Preparar los parámetros para el procedimiento almacenado
            $params = [
                $validated['status'] ?? null, // JSON
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null,
                $validated['minimum_price'] ?? null,
                $validated['maximum_price'] ?? null,
                $validated['market_segment'] ?? null,
                $validated['transaction_type'] ?? null,
                $validated['property_status'] ?? null,
                $validated['contract_type'] ?? null,
                $validated['state_id'] ?? null,
                $validated['province_id'] ?? null,
                $validated['city_id'] ?? null,
                $validated['zone_id'] ?? null,
                $validated['street_name'] ?? null,
                $validated['street_number'] ?? null,
                $validated['address'] ?? null,
                $validated['district'] ?? null,
                $validated['postal_code'] ?? null,
                $validated['property_type_id'] ?? null,
                $validated['market_status_id'] ?? null,
                $validated['category_id'] ?? null,
                $validated['min_rooms'] ?? null,
                $validated['max_rooms'] ?? null,
                $validated['min_bedrooms'] ?? null,
                $validated['max_bedrooms'] ?? null,
                $validated['min_toiletroom'] ?? null,
                $validated['max_toiletroom'] ?? null,
                $validated['min_bathrooms'] ?? null,
                $validated['max_bathrooms'] ?? null,
                $validated['min_parking'] ?? null,
                $validated['max_parking'] ?? null,
                $validated['min_total_sqm'] ?? null,
                $validated['max_total_sqm'] ?? null,
                $validated['min_year_built'] ?? null,
                $validated['max_year_built'] ?? null,
                $validated['min_floors'] ?? null,
                $validated['max_floors'] ?? null,
                $validated['characteristics'] ?? null, // JSON
            ];
            Log::info('Datos validados:', ['validated_data' => $validated]);
            // Ejecutar el procedimiento almacenado
            $query = DB::table('listings as lis')
            ->select('lis.*')
            ->join('listing_prices as lisprice', 'lis.id', '=', 'lisprice.listing_id')
            ->join('listings_information as lisI', 'lis.id', '=', 'lisI.listing_id')
            ->join('locations as loc', 'lis.id', '=', 'loc.listing_id')
            ->join('cities as c', 'loc.city_id', '=', 'c.id')
            ->join('provinces as p_loc', 'c.province_id', '=', 'p_loc.id')
            ->leftJoin('feacture_listing as lf2', 'lf2.listing_id', '=', 'lis.id');

        // *Filtros dinámicos*
        if (!empty($validated['status_id'])) {
            $query->whereRaw('JSON_CONTAINS(?, JSON_ARRAY(lis.status_listing_id))', $validated['status_id']);
        }

        if (!empty($validated['start_date']) && !empty($validated['end_date'])) {
            $query->whereBetween('lis.date_of_listing', [$validated['start_date'], $validated['end_date']]);
        }

        if (!empty($validated['minimum_price'])) {
            $query->where('lisprice.amount', '>=', $validated['minimum_price']);
        }

            $query->where('lisprice.amount', '>=', $validated['minimum_price']);
        if (!empty($validated['minimum_price'])) {
            $query->where('lisprice.amount', '<=', $validated['minimum_price']);
        }

        if (!empty($p_market_segment)) {
            $query->where('lis.area_id', $p_market_segment);
        }

        if (!empty($validated['transaction_type_id'])) {
            $query->where('lis.transaction_type_id', $validated['transaction_type_id']);
        }

        if (!empty($p_property_status)) {
            $query->where('lisI.state_property_id', $p_property_status);
        }

        if (!empty($p_contract_type)) {
            $query->where('lis.contract_type_id', $p_contract_type);
        }

        if (!empty($validated['state_id'])) {
            $query->where('p_loc.state_id',  $validated['state_id']);
        }

        if (!empty($p_province_id)) {
            $query->where('p_loc.id', $p_province_id);
        }

        if (!empty($p_city_id)) {
            $query->where('c.id', $p_city_id);
        }

        if (!empty($p_zone_id)) {
            $query->where('loc.zone_id', $p_zone_id);
        }

        if (!empty($p_street_name)) {
            $query->where('loc.second_address', 'LIKE', "%{$p_street_name}%");
        }

        if (!empty($p_address)) {
            $query->where('loc.first_address', 'LIKE', "%{$p_address}%");
        }

        if (!empty($p_street_number)) {
            $query->where('loc.number', $p_street_number);
        }

        if (!empty($p_district)) {
            $query->where('loc.district', 'LIKE', "%{$p_district}%");
        }

        if (!empty($p_postal_code)) {
            $query->where('loc.zip_code', $p_postal_code);
        }

        // *Filtros adicionales*
        if (!empty($p_property_type_id)) {
            $query->where('lisI.subtype_property_id', $p_property_type_id);
        }

        if (!empty($p_market_status_id)) {
            $query->where('lisI.market_status_id', $p_market_status_id);
        }

        if (!empty($p_category_id)) {
            $query->where('lisI.property_category_id', $p_category_id);
        }

        // *Rango de valores (habitaciones, baños, etc.)*
        if (!empty($p_min_rooms)) {
            $query->where('lisI.total_number_rooms', '>=', $p_min_rooms);
        }

        if (!empty($p_max_rooms)) {
            $query->where('lisI.total_number_rooms', '<=', $p_max_rooms);
        }

        if (!empty($p_min_bedrooms)) {
            $query->where('lisI.number_bedrooms', '>=', $p_min_bedrooms);
        }

        if (!empty($p_max_bedrooms)) {
            $query->where('lisI.number_bedrooms', '<=', $p_max_bedrooms);
        }

        if (!empty($p_min_bathrooms)) {
            $query->where('lisI.number_bathrooms', '>=', $p_min_bathrooms);
        }

        if (!empty($p_max_bathrooms)) {
            $query->where('lisI.number_bathrooms', '<=', $p_max_bathrooms);
        }

        if (!empty($p_min_toiletroom)) {
            $query->where('lisI.number_toiletrooms', '>=', $p_min_toiletroom);
        }

        if (!empty($p_max_toiletroom)) {
            $query->where('lisI.number_toiletrooms', '<=', $p_max_toiletroom);
        }

        if (!empty($p_min_parking)) {
            $query->where('lisI.parking_slots', '>=', $p_min_parking);
        }

        if (!empty($p_max_parking)) {
            $query->where('lisI.parking_slots', '<=', $p_max_parking);
        }

        if (!empty($p_min_total_sqm)) {
            $query->where('lisI.total_area', '>=', $p_min_total_sqm);
        }

        if (!empty($p_max_total_sqm)) {
            $query->where('lisI.total_area', '<=', $p_max_total_sqm);
        }

        if (!empty($p_min_year_built)) {
            $query->where('lisI.year_construction', '>=', $p_min_year_built);
        }

        if (!empty($p_max_year_built)) {
            $query->where('lisI.year_construction', '<=', $p_max_year_built);
        }

        if (!empty($p_min_floors)) {
            $query->where('lisI.plant_numbers', '>=', $p_min_floors);
        }

        if (!empty($p_max_floors)) {
            $query->where('lisI.plant_numbers', '<=', $p_max_floors);
        }

        // *Características (JSON)*
        if (!empty($p_characteristics)) {
            $query->whereRaw('JSON_CONTAINS(?, JSON_ARRAY(lf2.feature_id))', [$p_characteristics]);
        }

        // *Agrupación final*
        $query->groupBy('lis.id');

        // *Ejecución de la consulta*
        $listings = $query->get();
            // Imprimir los datos para ver si fueron recuperados
            Log::info('Datos recuperados: ', ['listings' => $listings]);

            // Comprobar si no hay resultados
            if (empty($listings)) {
                Log::info('No se encontraron resultados en la consulta');
            }

            return response()->json([
                'listings' => $listings->take(250),
                'total_matches' => $listings->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error en la búsqueda por coordenadas:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Ocurrió un error en la búsqueda.');
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function filterListings(Request $request)
    {
        //dd($request->all());
        try {
            Log::info('Solicitud recibida:', ['request_data' => $request->all()]);
            // Transformar datos antes de la validación
            $request->merge([
                'start_date' => $this->formatDate($request->input('start_date')),
                'end_date' => $this->formatDate($request->input('end_date')),

            ]);
            // Validar los datos de entrada
            $validated = $request->validate([
                'status' => 'nullable',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'minimum_price' => 'nullable|numeric',
                'maximum_price' => 'nullable|numeric',
                'market_segment' => 'nullable|integer',
                'transaction_type' => 'nullable|integer',
                'property_status' => 'nullable|integer',
                'contract_type' => 'nullable|integer',
                'region_id' => 'nullable|integer',
                'state_id' => 'nullable|integer',
                'province_id' => 'nullable|integer',
                'city_id' => 'nullable|integer',
                'zone_id' => 'nullable|integer',
                'street_name' => 'nullable|string',
                'street_number' => 'nullable|string',
                'address' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:50',
                'only_office' => 'nullable|boolean',
                'property_type_id' => 'nullable|integer',
                'market_status_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'min_rooms' => 'nullable|integer|min:0',
                'max_rooms' => 'nullable|integer|min:0',
                'min_bedrooms' => 'nullable|integer|min:0',
                'max_bedrooms' => 'nullable|integer|min:0',
                'min_toiletroom' => 'nullable|integer|min:0',
                'max_toiletroom' => 'nullable|integer|min:0',
                'min_bathrooms' => 'nullable|integer|min:0',
                'max_bathrooms' => 'nullable|integer|min:0',
                'min_parking' => 'nullable|integer|min:0',
                'max_parking' => 'nullable|integer|min:0',
                'min_total_sqm' => 'nullable|numeric|min:0',
                'max_total_sqm' => 'nullable|numeric|min:0',
                'min_year_built' => 'nullable|integer',
                'max_year_built' => 'nullable|integer',
                'min_floors' => 'nullable|integer|min:0',
                'max_floors' => 'nullable|integer|min:0',
                'latlngs' => 'nullable|array|min:3',
                'latlngs.*.lat' => 'nullable|numeric',
                'latlngs.*.lng' => 'nullable|numeric',
                'characteristics' => 'nullable',
            ]);
            $params = [
                $validated['status'] ?? null, // JSON
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null,
                $validated['minimum_price'] ?? null,
                $validated['maximum_price'] ?? null,
                $validated['market_segment'] ?? null,
                $validated['transaction_type'] ?? null,
                $validated['property_status'] ?? null,
                $validated['contract_type'] ?? null,
                $validated['state_id'] ?? null,
                $validated['province_id'] ?? null,
                $validated['city_id'] ?? null,
                $validated['zone_id'] ?? null,
                $validated['street_name'] ?? null,
                $validated['street_number'] ?? null,
                $validated['address'] ?? null,
                $validated['district'] ?? null,
                $validated['postal_code'] ?? null,
                $validated['property_type_id'] ?? null,
                $validated['market_status_id'] ?? null,
                $validated['category_id'] ?? null,
                $validated['min_rooms'] ?? null,
                $validated['max_rooms'] ?? null,
                $validated['min_bedrooms'] ?? null,
                $validated['max_bedrooms'] ?? null,
                $validated['min_toiletroom'] ?? null,
                $validated['max_toiletroom'] ?? null,
                $validated['min_bathrooms'] ?? null,
                $validated['max_bathrooms'] ?? null,
                $validated['min_parking'] ?? null,
                $validated['max_parking'] ?? null,
                $validated['min_total_sqm'] ?? null,
                $validated['max_total_sqm'] ?? null,
                $validated['min_year_built'] ?? null,
                $validated['max_year_built'] ?? null,
                $validated['min_floors'] ?? null,
                $validated['max_floors'] ?? null,
                $validated['characteristics'] ?? null, // JSON
            ];

            $polygonWKT = null;
            if (!empty($validated['latlngs'])) {
                $polygonWKT = $this->convertToPolygonWKT($validated['latlngs']);
            }
            $params[] = $polygonWKT;

            Log::info('Datos validados:', ['validated_data' => $validated]);
            // Ejecutar el procedimiento almacenado
            $listings = DB::select(
                'CALL FilterListings( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)',
                $params
            );

            // Imprimir los datos para ver si fueron recuperados
            Log::info('Datos recuperados: ', ['listings' => $listings]);

            // Comprobar si no hay resultados
            if (empty($listings)) {
                Log::info('No se encontraron resultados en la consulta');
            }
            // Convertir el resultado a una colección para facilitar el mapeo
            $listingsCollection = collect($listings);

            // Transformar los listados según los campos requeridos
            $response  = $listingsCollection->map(function ($listing) {
                return [
                    'id'          => $listing->id ?? null,
                    'title'       => $listing->title ?? null,
                    'description' => $listing->description_website ?? null,
                    'fecha_de_carga' => $listing->first_upload_date ?? null,
                    'MLSID' => $listing->MLSID ?? null,
                    'key' => $listing->key ?? null,
                    //'subtypeproperty'=>$this->getSubtypepropertyname($listing->listings_information),
                    'status'      => $this->getStatusName($listing->status_listing_id),
                    'status_id' => $listing->status_listing_id,
                    'prices'      => $this->getListingPrices($listing->id),
                    'location'    => $this->getListingLocation($listing->id),
                    'multimedias' => $this->getMultimedias($listing->id),
                    // Nuevo: días en el mercado
                    'days_in_market' => $this->getDaysInMarket($listing),
                    'address' => $this->getFullLocation($listing->id),
                    // Nuevo: precio USD/m2
                    'usd_per_m2' => $this->getUsdPerM2($listing->id),
                    // Nuevo: Subtipo de la propiedad
                    'subtype_name'  => $this->getSubtypeName($listing->id),
                    // Nuevo: Año de construcción
                    'year_built'    => $this->getYearBuilt($listing->id),
                ];
            });
            // Cantidad total de coincidencias (sin limitar)
            $totalCount = $response->count();

            // Tomar solo los primeros 250 registros
            $response  = $response->take(250);


            return response()->json([
                'listings' => $response,
                'total_matches' => $totalCount,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error en la búsqueda por coordenadas:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Ocurrió un error en la búsqueda.');
        }
    }


    private function formatDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // Devuelve null si hay un error
        }
    }


    private function getListingLocation($listingId): array
    {
        if (!$listingId) {
            return [];
        }

        // Busca la ubicación en la tabla 'locations'
        $location = DB::table('locations')
            ->where('listing_id', $listingId)
            ->select('latitude', 'longitude')
            ->first();

        // Si no existe registro en 'locations', retornamos un arreglo vacío
        if (!$location) {
            return [];
        }

        return [
            'latitude'  => $location->latitude,
            'longitude' => $location->longitude,
        ];
    }
    private function getStatusName($statusListingId): string
    {
        if (!$statusListingId) {
            return 'Estado desconocido';
        }

        $status = DB::table('status_listings')->where('id', $statusListingId)->first();

        return $status->name ?? 'Estado desconocido';
    }


    private function getSubtypepropertyname($subtypepropertyId): string
    {
        if (!$subtypepropertyId) {
            return 'Estado desconocido';
        }

        $subtypeproperty = DB::table('subtype_properties')->where('id', $subtypepropertyId)->first();

        return $subtypeproperty->name ?? 'Estado desconocido';
    }
    private function getListingPrices($listingId): ?array
    {
        if (!$listingId) {
            return null;
        }

        $latestExchangeRate = ExchangeRate::orderBy('id', 'desc')->first();

        $usdToBobRate = $latestExchangeRate?->amount ?? 6.96;

        $price = ListingPrice::with('listing')
                    ->where('listing_id', $listingId)
                    ->orderBy('id', 'asc')
                    ->first();

        // Si no se encontró ningún registro, retornar null
        if (!$price) {
            return null;
        }
        //    de lo contrario, tomar el amount tal cual.
        $priceInBolivianos = ($price->currency_id === 2)
            ? ($price->amount * $usdToBobRate)
            : $price->amount;
        /*$priceInDollars = ($price->currency_id === 1)
            ? $price->amount
            : ($price->amount / $usdToBobRate);*/

        // 6. Retornar un array con la información solicitada
        return [
            'id'                 => $price->id,
            'amount'             => $price->amount,
            'currency_id'        => $price->currency_id,
            'price_in_bolivianos'=> $priceInBolivianos,
            'price_in_dollars'   => $price->price_in_dollars,    // Usa getPriceInDollarsAttribute()
        ];
    }
    private function getMultimedias($listingId): array
    {
        if (!$listingId) {
            return [];
        }

        $multimedias = DB::table('multimedias')
            ->where('listing_id', $listingId)
            ->leftJoin('multimedia_types', 'multimedias.multimedia_type_id', '=', 'multimedia_types.id')
            ->select('multimedias.id', 'multimedias.link', 'multimedias.description', 'multimedia_types.name as type')
            ->orderByDesc('is_default') // Primero las imágenes con is_default = 1
            ->orderBy('multimedias.id') // Si no hay is_default = 1, toma la primera disponible
            ->limit(1) // Solo una imagen
            ->get();

        return $multimedias->map(function ($media) {
            return [
                'id' => $media->id,
                'link' => $media->link ? Storage::disk('public')->url($media->link) : null,
                'description' => $media->description,
                'type' => $media->type ?? 'Tipo desconocido',
            ];
        })->toArray();
    }
    private function getMultimediaspath($listingId): array
    {
        if (!$listingId) {
            return [];
        }

        $multimedias = DB::table('multimedias')
            ->where('listing_id', $listingId)
            ->leftJoin('multimedia_types', 'multimedias.multimedia_type_id', '=', 'multimedia_types.id')
            ->select('multimedias.id', 'multimedias.link', 'multimedias.description', 'multimedia_types.name as type')
            ->orderByDesc('is_default') // Primero las imágenes con is_default = 1
            ->orderBy('multimedias.id') // Si no hay is_default = 1, toma la primera disponible
            ->limit(1) // Solo una imagen
            ->get();

        return $multimedias->map(function ($media) {
            return [
                'id' => $media->id,
                'link' => $media->link ? public_path('storage/' . $media->link) : null,
                'description' => $media->description,
                'type' => $media->type ?? 'Tipo desconocido',
            ];
        })->toArray();
    }


    private function getMultimediastoday($listingId): array
    {
        if (!$listingId) {
            return [];
        }

        $multimedias = DB::table('multimedias')
            ->where('listing_id', $listingId)
            ->leftJoin('multimedia_types', 'multimedias.multimedia_type_id', '=', 'multimedia_types.id')
            ->select('multimedias.id', 'multimedias.link', 'multimedias.description', 'multimedia_types.name as type')
            ->get();

        return $multimedias->map(function ($media) {
            return [
                'id' => $media->id,
                'link' => $media->link ? Storage::url($media->link) : null,
                'description' => $media->description,
                'type' => $media->type ?? 'Tipo desconocido',
            ];
        })->toArray();
    }
    private function getMultimediastodaypath($listingId): array
    {
        if (!$listingId) {
            return [];
        }

        $multimedias = DB::table('multimedias')
            ->where('listing_id', $listingId)
            ->leftJoin('multimedia_types', 'multimedias.multimedia_type_id', '=', 'multimedia_types.id')
            ->select('multimedias.id', 'multimedias.link', 'multimedias.description', 'multimedia_types.name as type')
            ->get();

        return $multimedias->map(function ($media) {
            return [
                'id' => $media->id,
                'link' => $media->link ? public_path('storage/' . $media->link) : null,
                'description' => $media->description,
                'type' => $media->type ?? 'Tipo desconocido',
            ];
        })->toArray();
    }


    private function getSubtypeName($listingId): string
    {
        if (!$listingId) {
            return 'N/A';
        }

        $subtypePropertyId = DB::table('listings_information')
            ->where('listing_id', $listingId)
            ->value('subtype_property_id');

        if (!$subtypePropertyId) {
            return 'N/A'; // No hay subtipo
        }

        // 2) Con ese ID, tomamos el 'name' de la tabla `subtype_properties`
        $subtypeName = DB::table('subtype_properties')
            ->where('id', $subtypePropertyId)
            ->value('name');

        return $subtypeName ?: 'N/A';
    }


    private function getYearBuilt($listingId): string
    {
        if (!$listingId) {
            return 'N/A';
        }

        $year = DB::table('listings_information')
            ->where('listing_id', $listingId)
            ->value('year_construction');

        // Si no hay registro o es null, retornamos 'N/A'
        if (!$year) {
            return 'N/A';
        }

        return (string)$year;
    }


    public function searchByCoordinates(Request $request)
    {

        try {
            // Validar el array de coordenadas
            $validated = $request->validate([
                'latlngs' => 'required|array|min:3',
                'latlngs.*.lat' => 'required|numeric',
                'latlngs.*.lng' => 'required|numeric',
            ]);

            $coordinates = $validated['latlngs'];
            $polygonWKT = $this->convertToPolygonWKT($coordinates);

            // Consultar localizaciones dentro del rango
            $locations = Location::select('id', 'latitude', 'longitude', 'listing_id')
                ->whereRaw("ST_Contains(ST_GeomFromText(?), POINT(longitude, latitude))", [$polygonWKT])
                ->get();
            $totalLocations = $locations->count();

            $limitedLocations = $locations->take(250);

            // Preparar los datos de respuesta
            $response = $limitedLocations->map(function ($location) {
                $listing = DB::table('listings')
                    ->select([
                        'id',
                        'title',
                        'description_website',
                        'status_listing_id',
                        'MLSID',
                        'first_upload_date',
                        'date_of_listing',
                        'contract_end_date',
                        'cancellation_date',
                    ])
                    ->where('id', $location->listing_id)
                    ->first(); // retorna un objeto stdClass con esas columnas

                return [
                    'id' => $location->id,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'listing' => [
                        'id' => $location->listing_id ?? null,
                        'title'       => $listing->title ?? null,
                        'description' => $listing->description_website ?? null,
                        'fecha_de_carga' => $listing->first_upload_date ?? null,
                        'MLSID' => $listing->MLSID ?? null,
                        'prices' => $this->getListingPrices($location->listing_id),
                        'multimedias' => $this->getMultimedias($location->listing_id),
                        'status_id' => $listing->status_listing_id,
                        'status' => $this->getStatusName($location->listing->status_listing_id),
                        // Nuevo: días en el mercado
                        'days_in_market' => $this->getDaysInMarket($listing),
                        'address' => $this->getFullLocation($location->listing_id),
                        // Nuevo: precio USD/m2
                        'usd_per_m2' => $this->getUsdPerM2($location->listing_id),
                        // Nuevo: Subtipo de la propiedad
                        'subtype_name'  => $this->getSubtypeName($location->listing_id),
                        // Nuevo: Año de construcción
                        'year_built'    => $this->getYearBuilt($location->listing_id),
                    ],
                ];
            });

            return response()->json([
                'listings' => $response,
                'total_matches' => $totalLocations,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error en la búsqueda por coordenadas:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Ocurrió un error en la búsqueda.');
        }
    }


    private function convertToPolygonWKT(array $coordinates): string
    {
        $points = implode(', ', array_map(function ($coordinate) {
            return "{$coordinate['lng']} {$coordinate['lat']}";
        }, $coordinates));

        $points .= ", {$coordinates[0]['lng']} {$coordinates[0]['lat']}";

        return "POLYGON(($points))";
    }
    private function getDaysInMarket($listing): int
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
                // Otros estados (Borrador=1, propuesto => 0
                return 0;
        }
    }

    private function getListingPrice($listingId): ?float
    {
        if (!$listingId) {
            return null;
        }

        // Verificar si el estado del listing es 'vendido' (estado 8)
        $isSold = DB::table('listings')
            ->where('id', $listingId)
            ->value('status_listing_id'); // Cambia 'status_id' si tu columna tiene otro nombre

        // Si está vendido, obtener el precio de la transacción
        if ($isSold == 8) {
            $transactionPrice = DB::table('transactions')
                ->where('listing_id', $listingId)
                ->orderBy('id', 'desc') // Cambia 'id' por el criterio que prefieras (p.ej., fecha)
                ->value('current_listing_price');

            if ($transactionPrice) {
                return (float) $transactionPrice;
            }
        }

        // Obtener el primer 'amount' de listing_prices
        $price = DB::table('listing_prices')
            ->where('listing_id', $listingId)
            ->orderBy('id') // o algún otro criterio, p.ej. created_at
            ->value('amount'); // retira el '->value(...)' para obtener un solo campo

        return $price ? (float)$price : null;
    }

    private function getUsdPerM2($listingId): float
    {
        if (!$listingId) {
            return 0.0;
        }

        // Tomar el área
        $area = DB::table('listings_information')
            ->where('listing_id', $listingId)
            ->value('total_area');
        if (!$area || $area <= 0) {
            return 0.0;
        }

        // Reutiliza getListingPrice para tomar el primer precio
        $price = $this->getListingPrice($listingId);
        if (!$price || $price <= 0) {
            return 0.0;
        }

        // Calcula
        $usdPerM2 = $price / $area;

        return round($usdPerM2, 2);
    }
    public function getFullLocation(int $listingId): ?array
    {
        // Buscamos la ubicación del listing con sus relaciones de ciudad y zona
        $location = Location::where('listing_id', $listingId)
            ->with(['city', 'zone'])
            ->first();

        if (!$location) {
            return null;
        }
        return [
            'province'     => optional(optional($location->city)->province)->name,
            'city'         => optional($location->city)->name,
            'zone'         => optional($location->zone)->name,
        ];
    }
    public function getLoggedAgentOfficeLocation(): ?array
    {
        $agent = Auth::user();

        if (!$agent || !$agent->office) {
            return null;
        }

        return [
            'latitude'  => $agent->office->latitude,
            'longitude' => $agent->office->longitude,
        ];
    }


    public function getAlllisting(Request $request)
    {
        try {

            $key = $request->query('listing');

            // 🔹 Buscar el listing con ese MLSID
            $listing = Listing::where('key', $key)
                ->with([
                    'listing_information' => function ($query) {
                        $query->select(
                            'id',
                            'number_bedrooms',
                            'number_bathrooms',
                            'listing_id',
                            'subtype_property_id'
                        )->with(['subtype_property:id,name']);
                    },
                    //'price:amount',
                    'transaction_type:id,name',
                    'status_listing:id,name',
                    'area:id,name',
                    'agents' => function ($query) {
                        $query->select('agents.id', 'agents.office_id', 'agents.user_id', 'agents.image_name') // Necesitás image_name para el accessor
                            ->with([
                                'office:id,office_id,name',
                                'user:id,name_to_show,email,phone_number'
                            ]);
                    },

                    'location' => function ($query) {
                        $query->select('id', 'show_addres_on_website', 'listing_id', 'city_id', 'zone_id', 'latitude', 'longitude')
                            ->with([
                                'zone:id,name',
                                'city' => function ($query) {
                                    $query->select('id', 'name', 'province_id')
                                        ->with(['province:id,name']);
                                },
                            ]);
                    },
                    'default_imagen:id,link,listing_id',
                    'contract_type:id,name',
                    'features' => function ($query) {
                        $query->select('features.id', 'features.name', 'features.feature_id')
                            ->with(['allChildren:id,name,feature_id']);
                    }
                ])
                ->first();

            if (!$listing) {
                return redirect()->back()->with('error', 'Listing no encontrado');
            }

            $allFeatures = Feature::select('id', 'name', 'feature_id')->get();
            $listing->all_features = $allFeatures;
            $listing->days_in_market = $this->getDaysInMarket($listing);
            $listing->multimedias = $this->getMultimediastoday($listing->id);
            $listing->agents->each(function ($agent) {
                $agent->listing_count = $agent->listing_count; // Esto activa el accesor y lo deja como propiedad
            });
            $listing->prices = $this->getListingPrices($listing->id);
            return Inertia::render('MarketAnalysis/Show', [
                'listing' => $listing, // Pasar el listing como prop
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado. Intente nuevamente.',
                'details' => $e->getMessage(),
            ]);
        }
    }
}
