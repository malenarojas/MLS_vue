<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGoalRequest;
use App\Models\ExchangeRate;
use App\Models\Goal;
use App\Services\GoalService;
use App\Services\MenuService;
use App\Traits\AutenticationTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class GoalController extends Controller
{

    use AutenticationTrait;

    protected $goalService;

    public function __construct(
        GoalService $goalService
    ) {
        $this->goalService = $goalService;
    }

    public function index () {
        return Inertia::render('Goals/index');
    }

    public function getGoals (Request $request) {
        $user = $this->getAuthenticate();

        if(!$user->roles ){
            return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
        }

        if(in_array($user->roles->first()->name, ['Soporte', 'Administrador', 'Super Administrador'])) {
            $data = $request->all();
            $goals = $this->goalService->getGoals($data);
            return response()->json($goals);

        }

        if($user->roles->first()->name == 'Broker'){
            $request->office_id = $user->agent->office->id;
            $goals = $this->goalService->getGoals($request);
            return response()->json($goals);
        }

        return response()->json(['message' => 'Not Found'], Response::HTTP_NOT_FOUND);
    }
    /**
     * Metodo de actualizacion de un registro en "GOAL"
     * 
     * Caso de ser una meta para oficina se debe enviar el id de la oficina
     * Caso de ser una meta para un agente se debe enviar el id del agente y el id de la oficina
     */
    public function update (UpdateGoalRequest $request) {
        
        $validated = $request->validated();

        Goal::updateOrCreate(
            [
                'office_id' => $validated['office_id'],
                'agent_id' => $validated['agent_id'],
                'month' => $validated['month'],
                'year' => $validated['year'],
                'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id
            ],
            $validated
        );
        return response()->json(['message' => 'Meta actualizada correctamente']);
    }
}
