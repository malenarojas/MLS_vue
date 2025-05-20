<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginAsController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        $broker = Auth::user();

        if (!$broker || $broker->hasRole('Agente')) {
            abort(403, 'No tienes permiso para usar esta función.');
        }

        if ($broker->hasRole('Broker')) {
            $myAgent = Agent::where('user_id', $broker->id)->first();
            $agentToLoginAs = Agent::where('user_id', $request->user_id)->first();

            if ($myAgent->office_id != $agentToLoginAs->office_id) {
                abort(403, 'No tienes permiso para usar esta función.');
            }
            /*if ($agentToLoginAs->agent_status_id == 1) {
                abort(403, 'Este agente está inactivo. No puedes iniciar sesión como él.');
            }*/
        }
        $user = User::findOrFail($request->user_id);

        session()->put('original_user', $broker->id);
        session()->put('login_as', $user->id);
        session()->save();

        return response()->json([
            'message' => 'Has iniciado sesión como ' . $user->name,
            'user' => $user,
        ]);
    }

    public function destroy()
    {
        $user = User::findOrFail(session('original_user'));

        session()->forget('login_as');
        session()->forget('original_user');
        session()->save();

        return response()->json([
            'message' => 'Has vuelto a tu cuenta',
            'user' => $user,
        ]);
    }
}
