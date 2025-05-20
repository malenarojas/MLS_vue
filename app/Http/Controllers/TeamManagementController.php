<?php

namespace App\Http\Controllers;

use App\Models\SocialNetwork;
use App\Models\Team;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Agent;
use App\Models\Office;
use App\Models\TeamManagement;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ImageService;

class TeamManagementController extends Controller
{


    protected ImageService $imageService;


    public function __construct(ImageService $imageService)
    {
        $this->imageService= $imageService;

    }
    /**
     * Listar todos los equipos.
     *
     * @group Teams
     * @authenticated
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "TEAM ALL SERVICE",
     *             "office_id": 1,
     *             "leader_id": 5,
     *             "members_count": 7,
     *             "listings_count": 0,
     *             "shortlink": "/TEAM-ALL-SERVICE",
     *             "created_at": "2024-01-01 00:00:00",
     *             "updated_at": "2024-01-01 00:00:00"
     *         }
     *     ]
     * }
     */
    public function index()
    {
        $teams = TeamManagement::with(['office', 'leader.user'])
            ->latest()
            ->paginate(10);

        $offices = Office::select('id', 'name', 'office_id')->orderBy('name')->get();

        return Inertia::render('TeamManagement/index', [
            'offices' => $offices,
            'teams' => $teams,
            'filters' => request()->only(['search', 'office'])
        ]);
    }
    public function create(Request $request)
    {
        Log::info('📥 Request recibido en TeamManagement@create:', $request->all());

        $offices = Office::select('id', 'name' , 'office_id')->orderBy('name')->get();

        $agents = [];

        if ($request->has('office_id')) {
            $officeId = $request->input('office_id');

            Log::info('🔍 Buscando agentes para la oficina:', ['office_id' => $officeId]);
            $agents = Agent::where('office_id', $officeId)
            ->with('user:id,id,name_to_show') // solo traemos lo necesario
            ->get()
            ->map(function ($agent) {
                return [
                    'id' => $agent->id,
                    'name' => $agent->user->name_to_show ?? 'Sin nombre',
                ];
            });

        }

        return Inertia::render("TeamManagement/index", [
            'offices' => $offices,
            'agents' => $agents,
            'selectedOffice' => $request->input('office_id'),
        ]);
    }
    public function getAgents(Request $request)
{
    $officeId = $request->input('office_id');

    if (!$officeId) {
        return response()->json(['agents' => []]);
    }

    $agents = Agent::where('office_id', $officeId)
        ->with('user:id,id,name_to_show')
        ->get()
        ->map(function ($agent) {
            return [
                'id' => $agent->id,
                'name' => $agent->user->name_to_show ?? 'Sin nombre',
            ];
        });

    return response()->json(['agents' => $agents]);
}


    public function edit(Request $request)
    {
        Log::info("✏️ Editando team:", ['query_params' => $request->query()]);

        $teamId = $request->query('team_id');

        Log::info("🆔 ID del equipo recibido:", ['team_id' => $teamId]);

        // Buscamos el team con sus relaciones necesarias
        $team = TeamManagement::with(['office', 'leader.user', 'members.user', 'socialNetworks'])->findOrFail($teamId);

        $officeId = $team->office_id;

        $agents = Agent::with('user:id,name_to_show,first_name,last_name')
            ->where('office_id', $officeId)
            //->where('agent_status_id', 1)
            ->select('id', 'user_id', 'office_id')
            ->get();

        // Oficinas disponibles para selección
        $offices = Office::select('id', 'office_id', 'name')->get();

        return Inertia::render('TeamManagement/edit', [
            'team' => $team,
            'agents' => $agents,
            'offices' => $offices,
        ]);
    }
    /**
     * Crear un nuevo equipo.
     *
     * @group Teams
     * @authenticated
     * @bodyParam name string required Nombre del equipo. Example: TEAM ALL SERVICE
     * @bodyParam office_id integer required ID de la oficina. Example: 1
     * @bodyParam leader_id integer required ID del líder del equipo. Example: 5
     * @bodyParam shortlink string required Enlace corto del equipo. Example: /TEAM-ALL-SERVICE
     *
     * @response 201 {
     *     "message": "Equipo creado correctamente",
     *     "data": {
     *         "id": 1,
     *         "name": "TEAM ALL SERVICE",
     *         "office_id": 1,
     *         "leader_id": 5,
     *         "shortlink": "/TEAM-ALL-SERVICE",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     * }
     */
    public function store(StoreTeamRequest $request)
    {
        $team = TeamManagement::create($request->validated());
        if ($request->filled('leader_id')) {
            $team->members()->attach($request->leader_id, [
                'is_leader' => true,
            ]);
        }
        return redirect()->route('teammanagement.index')
            ->with('success', 'Equipo creado correctamente');
    }

    /**
     * Mostrar los detalles de un equipo específico.
     *
     * @group Teams
     * @authenticated
     * @urlParam id integer required El ID del equipo. Example: 1
     *
     * @response {
     *     "id": 1,
     *     "name": "TEAM ALL SERVICE",
     *     "office_id": 1,
     *     "leader_id": 5,
     *     "members_count": 7,
     *     "listings_count": 0,
     *     "shortlink": "/TEAM-ALL-SERVICE",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z",
     *     "office": {
     *         "id": 1,
     *         "name": "RE/MAX At Work"
     *     },
     *     "leader": {
     *         "id": 5,
     *         "name": "Victor Hugo Flores Mejía"
     *     }
     * }
     */
    public function show(string $id): JsonResponse
    {
        $team = TeamManagement::with(['office', 'leader'])->findOrFail($id);

        return response()->json($team);
    }

    /**
     * Actualizar los detalles de un equipo específico.
     *
     * @group Teams
     * @authenticated
     * @urlParam id integer required El ID del equipo a actualizar. Example: 1
     *
     * @bodyParam name string required Nombre del equipo. Example: TEAM ALL SERVICE
     * @bodyParam office_id integer required ID de la oficina. Example: 1
     * @bodyParam leader_id integer required ID del líder del equipo. Example: 5
     * @bodyParam shortlink string required Enlace corto del equipo. Example: /TEAM-ALL-SERVICE
     *
     * @response {
     *     "message": "Equipo actualizado correctamente",
     *     "data": {
     *         "id": 1,
     *         "name": "TEAM ALL SERVICE",
     *         "office_id": 1,
     *         "leader_id": 5,
     *         "shortlink": "/TEAM-ALL-SERVICE",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     * }
     */
    public function update(UpdateTeamRequest $request, string $id)
{
    $team = TeamManagement::findOrFail($id);
     // 🌐 Actualizar redes sociales
     $socialNetworks = $request->input('socialNetworks', []);
Log::info('🔍 Social networks recibidas en el request:', $socialNetworks);
     foreach ($request->input('socialNetworks', []) as $networkData) {
        SocialNetwork::updateOrCreate(
            [
                'id' => $networkData['id'] ?? null,
                'team_management_id' => $id,
            ],
            [
                'name' => $networkData['name'],
                'state' => $networkData['state'] ?? 0,
                'url' => $networkData['url'] ?? '',
            ]
        );
    }

    // 📸 Imagen del equipo
    if ($request->hasFile('image_file')) {
        Log::info('📁 Se recibió una imagen del equipo');

        // ✅ Opción: eliminar imagen anterior
        // if ($team->image_name) Storage::disk('public')->delete($team->image_name);

        $imagePath = $this->imageService->uploadImageFromFile(
            $request->file('image_file'),
            'team_management/image'
        );

        $team->image_name = $imagePath;
    }

    // 🖼 Logo del equipo
    if ($request->hasFile('logo_file')) {
        Log::info('📁 Se recibió un logo del equipo');

        // ✅ Opción: eliminar logo anterior
        // if ($team->logo_name) Storage::disk('public')->delete($team->logo_name);

        $logoPath = $this->imageService->uploadImageFromFile(
            $request->file('logo_file'),
            'team_management/logo'
        );

        $team->logo_name = $logoPath;
    }

    // 📝 Actualizar campos restantes validados
    $team->update($request->validated());

    // 👥 Sincronizar miembros del equipo con `is_leader` en tabla pivote
    $leaderId = $request->input('leader_id');
    $agentIds = $request->input('members', []);

    $syncData = collect($agentIds)->mapWithKeys(fn($agentId) => [
        $agentId => ['is_leader' => $agentId == $leaderId]
    ])->toArray();

    $team->members()->sync($syncData);

    return redirect()
        ->route('teammanagement.edit', ['team_id' => $team->id])
        ->with('success', 'Equipo actualizado correctamente');
}


    /**
     * Eliminar un equipo.
     *
     * @group Teams
     * @authenticated
     * @urlParam id required El ID del equipo. Example: 1
     * @response 204
     */
    public function destroy(string $id): JsonResponse
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return response()->json(['message' => 'Equipo eliminado correctamente']);
    }
}
