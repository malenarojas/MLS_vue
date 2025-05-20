<?php


namespace App\Http\Controllers;

use App\Dtos\Agents\OfficeDto;
use App\Dtos\Listings\LocationDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\OfficeController\StoreOfficeRequest;
use App\Http\Requests\OfficeController\UpdateOfficeRequest;
use App\Models\Achievement;
use App\Models\AchievementOffice;
use App\Models\AchievementOfficePivot;
use App\Models\City;
use App\Models\Office;
use App\Models\Region;
use App\Models\State;
use App\Models\Model;
use App\Models\SocialNetwork;
use Illuminate\Http\Request;
use App\Services\Agents\OfficeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Traits\HasLocationData;
use App\Services\ImageService;

class OfficeController extends Controller
{
    use HasLocationData;


    protected OfficeService $officeService;
    protected ImageService $imageService;


    public function __construct(OfficeService $officeService,ImageService $imageService)
    {
        $this->officeService = $officeService;
        $this->imageService= $imageService;

    }
    /**
     * Listar todas las oficinas.
     *
     * @group Offices
     * @authenticated
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "region_id": 2,
     *             "office_id": 1001,
     *             "name": "Office Name",
     *             "city": "City Name",
     *             "province": "Province Name",
     *             "country": "Country Name",
     *             "latitude": 12.345678,
     *             "longitude": 98.765432,
     *             "first_updated_to_web": "2024-01-01 00:00:00",
     *             "access_ilist_net": 1,
     *             "succeed_certified": 0,
     *             "is_regional_office": 1,
     *             "is_satellite_office": 0,
     *             "first_year_licenced": "2024-01-01",
     *             "is_commercial": 1,
     *             "is_collection": 0,
     *             "date_time_stamp": "2024-01-01 00:00:00",
     *             "active_office": 1,
     *             "office_iconnect_id": "1234ID",
     *             "office_intl_id": "5678INTL",
     *             "macro_office": "Macro Name",
     *             "office_type": "Regular Office"
     *         }
     *     ]
     * }
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'active_office' => $request->input('active_office'),
                'search' => $request->input('search'),
            ];

            Log::info('Filtros recibidos en index de oficinas:', $filters);

            if (
                (!isset($filters['active_office']) || $filters['active_office'] === 'all') &&
                (empty($filters['search']))
            ) {
                Log::info('Filtros recibidos en index para all  de oficinas:', $filters);
                $offices = $this->officeService->getAll();
            } else {
                Log::info('Filtros recibidos en index de oficinas:', $filters);
                $offices = $this->officeService->getOfficesWithFilters($filters);
            }

            return Inertia::render('Offices/index', [
                'offices' => $offices,
                'filters' => $filters,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener oficinas: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Error al obtener oficinas.',
                'details' => $e->getMessage(),
            ]);
        }
    }

    public function getAllOffice()
    {
        $offices = $this->officeService->getAll();
        return response()->json($offices, 200);
    }


    /**
     * Crear una nueva oficina.
     *
     * @group Offices
     * @authenticated
     *
     * @bodyParam region_id integer required ID de la región asociada. Example: 1
     * @bodyParam office_id integer required ID único de la oficina. Example: 1001
     * @bodyParam name string required Nombre de la oficina. Example: Office Name
     * @bodyParam city string required Ciudad de la oficina. Example: City Name
     * @bodyParam province string required Provincia de la oficina. Example: Province Name
     * @bodyParam country string required País de la oficina. Example: Country Name
     * @bodyParam latitude numeric Latitud de la oficina. Example: 12.345678
     * @bodyParam longitude numeric Longitud de la oficina. Example: 98.765432
     * @bodyParam first_updated_to_web date Fecha de la primera actualización de la oficina en la web. Example: 2023-01-01
     * @bodyParam access_ilist_net boolean Indica si la oficina tiene acceso a iList Net. Example: true
     * @bodyParam succeed_certified boolean Indica si la oficina está certificada con éxito. Example: false
     * @bodyParam is_regional_office integer Indica si es una oficina regional (1 para sí, 0 para no). Example: 1
     * @bodyParam is_satellite_office boolean Indica si es una oficina satélite. Example: false
     * @bodyParam first_year_licenced date Año en el que se licenció por primera vez. Example: 2022-01-01
     * @bodyParam is_commercial boolean Indica si la oficina es comercial. Example: true
     * @bodyParam is_collection boolean Indica si es una oficina de colección. Example: false
     * @bodyParam date_time_stamp date Fecha y hora del sello de tiempo. Example: 2023-11-20
     * @bodyParam active_office boolean Indica si la oficina está activa. Example: true
     * @bodyParam office_iconnect_id string ID de la oficina en iConnect. Example: 12345O
     * @bodyParam office_intl_id string ID internacional de la oficina. Example: 67890INTL
     * @bodyParam macro_office string Macro oficina a la que pertenece. Example: Main Office
     * @bodyParam office_type string Tipo de oficina. Example: Regular Office
     *
     * @response 201 {
     *     "message": "Oficina creada correctamente",
     *     "data": {
     *         "id": 1,
     *         "region_id": 2,
     *         "office_id": 1001,
     *         "name": "Office Name",
     *         "city": "City Name",
     *         "province": "Province Name",
     *         "country": "Country Name",
     *         "latitude": 12.345678,
     *         "longitude": 98.765432,
     *         "first_updated_to_web": "2023-01-01",
     *         "access_ilist_net": true,
     *         "succeed_certified": false,
     *         "is_regional_office": 1,
     *         "is_satellite_office": false,
     *         "first_year_licenced": "2022-01-01",
     *         "is_commercial": true,
     *         "is_collection": false,
     *         "date_time_stamp": "2023-11-20",
     *         "active_office": true,
     *         "office_iconnect_id": "12345O",
     *         "office_intl_id": "67890INTL",
     *         "macro_office": "Main Office",
     *         "office_type": "Regular Office",
     *         "created_at": "2023-11-20T00:00:00.000000Z",
     *         "updated_at": "2023-11-20T00:00:00.000000Z"
     *     }
     * }
     */

    public function getFilteredOffices(Request $request)
    {
        try {
            $filters = [
                'active_office' => $request->input('active_office'),
                'search' => $request->input('search'),
            ];

            $offices = $this->officeService->getOfficesWithFilters($filters);

            return Inertia::render('Offices/index', [
                'offices' => $offices,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al obtener oficinas filtradas.',
                'details' => $e->getMessage(),
            ]);
        }
    }


    public function store(StoreOfficeRequest $request): RedirectResponse
    {
        $dto = OfficeDto::from($request->validated());
        $this->officeService->create($dto);

        return redirect()->back()->with('success', 'Oficina creada correctamente');
    }
    public function edit(Request $request)
    {
        Log::info("🔍 Request recibido en edit (office):", ['query_params' => $request->query()]);

        $id = $request->query('id');

        Log::info("🆔 ID de la oficina recibido:", ['id' => $id]);
        $state_id = request('state_id');
        $province_id = request('province_id');
        $city_id = request('city_id');
        $zone_id = request('zone_id');

        $locationDto = LocationDto::from([
            'state_id' => $state_id,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'zone_id' => $zone_id,
        ]);
        $locationData = $this->getLocationData($locationDto);
        Log::info(' location actulizada:', $locationData);

        $office = Office::with(['regions', 'agents', 'socialNetworks','achievementoffices' ]) // agrega relaciones si las necesitas
            ->find($id);


        $formData = [
            'state' => State::select('id', 'name')->get(),
             'achievement' => AchievementOffice::get(),
            // ... más si necesitás
        ];

        return Inertia::render("Offices/edit", [
            'office' => $office,
            'formData' => $formData,
            'provinces' => $locationData['provinces'],
            'cities' => $locationData['cities'],
            'zones' => $locationData['zones'],
        ]);
    }

    /**
     * Mostrar los detalles de una oficina específica.
     *
     * @group Offices
     * @authenticated
     *
     * @urlParam id integer required El ID de la oficina. Example: 1
     *
     * @response {
     *     "id": 1,
     *     "region_id": 2,
     *     "office_id": 1001,
     *     "name": "Office Name",
     *     "city": "City Name",
     *     "province": "Province Name",
     *     "country": "Country Name",
     *     "latitude": 12.345678,
     *     "longitude": 98.765432,
     *     "first_updated_to_web": "2023-01-01",
     *     "access_ilist_net": true,
     *     "succeed_certified": false,
     *     "is_regional_office": 1,
     *     "is_satellite_office": false,
     *     "first_year_licenced": "2022-01-01",
     *     "is_commercial": true,
     *     "is_collection": false,
     *     "date_time_stamp": "2023-11-20",
     *     "active_office": true,
     *     "office_iconnect_id": "12345O",
     *     "office_intl_id": "67890INTL",
     *     "macro_office": "Main Office",
     *     "office_type": "Regular Office",
     *     "created_at": "2023-11-20T00:00:00.000000Z",
     *     "updated_at": "2023-11-20T00:00:00.000000Z"
     * }
     */

    public function show(String $id): JsonResponse
    {
        $office = $this->officeService->findById($id);
        return response()->json($office);
    }
    /**
     * Actualizar los detalles de una oficina específica.
     *
     * @group Offices
     * @authenticated
     *
     * @urlParam id integer required El ID de la oficina a actualizar. Example: 1
     *
     * @bodyParam region_id integer required ID de la región asociada. Example: 1
     * @bodyParam office_id integer required ID único de la oficina. Example: 1001
     * @bodyParam name string required Nombre de la oficina. Example: Office Name
     * @bodyParam city string required Ciudad de la oficina. Example: City Name
     * @bodyParam province string required Provincia de la oficina. Example: Province Name
     * @bodyParam country string required País de la oficina. Example: Country Name
     * @bodyParam latitude numeric Latitud de la oficina. Example: 12.345678
     * @bodyParam longitude numeric Longitud de la oficina. Example: 98.765432
     * @bodyParam first_updated_to_web date Fecha de la primera actualización en la web. Example: 2023-01-01
     * @bodyParam access_ilist_net boolean Indica si tiene acceso a ilist_net. Example: true
     * @bodyParam succeed_certified boolean Indica si está certificado. Example: false
     * @bodyParam is_regional_office integer Es una oficina regional (1 para sí, 0 para no). Example: 1
     * @bodyParam is_satellite_office boolean Es una oficina satélite. Example: false
     * @bodyParam first_year_licenced date Año en el que se obtuvo la licencia. Example: 2022-01-01
     * @bodyParam is_commercial boolean Indica si es comercial. Example: true
     * @bodyParam is_collection boolean Indica si es de colección. Example: false
     * @bodyParam date_time_stamp date Marca de tiempo de la oficina. Example: 2023-11-20
     * @bodyParam active_office boolean Indica si la oficina está activa. Example: true
     * @bodyParam office_iconnect_id string ID de iConnect de la oficina. Example: 12345O
     * @bodyParam office_intl_id string ID internacional de la oficina. Example: 67890INTL
     * @bodyParam macro_office string Macro oficina asociada. Example: Main Office
     * @bodyParam office_type string Tipo de oficina. Example: Regular Office
     *
     * @response {
     *     "id": 1,
     *     "region_id": 2,
     *     "office_id": 1001,
     *     "name": "Office Name",
     *     "city": "City Name",
     *     "province": "Province Name",
     *     "country": "Country Name",
     *     "latitude": 12.345678,
     *     "longitude": 98.765432,
     *     "first_updated_to_web": "2023-01-01",
     *     "access_ilist_net": true,
     *     "succeed_certified": false,
     *     "is_regional_office": 1,
     *     "is_satellite_office": false,
     *     "first_year_licenced": "2022-01-01",
     *     "is_commercial": true,
     *     "is_collection": false,
     *     "date_time_stamp": "2023-11-20",
     *     "active_office": true,
     *     "office_iconnect_id": "12345O",
     *     "office_intl_id": "67890INTL",
     *     "macro_office": "Main Office",
     *     "office_type": "Regular Office",
     *     "created_at": "2023-11-20T00:00:00.000000Z",
     *     "updated_at": "2023-11-20T00:00:00.000000Z"
     * }
     */


    public function update(UpdateOfficeRequest $request, String $id)
    {
        Log::info('🔥 Entró al método update');
        $dto = OfficeDto::from($request->validated());
        Log::info('📝 Datos recibidos para actualizar oficina:', $dto->toArray());
        foreach ($request->input('socialNetworks', []) as $networkData) {
            SocialNetwork::updateOrCreate(
                [
                    'id' => $networkData['id'] ?? null, // Buscar por ID si existe
                    'office_id' =>$request->input('id')// Asegurar que se relacione con el agente correcto
                ],
                [
                    'name' => $networkData['name'],
                    'state' => $networkData['state'] ?? 0, // Estado por defecto 0 si no está definido
                    'url' => $networkData['url'] ?? '', // URL vacía si no se proporciona
                ]
            );
        }
        foreach ($request->input('achievementoffices', []) as $achievementoffices) {
            AchievementOfficePivot::updateOrCreate(
                [
                    'id' => $achievementoffices['id'] ?? null, // Buscar por ID si existe
                    'office_id' =>$request->input('id')// Asegurar que se relacione con el agente correcto
                ],
                [
                    'achievement_date' => $achievementoffices['achievement_date'] ?? null,
					'enable_achievement' => $achievementoffices['enable_achievement'] ?? 0,
					'achievement_id' => $achievementoffices['achievement_id'],
                ]
            );
        }
        if ($request->filled('image_data')) {
            Log::info('Se recibió una imagen en formato Base64.');

            $currentFileName = $dto->image; // Nombre de la imagen actual

            $imageName = $this->imageService->updateuploadAndReplaceImageFromBase64(
                $request->input('image_data'), // Imagen en Base64
                'oficinas', // Carpeta de almacenamiento
                null, // Generar automáticamente un nombre
                595, // Ancho deseado
                250, // Alto deseado
                $currentFileName // Nombre de la imagen actual
            );

            // Asignar el nombre de la nueva imagen al DTO
            $dto->image = $imageName;
        }

        $office = $this->officeService->update($id, $dto);
        Log::info('✅ Oficina actualizada correctamente:', ['id' => $id, 'name' => $office->name]);
        return redirect()->route('offices.edit', ['id' => $id])
        ->with('success', 'Oficina actualizada correctamente');

    }

    /**
     * Eliminar una oficina.
     *
     * @group Offices
     * @authenticated
     * @urlParam id required El ID de la oficina. Example: 1
     * @response 204
     */
    public function destroy(string $id)
    {

        $this->officeService->delete($id);
        return response()->json(['message' => 'Office deleted successfully.']);
    }
}
