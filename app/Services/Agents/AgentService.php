<?php

namespace App\Services\Agents;

use App\Dtos\Agents\AchievementUserDto;
use App\Dtos\Agents\AgentDto;
use App\Dtos\Agents\AreaSpecialityUserDto;
use App\Dtos\Agents\SocialNetworkDto;
use App\Models\Agent;
use App\Models\AchievementUser;
use App\Models\SocialNetwork;
use App\Models\AreaSpecialityUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Dtos\Agents\AgentParamsDto;
use App\Models\Office;
use App\Models\TypeDocumentationContact;
use App\Models\User;
use App\Services\ImageService;
use App\Services\Listings\DocumentationTypeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Utils\StringGenerateKey;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;




class AgentService
{

    public function __construct(
        private AchievementService $AchievementService,
        private AreaService $AreaService,
        private AreaSpecialityService $AreaSpecialityService,
        private CustomerPreferenceService $CustomerPreferenceService,
        private OfficeService $officeService,
        private RegionService $regionService,
        private QualificationService $QualificationService,
        private RemaxTitleService $RemaxTitleService,
        private RemaxtitletoshowService $RemaxtitletoshowService,
        private SocialNetworkService $SocialNetworkService,
        private SpecialityService $specialityService,
        private TeamStatusService $teamStatusService,
        private UserTypeService $userTypeService,
        private LanguageService $LanguageService,
        private CityService $CityService,
        private AreaSpecialityUserService $AreaSpecialityUserService,
        private AchievementUserService $achievementUserService,
        private AgentStatusService $agentstatusService,
        private DocumentationTypeService $documentationtypeService,
        private ImageService $ImageService,

    ) {}
    public function getAllWithPagination(int $perPage = 10): Collection
    {
        return Agent::paginate($perPage);
    }

    public function getAll(): Collection
    {
        $agente = Agent::with(
            'user.roles',
            'user',
            'office',
            'region',
            'goals',
            'socialNetworks',
            'qualification',

        )->get();

        $agente = Agent::with('user.roles')->get();

        $roles = $agente;

        return $agente;
    }

    public function getAllWithUser(): Collection
    {
        return Agent::select('id', 'user_id')
            ->with(['user:id,first_name,last_name'])
            ->limit(10)
            ->get();
    }

    public function filterAgents(array $filters)
    {
        // $query = Agent::query();
        // $fillable = (new Agent)->getFillable();

        // foreach ($filters as $key => $value) {
        //     if (in_array($key, $fillable)) {
        //         $query->where($key, $value);
        //     }
        // }

        return Agent::select(
            'agents.id',
            'agents.office_id',
            'agents.user_id',
            'agents.agent_status_id',
            'agents.region_id',
            'users.name_to_show',
            'agents.image_name'
        )
            ->join('users', 'agents.user_id', '=', 'users.id')
            // ->where('agents.office_id', $filters['office_id'])
            ->when(isset($filters['office_id']), function ($query) use ($filters) {
                $query->where('office_id', $filters['office_id']);
            })
            ->where('agents.agent_status_id', 1)
            ->orderBy('users.name_to_show', 'asc')
            ->get();
    }
    public function getFormOptions(): array
    {
        return [
            'offices' => $this->officeService->getAll(), // Lista de oficinas
            'regions' => $this->regionService->getRegionsWithOfficesAndTeamStatuses(), // Regiones con oficinas y estados
            'team_statuses' => $this->teamStatusService->getAll(), // Lista de estados de equipoq
            'goals' => [], // Lista de metas
            //'social_networks' => $this->SocialNetworkService->getAll(), // Redes sociales disponibles
            //'qualifications' => $this->QualificationService->getAll(), // Lista de calificaciones
            'user_types' => $this->userTypeService->getAll(), // Tipos de usuario para User
            'remax_titles' => $this->RemaxTitleService->getAll(), // Títulos Remax
            'remax_titles_to_show' => $this->RemaxtitletoshowService->getAll(), // Títulos Remax
            'language' => $this->LanguageService->getall(),
            'customer_preference' => $this->CustomerPreferenceService->getAll(), // Títulos para mostrar
            // Áreas con sus especialidades relacionadas
            'Achievements' => $this->AchievementService->getall() ?? [],
            'areas' => $this->AreaService->getAreasWithSpecialities(),
            'specialities' => $this->specialityService->getAll() ?? [], // Todas las especialidades
            'area_specialities' => $this->AreaSpecialityService->getAll(), // Áreas y especialidades relacionadas
            'city' => $this->CityService->getAll(), // Áreas y especialidades relacionadas
            //'AchievementUser' => $this->achievementUserService->getAll(),
            //'area_specialities_user' => $this->AreaSpecialityUserService->getAll(),
            'agent_status' => $this->agentstatusService->getAll(),
            'documentationtype' => $this->documentationtypeService->getAll(),
            'all_permissions' => Permission::all(),
            'all_roles'=> Role::all(),
        ];
    }
    public function getFormOptionsindex(): array
    {
        return [
            'offices' => $this->officeService->getAll(), // Lista de oficinas
            'regions' => $this->regionService->getRegionsWithOfficesAndTeamStatuses(), // Regiones con oficinas y estados
           // 'team_statuses' => $this->teamStatusService->getAll(), // Lista de estados de equipoq
            'agent_status' => $this->agentstatusService->getAll(),
        ];
    }


    public function findById(string $id): Agent
    {
        // Busca el agente con sus relaciones necesarias
        $agent = Agent::with(['user.roles:id:name','user.permissions', 'documentation'])->findOrFail($id);
        // Devuelve el agente directamente
        return $agent;
    }

    public function create()

    {
        // Opciones para los selects del formulario
        $formOptions = $this->getFormOptions();

        // DTO inicializado con valores predeterminados o nulos
        $agentDto = new AgentDto(
            id: null,
            agent_internal_id: null,
            office_id: null,
            region_id: null,
            user_id: null,
            date_joined: null,
            date_termination: null,
            studies: null,
            agent_status_id: null,
            additional_education: null,
            image_name: null,
            previous_occupation: null,
            license_type: null,
            license_department: null,
            year_obtained_license: null,
            expiration_date_license: null,
            license_number: null,
            address: null,
            landline_phone: null,
            marketing_slogan: null,
            website_descripction: null,
            countries_interested: null,
            meta_tag_description: null,
            bullet_point_one: null,
            bullet_point_two: null,
            bullet_point_three: null,
            meta_tag_keywords: null,
            rejectionReason: null,
            deactivation_date: null,
            commission_percentage: null,
            nro_internacional_remax: null,
            id_business_agent: null,
            contact_id: null,
            url_alterno: null,
        );

        return [
            'formOptions' => $formOptions,
            'agent' => $agentDto, // DTO vacío para inicializar el formulario
        ];
    }
    public function countAgentsInOffice(int $officeId): int
    {
        return Agent::where('office_id', $officeId)->count();
    }

    public function countAgentListings(int $agentId): int
    {
        $agent = Agent::find($agentId);
        if ($agent) {
            return $agent->listings()->count(); // Cuenta los listings del agente
        }
        return 0; // Si el agente no existe, devuelve 0
    }

    public function store(AgentDto $dto): Agent
{
    return DB::transaction(function () use ($dto): Agent {
        $agent_internal_id = null;

        if (!empty($dto->office_id)) {
            $officeCode = str_pad($dto->office_id, 5, '0', STR_PAD_LEFT); // Aseguramos formato como 12004
            $maxId = Agent::where('agent_internal_id', 'LIKE', $officeCode . '%')
                ->selectRaw("MAX(CAST(SUBSTRING(agent_internal_id, LENGTH(?) + 1) AS UNSIGNED)) as max_suffix", [$officeCode])
                ->value('max_suffix');

            $nextSuffix = ($maxId ?? 0) + 1;
            $agent_internal_id = $officeCode . str_pad($nextSuffix, 3, '0', STR_PAD_LEFT); // genera: 120041001, etc.
        }

        // Convertir DTO a array
        $dataToStore = get_object_vars($dto);

        // Sobrescribir el agent_internal_id si se generó
        if ($agent_internal_id !== null) {
            $dataToStore['agent_internal_id'] = $agent_internal_id;
        }

        return Agent::create($dataToStore);
    });
}


    public function update(string $id, AgentDto $dto): Agent
    {
        return DB::transaction(function () use ($id, $dto) {

            $agent = $this->findById($id);
            if (!$agent) {
                throw new \Exception("El agente con ID {$id} no existe.");
            }

            $office = $dto->office_id;
            $region = $dto->region_id;

            $agentid = $agent->id;

            $agent_internal_id = null;
            if (!empty($office)) {
                $key = StringGenerateKey::generateKey();
                $key = "$key$agentid";

                $agent_internal_id = StringGenerateKey::generateAgentInternal($office, $agentid);
            }
            $dataToUpdate = $dto->toArray();
            if ($agent_internal_id !== null) {
                //$dataToUpdate['agent_internal_id'] = $agent_internal_id;
            }

            $agent->update($dataToUpdate);
            return $agent;
        });
    }


   /* public function delete(int $id): bool
    {
        $agent = Agent::with('user')->findOrFail($id);

        return DB::transaction(function () use ($agent) {
            // Eliminar usuario si está asociado
            if ($agent->user) {
                $agent->user->delete();
            }

            // Eliminar el agente
            return $agent->delete();
        });
    }*/
    public function delete(int $id): bool
    {
        $agent = Agent::findOrFail($id);
        return $agent->delete();
    }


    public function getComisiones($data)
    {
        if ($data['mensual'] == 1) {
            $anio = $data['anio'][0];
            $mes = $data['meses'][count($data['meses']) - 1];
            $fecha = Carbon::createFromFormat('Y-m-d', $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01')->format('Y-m-d');
        } else {
            $anio = $data['anio'][0];
            $fecha = `{$anio}-12-31`;
        }

        $queryComisiones = Agent::selectRaw("
                COUNT(agents.id) as total,
                agents.commission_percentage as porcentaje
            ")
            ->where(function ($query) use ($fecha) {
                $query->where('date_termination', '>', $fecha)
                    ->orWhere(function ($query) {
                        $query->whereNull('date_termination');
                    });
            })->where('date_joined', '<', $fecha)
            ->groupBy('porcentaje')
            ->orderBy('porcentaje', 'DESC');

        if (!empty($data['office_id']) || !empty($data['states_id'])) {
            $queryComisiones->leftJoin('offices', 'offices.office_id', 'agents.office_id');

            if (!empty($data['office_id'])) {
                $queryComisiones->whereIn('offices.id', $data['office_id']);
            }

            if (!empty($data['states_id'])) {
                $queryComisiones->leftJoin('provinces', 'provinces.id', 'offices.province_id');
                if ($data['states_id'][0] == 'other') {
                    $queryComisiones->whereNotIn('provinces.state_id', [1, 2, 3]);
                } else {
                    $queryComisiones->whereIn('provinces.state_id', $data['states_id']);
                }
            }
        }

        $comisiones = $queryComisiones->get();

        return $comisiones;
    }

    public function getAgentes($data)
    {
        $queryAgentes = Agent::selectRaw('
                users.name_to_show as name,
                agents.id,
                users.id as user_id
            ')
            ->leftJoin('users', 'users.id', '=', 'agents.user_id')
            ->where('agents.agent_status_id', 1);

        if (isset($data['office_id']) && $data['office_id'] != '') {
            $queryAgentes->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
                ->where('offices.id', $data['office_id']);
        }
        $result = $queryAgentes->orderBy('users.name_to_show', 'ASC')->get();
        return  $result;
    }
    public function getAgentesByOffice($data)
    {
        $queryAgentes = Agent::selectRaw('
                users.name_to_show as name,
                agents.id
            ')
            ->leftJoin('users', 'users.id', '=', 'agents.user_id')
            ->where('agents.agent_status_id', 1);

        if (isset($data['office_id']) && $data['office_id'] != '') {
            $queryAgentes->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
                ->where('offices.office_id', $data['office_id']);
        }
        $result = $queryAgentes->get();
        return  $result;
    }

    public function getAgentsByRegion($data)
    {
        $queryAgentes = Agent::selectRaw('
                users.name_to_show as name,
                agents.id
            ')
            ->leftJoin('users', 'users.id', '=', 'agents.user_id')
            ->where('agents.agent_status_id', 1);

        // Filtrar por región si se proporciona
        if (isset($data['region_id']) && $data['region_id'] != '') {
            $queryAgentes->where('agents.region_id', $data['region_id']);
        }

        $result = $queryAgentes->get();
        return $result;
    }

    public function getAgentsWithFilters($data, int $perPage)
    {
        $queryAgentes = Agent::select('agents.*')
            ->with(['user'])
            ->where(function ($query) use ($data) {
                if (isset($data['agent_status_id'])) {
                    $query->where('agents.agent_status_id', $data['agent_status_id']);
                }

                if (isset($data['region_id']) && $data['region_id'] != '') {
                    $query->where('agents.region_id', $data['region_id']);
                }

                if (isset($data['office_id']) && $data['office_id'] != '') {
                    $query->where('agents.office_id', $data['office_id']);
                }
                // Filtro para búsqueda global
                if (isset($data['search']) && $data['search'] != '') {
                    $query->whereHas('user', function ($q) use ($data) {
                        $q->where('first_name', 'like', '%' . $data['search'] . '%')
                            ->orWhere('middle_name', 'like', '%' . $data['search'] . '%')
                            ->orWhere('last_name', 'like', '%' . $data['search'] . '%')
                            ->orWhere('name_to_show', 'like', '%' . $data['search'] . '%')
                            ->orWhere('ci', 'like', '%' . $data['search'] . '%');
                    });
                }
            });
        $agents = $queryAgentes->paginate($perPage);

        return $agents;
    }



    public function getAgentesPorUbicacion($data)
    {
        $query = Agent::select('users.name_to_show as name', 'agents.id')
            ->leftJoin('offices', 'offices.office_id', '=', 'agents.office_id')
            ->where(function ($query) use ($data) {
                if (isset($data['office_id']) && $data['office_id'] != []) {
                    $query->whereIn('offices.id', $data['office_id'])
                        ->where('offices.id', '!=', 0);
                }
            })
            ->where('agents.agent_status_id', 1)
            ->leftJoin('users', 'users.id', '=', 'agents.user_id');

        /*
			Para ver qué agentes están relacionados con una transacción
			(es util para el filtro de agentes en el form de transacciones)
		*/
        if (!empty($data['transaction_internal_id'])) {
            $query->orWhereIn('agents.id', function ($query) use ($data) {
                $query->select('agents.id')
                    ->from('transactions')
                    ->leftJoin('payments', 'payments.transaction_id', '=', 'transactions.id')
                    ->leftJoin('agents', 'agents.agent_internal_id', '=', 'payments.agent_internal_id')
                    ->where('transactions.internal_id', $data['transaction_internal_id']);
            });
        }

        $agentes = $query->get();

        return $agentes;
    }

    public function migrateAgents($agents)
    {
        foreach ($agents as $agent) {
            Log::info('AGENTE');
            Log::info($agent['AgentID']);

            $agentFinded = Agent::where('agent_internal_id', $agent['AgentID'])
                ->first();

            $office = Office::where('office_id', $agent['OfficeID'])->first();

            Log::info('Mostrando email');
            Log::info($agent['Email']);

            if ($office) {
                if (!$agentFinded) {
                    Log::info('Creando Usuario');
                    $user = User::create(
                        [
                            'name_to_show' => $agent['AgentName'],
                            'first_name' => $agent['FirstName'],
                            'middle_name' => $agent['MiddleName'],
                            'last_name' => $agent['LastName'],
                            'ci' => $agent['SIN'],
                            'gender' => $agent['GenderUID'] == 1324 ? 'Masculino' : 'Femenino',
                            // 'phone_number' => $agent[''],
                            'email' => !empty($agent['Email']) ? $agent['Email'] : 'no-email@email.com',
                            'url' => $agent['VirtualUrl'],
                            'remax_start_date' => $agent['DateJoinedREMAX'],
                            // 'password' => $agent['Password'],
                            'username' => !empty($agent['AgentUserID']) ? $agent['AgentUserID'] : 'no-username',
                            'birthdate' => $agent['DateOfBirth'],
                            // 'user_type_id' => $agent,
                            'remax_title_id' => $agent['RemaxTitleUID'] == 1319 ? 18 : ($agent['RemaxTitleUID'] == 1317 ? 1 : null),
                            'remax_title_to_show_id' => $agent['DisplayTitleUID'] == 2566 ? 8 : ($agent['DisplayTitleUID'] == 2569 ? 2 : null),
                            'team_status_id' => $agent['TeamStatusUID'] == 1307 ? 1 : ($agent['TeamStatusUID'] == 1308 ? 3 : 2),
                        ]
                    );
                    Log::info('Usuario Creado');
                } else {
                    Log::info('Actualizando Usuario');
                    $user = User::updateOrCreate(
                        ['id' => $agentFinded->user_id],
                        [
                            'name_to_show' => $agent['AgentName'],
                            'first_name' => $agent['FirstName'],
                            'middle_name' => $agent['MiddleName'],
                            'last_name' => $agent['LastName'],
                            'ci' => $agent['SIN'],
                            'gender' => $agent['GenderUID'] == 1324 ? 'Masculino' : 'Femenino',
                            // 'phone_number' => $agent[''],
                            'email' => !empty($agent['Email']) ? $agent['Email'] : 'no-email@email.com',
                            'url' => $agent['VirtualUrl'],
                            'remax_start_date' => $agent['DateJoinedREMAX'],
                            // 'password' => $agent['Password'],
                            'username' => !empty($agent['AgentUserID']) ? $agent['AgentUserID'] : 'no-username',
                            'birthdate' => $agent['DateOfBirth'],
                            // 'user_type_id' => $agent,
                            'remax_title_id' => $agent['RemaxTitleUID'] == 1319 ? 18 : ($agent['RemaxTitleUID'] == 1317 ? 1 : null),
                            'remax_title_to_show_id' => $agent['DisplayTitleUID'] == 2566 ? 8 : ($agent['DisplayTitleUID'] == 2569 ? 2 : null),
                            'team_status_id' => $agent['TeamStatusUID'] == 1307 ? 1 : ($agent['TeamStatusUID'] == 1308 ? 3 : 2),
                        ]
                    );
                    Log::info('Usuario Actualizado');
                }

                Log::info('Actualizando o creando agente');
                $existingAgent = DB::table('agents')
                    ->where('agent_internal_id', $agent['AgentID'])
                    ->first();

                if ($existingAgent) {
                    $officeId = Office::where('office_id', $agent['OfficeID'])->first()->office_id;

                    DB::table('agents')
                        ->where('agent_internal_id', $agent['AgentID'])
                        ->update([
                            'office_id' => $officeId,
                            'region_id' => 120,
                            'agent_status_id' => $agent['Active'] ? 1 : 2,
                            'date_termination' => $agent['TerminationDate'],
                            'date_joined' => $agent['DateJoinedREMAX'],
                            'image_name' => isset($agent['AgentPhoto']) ? $agent['AgentPhoto'] : null,
                            'license_type' => $agent['SalesLicensed'] ? 'Agente' : ($agent['BrokerLicensed'] ? 'Broker' : null),
                            'license_department' => null,
                            'year_obtained_license' => null,
                            'license_number' => $agent['SaLicenseNumber'] ?? $agent['BrokerLicenseNumber'] ?? null,
                            'marketing_slogan' => $agent['Slogan'],
                            'website_descripction' => $agent['Closer'],
                            'countries_interested' => null,
                            'meta_tag_description' => null,
                            'bullet_point_one' => $agent['BulletPoint1'],
                            'bullet_point_two' => $agent['BulletPoint2'],
                            'bullet_point_three' => $agent['BulletPoint3'],
                            'meta_tag_keywords' => null,
                            'nro_internacional_remax' => null,
                            'id_business_agent' => null,
                            'user_id' => $user->id,
                            'updated_at' => now(),
                        ]);
                } else {

                    DB::table('agents')->insert([
                        'agent_internal_id' => $agent['AgentID'],
                        'agent_status_id' => $agent['Active'] ? 1 : 2,
                        'date_termination' => $agent['TerminationDate'],
                        'date_joined' => $agent['DateJoinedREMAX'],
                        'image_name' => isset($agent['AgentPhoto']) ? $agent['AgentPhoto'] : null,
                        'license_type' => $agent['SalesLicensed'] ? 'Agente' : ($agent['BrokerLicensed'] ? 'Broker' : null),
                        'license_department' => null,
                        'year_obtained_license' => null,
                        'license_number' => $agent['SaLicenseNumber'] ?? $agent['BrokerLicenseNumber'] ?? null,
                        'marketing_slogan' => $agent['Slogan'],
                        'website_descripction' => $agent['Closer'],
                        'countries_interested' => null,
                        'meta_tag_description' => null,
                        'bullet_point_one' => $agent['BulletPoint1'],
                        'bullet_point_two' => $agent['BulletPoint2'],
                        'bullet_point_three' => $agent['BulletPoint3'],
                        'meta_tag_keywords' => null,
                        'nro_internacional_remax' => null,
                        'id_business_agent' => null,
                        'user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $languages = ['Spanish', 'English', 'Portuguese'];

                $user->languages()->detach();

                foreach ($agent['LanguagesSpoken'] as $languageSpoken) {
                    $language_id = array_search($languageSpoken, $languages);

                    if ($language_id) {

                        $languagePreferred = $agent['PreferredLanguage'];
                        $languagePreferredArray = explode('-', $languagePreferred);

                        if (array_search($languageSpoken, $languagePreferredArray) !== false) {
                            $user->languages()->attach($language_id + 1, ['is_preferred' => 1]);
                        } else {
                            $user->languages()->attach($language_id + 1);
                        }
                    }
                }
            } else {
                Log::info('No se encontró la oficina');
                Log::info($agent['OfficeID']);
            }
        }
    }

    public function migrateAgent($agent)
    {
        Log::info('AGENTE');
        Log::info($agent['AgentID']);

        $agentFinded = Agent::where('agent_internal_id', $agent['AgentID'])
            ->first();

        if(!$agentFinded)
        {
            Log::info('PRE NO SE ENCUENTRA AGENTE');
            $user = User::where('email', trim($agent['Email']))
                ->orWhere('name_to_show', trim($agent['AgentName']))
                ->first();

            if($user)
            {
                Log::info('PRE SE ENCUENTRA USUARIO');
                $agentFinded = Agent::where('user_id', $user->id)
                    ->where('office_id', trim($agent['OfficeID']))
                    //->where('agent_status_id', 1)
                    ->first();

                    if($agentFinded)
                    {
                        Log::info('SE ENCUENTRA AGENTE');
                    }

            }
        }

        $office = Office::where('office_id', trim($agent['OfficeID']))->first();

        Log::info('Mostrando email');
        Log::info($agent['Email']);

        if ($office) {
            if (!$agentFinded) {

                Log::info('Creando Usuario');

                $user = User::create(
                    [
                        'name_to_show' => $agent['AgentName'],
                        'first_name' => $agent['FirstName'],
                        'middle_name' => $agent['MiddleName'],
                        'last_name' => $agent['LastName'],
                        'ci' => $agent['SIN'],
                        'gender' => $agent['GenderUID'] == 1324 ? 'Masculino' : 'Femenino',
                        // 'phone_number' => $agent[''],
                        'email' => !empty($agent['Email']) ? $agent['Email'] : 'no-email@email.com',
                        'url' => $agent['VirtualUrl'],
                        'remax_start_date' => $agent['DateJoinedREMAX'],
                        // 'password' => $agent['Password'],
                        'username' => !empty($agent['AgentUserID']) ? $agent['AgentUserID'] : 'no-username',
                        'birthdate' => $agent['DateOfBirth'],
                        // 'user_type_id' => $agent,
                        'remax_title_id' => $agent['RemaxTitleUID'] == 1319 ? 18 : ($agent['RemaxTitleUID'] == 1317 ? 1 : null),
                        'remax_title_to_show_id' => $agent['DisplayTitleUID'] == 2566 ? 8 : ($agent['DisplayTitleUID'] == 2569 ? 2 : null),
                        'team_status_id' => $agent['TeamStatusUID'] == 1307 ? 1 : ($agent['TeamStatusUID'] == 1308 ? 3 : 2),
                    ]
                );
                Log::info('Usuario Creado');
            } else {

                Log::info('Actualizando Usuario');

                $user = User::updateOrCreate(
                    ['id' => $agentFinded->user_id],
                    [
                        'name_to_show' => $agent['AgentName'],
                        'first_name' => $agent['FirstName'],
                        'middle_name' => $agent['MiddleName'],
                        'last_name' => $agent['LastName'],
                        'ci' => $agent['SIN'],
                        'gender' => $agent['GenderUID'] == 1324 ? 'Masculino' : 'Femenino',
                        // 'phone_number' => $agent[''],
                        'email' => !empty($agent['Email']) ? $agent['Email'] : 'no-email@email.com',
                        'url' => $agent['VirtualUrl'],
                        'remax_start_date' => $agent['DateJoinedREMAX'],
                        // 'password' => $agent['Password'],
                        'username' => !empty($agent['AgentUserID']) ? $agent['AgentUserID'] : 'no-username',
                        'birthdate' => $agent['DateOfBirth'],
                        // 'user_type_id' => $agent,
                        'remax_title_id' => $agent['RemaxTitleUID'] == 1319 ? 18 : ($agent['RemaxTitleUID'] == 1317 ? 1 : null),
                        'remax_title_to_show_id' => $agent['DisplayTitleUID'] == 2566 ? 8 : ($agent['DisplayTitleUID'] == 2569 ? 2 : null),
                        'team_status_id' => $agent['TeamStatusUID'] == 1307 ? 1 : ($agent['TeamStatusUID'] == 1308 ? 3 : 2),
                    ]
                );

                Log::info('Usuario Actualizado');
            }




            if ($agentFinded) {

                $officeId = Office::where('office_id', $agent['OfficeID'])->first()->office_id;
                Log::info('Actualizando agente');
                $agentFinded->update([
                        'office_id' => $officeId,
                        'region_id' => 120,
                        'agent_status_id' => $agent['Active'] ? 1 : 2,
                        'date_termination' => $agent['TerminationDate'],
                        'date_joined' => $agent['DateJoinedREMAX'],
                        //'image_name' => isset($agent['AgentPhoto']) ? $agent['AgentPhoto'] : null,
                        'license_type' => $agent['SalesLicensed'] ? 'Agente' : ($agent['BrokerLicensed'] ? 'Broker' : null),
                        'license_department' => null,
                        'year_obtained_license' => null,
                        'license_number' => $agent['SaLicenseNumber'] ?? $agent['BrokerLicenseNumber'] ?? null,
                        'marketing_slogan' => $agent['Slogan'],
                        'website_descripction' => $agent['Closer'],
                        'countries_interested' => null,
                        'meta_tag_description' => null,
                        'bullet_point_one' => $agent['BulletPoint1'],
                        'bullet_point_two' => $agent['BulletPoint2'],
                        'bullet_point_three' => $agent['BulletPoint3'],
                        'meta_tag_keywords' => null,
                        'nro_internacional_remax' => null,
                        'id_business_agent' => null,
                        'user_id' => $user->id,
                        'updated_at' => now(),
                    ]);
            } else {

                Log::info('Creando agente');

                $agentId = DB::table('agents')->insertGetId([
                    'office_id' => $office->office_id,
                    'region_id' => 120,
                    'agent_internal_id' => $agent['AgentID'],
                    'agent_status_id' => $agent['Active'] ? 1 : 2,
                    'date_termination' => $agent['TerminationDate'],
                    'agent_status_id' => isset($agent['TerminationDate']) && $agent['TerminationDate'] != '' && $agent['TerminationDate'] != null ? 2 : 1,
                    'date_joined' => $agent['DateJoinedREMAX'],
                    //'image_name' => isset($agent['AgentPhoto']) ? $agent['AgentPhoto'] : null,
                    'license_type' => $agent['SalesLicensed'] ? 'Agente' : ($agent['BrokerLicensed'] ? 'Broker' : null),
                    'license_department' => null,
                    'year_obtained_license' => null,
                    'license_number' => $agent['SaLicenseNumber'] ?? $agent['BrokerLicenseNumber'] ?? null,
                    'marketing_slogan' => $agent['Slogan'],
                    'website_descripction' => $agent['Closer'],
                    'countries_interested' => null,
                    'meta_tag_description' => null,
                    'bullet_point_one' => $agent['BulletPoint1'],
                    'bullet_point_two' => $agent['BulletPoint2'],
                    'bullet_point_three' => $agent['BulletPoint3'],
                    'meta_tag_keywords' => null,
                    'nro_internacional_remax' => null,
                    'id_business_agent' => null,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $agentFinded = Agent::find($agentId);
            }

            $languages = ['Spanish', 'English', 'Portuguese'];

            $user->languages()->detach();

            foreach ($agent['LanguagesSpoken'] as $languageSpoken) {
                $language_id = array_search($languageSpoken, $languages);

                if ($language_id) {

                    $languagePreferred = $agent['PreferredLanguage'];
                    $languagePreferredArray = explode('-', $languagePreferred);

                    if (array_search($languageSpoken, $languagePreferredArray) !== false) {
                        $user->languages()->attach($language_id + 4, ['is_preferred' => 1]);
                    } else {
                        $user->languages()->attach($language_id + 4);
                    }
                }
            }

            if(isset($agent['SocialMedia']))
            {
                foreach($agent['SocialMedia'] as $social)
                {
                    SocialNetwork::updateOrCreate(
                        [
                            'name' => strtolower($social['SocialMediaName']),
                            'url' => $social['SocialMediaURL'],
                            'agent_id' => $agentFinded->id,
                            'office_id' => $office->id
                        ],
                        [
                            'state' => 1,
                        ]
                    );
                }
            }
            Log::info("\n");
        } else {
            Log::info('No se encontró la oficina');
            Log::info($agent['OfficeID']);
        }
    }



    public function migrateAgentImage(array $agents)
    {
        foreach ($agents as $agent) {
            $agentModel = Agent::where('agent_internal_id', $agent['AgentID'])->first();

            if (!$agentModel) {
                Log::warning('[Imagen] ❌ Agente no encontrado: ' . $agent['AgentID']);
                continue;
            }

            if (!empty($agent['AgentPhoto'])) {
                Log::info("[Imagen] 🧪 Procesando imagen del agente: {$agentModel->id} - {$agent['AgentPhoto']}");
                $this->processImageFromApi($agent['AgentPhoto'], $agentModel);
            } else {
                Log::info("[Imagen] ⚠️ El agente {$agentModel->id} no tiene imagen en la API.");
            }
        }
    }

    private function processImageFromApi($photoUrl, Agent $agent)
{
    if (!$photoUrl) {
        Log::warning('[Imagen] ❌ URL de imagen vacía para el agente ' . $agent->id);
        return;
    }

    try {
        // Eliminar imagen anterior
        if ($agent->image_name) {
            $this->ImageService->deleteImage($agent->image_name);
            Log::info("[Imagen] 🧼 Imagen anterior eliminada: {$agent->image_name}");
        }

        // Extraer el nombre del archivo de la URL
        $fileName = basename(parse_url($photoUrl, PHP_URL_PATH)); // A_d0dd0f....jpg
        $folder = 'agents';

        Log::info("[Imagen] 📥 Descargando: $photoUrl => $fileName");

        // Descargar y guardar imagen localmente
        $saved = $this->ImageService->downloadImageFromUrlAgent($photoUrl, $folder, $fileName);

        if ($saved) {
            $agent->image_name = $fileName;
            $agent->save();
            Log::info("[Imagen] ✅ Imagen guardada para el agente {$agent->id}: $fileName");
        } else {
            Log::warning("[Imagen] ❌ Falló la descarga de imagen: $photoUrl");
        }

    } catch (\Exception $e) {
        Log::error("[Imagen] ❌ Error al procesar imagen del agente {$agent->id}: " . $e->getMessage());
    }
}





}
