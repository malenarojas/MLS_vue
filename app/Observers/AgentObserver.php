<?php


namespace App\Observers;
use Carbon\Carbon;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AgentObserver
{
    /**
     * Evento que se ejecuta antes de crear un agente.
     */
    public function creating(Agent $agent): void
    {


    $userData = request()->get('user', []);

    if (isset($userData['remax_start_date']) && isset($userData['birthdate'])) {
        try {
            $userData['birthdate'] = $this->formatDate($userData['birthdate']);
            $userData['remax_start_date'] = $this->formatDate($userData['remax_start_date']);
        } catch (\Exception $e) {
            throw new \Exception('La fecha de inicio no tiene un formato válido.');
        }
    }


    $validator = Validator::make($userData, [
        'first_name' => 'nullable|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'name_to_show' => 'nullable|string|max:255',
        'ci' => 'nullable|string|max:50',
        'gender' => 'nullable|string',
        'phone_number' => 'nullable|string|max:20',
        'email' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'remax_start_date' => 'nullable|date_format:Y-m-d',
        'birthdate' => 'nullable|date_format:Y-m-d',
        'password' => 'required|string',
        'username' => 'required|string|max:255',
        'user_type_id' => 'nullable|numeric',
        'remax_title_id' => 'nullable|numeric',
        'remax_title_to_show_id' => 'nullable|numeric',
        'team_status_id' => 'nullable|numeric',
        'customer_preference_id' => 'nullable|numeric',
        'state_url'=> 'nullable|numeric'
    ]);

    if ($validator->fails()) {
        throw new \Exception('Error en la validación del usuario: ' . $validator->errors()->first());
    }

    // Crear el usuario asociado
    $validatedData = $validator->validated();
    $user = User::create([
        'first_name' => $validatedData['first_name'] ?? null,
        'middle_name' => $validatedData['middle_name'] ?? null,
        'last_name' => $validatedData['last_name'] ?? null,
        'name_to_show' => $validatedData['name_to_show'] ?? null,
        'ci' => $validatedData['ci'] ?? null,
        'gender' => $validatedData['gender'] ?? null,
        'phone_number' => $validatedData['phone_number'] ?? null,
        'email' => $validatedData['email'],
        'url' => $validatedData['url'] ?? null,
        'remax_start_date' => $validatedData['remax_start_date'] ?? null,
        'birthdate' => $validatedData['birthdate'] ?? null,
        'password' => bcrypt($validatedData['password']),
        'username' => $validatedData['username'],
        'user_type_id' => $validatedData['user_type_id'] ?? null,
        'remax_title_id' => $validatedData['remax_title_id'] ?? null,
        'remax_title_to_show_id' => $validatedData['remax_title_to_show_id'] ?? null,
        'team_status_id' => $validatedData['team_status_id'] ?? null,
        'customer_preference_id' => $validatedData['customer_preference_id'] ?? null,
    ]);


    $agent->user_id = $user->id;

}

    /**
 * Evento que se ejecuta después de actualizar un agente.
 */


 private function formatDate(?string $date): ?string
 {
     if (!$date) {
         return null;
     }

     try {
        return Carbon::parse($date)->format('Y-m-d'); // 🔥 Cambiamos a 'Y-m-d'
     } catch (\Exception $e) {
         // Si no se puede formatear la fecha, devuelve null.
         return null;
     }
 }
    public function deleted(Agent $agent): void
    {
        if ($agent->user) {
            $agent->user->delete();
        }
    }
}
