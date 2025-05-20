<?php

namespace App\Http\Requests\AgentController;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateAgentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'id ' => 'nullable|string',
            'agent_internal_id' => 'nullable|string',
            'office_id' => 'nullable|numeric',
            'region_id' => 'nullable|numeric',
            'user_id' => 'nullable|numeric',
            'date_joined'=> 'nullable|string',
            'date_termination' =>'nullable|string',
            'studies' => 'nullable|string',
            'agent_status_id'=>'nullable|numeric',
            'additional_education' => 'nullable|string',
            'image_agent' => [
            'nullable',
            'string',
            'regex:/^data:image\/(jpeg|png|jpg|gif);base64,/',],
            'image_name' => 'nullable|string',
            'previous_occupation' => 'nullable|string',
            'license_type' => 'nullable|string|max:255',
            'license_department' => 'nullable|string',
            'year_obtained_license' => 'nullable|string',
            'expiration_date_license' => 'nullable|string',
			'rejectionReason'=> 'nullable|string',
            'role_id'=> 'nullable|string',
            'license_number' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'landline_phone' => 'nullable|string|max:255',
            'marketing_slogan' => 'nullable|string',
            'website_descripction' => 'nullable|string',
            'countries_interested' => 'nullable|string',
            'meta_tag_description' => 'nullable|string',
            'bullet_point_one' => 'nullable|string',
            'bullet_point_two' => 'nullable|string',
            'bullet_point_three' => 'nullable|string',
            'meta_tag_keywords' => 'nullable|string',
            'deactivation_date' => 'nullable|string',
            'commission_percentage' => 'nullable|numeric',
            'qualifications_id' => 'nullable|numeric',
            'url_alterno' => 'nullable|numeric',

             // Reglas para social_networks
            'socialNetworks' => 'nullable|array',
            'socialNetworks.*.id' => 'nullable|string',
            'socialNetworks.*.name' => 'nullable|string',
            'socialNetworks.*.url' => 'nullable|string',
            'socialNetworks.*.state' => 'nullable|integer|in:0,1',

            // Reglas para area_specialities_user
            'areaSpecialityUser' => 'nullable|array',
            'areaSpecialityUser.*.area_speciality_id' => 'nullable|numeric',
            'areaSpecialityUser.*.user_id' => 'nullable|numeric',

            //'achievementuser' => 'nullable|array',
            //'achievementuser.*.id' => 'nullable|numeric',
            //'achievementuser.*.achievement_id' => 'nullable|numeric',
            //'achievementuser.*.achievement_date' => 'nullable|date', // Asegurar formato válido
           // 'achievementuser.*.enable_achievement' => 'nullable|numeric',
           // 'achievementuser.*.user_id' => 'nullable|numeric',

           'permissions' => 'nullable|array',
           'permissions.*' => 'nullable|string|exists:permissions,name',

            'archivo' => 'nullable|array',
            'archivo.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:255',
            'types' => 'nullable|array',
            'types.*' => 'required|exists:documentation_types,id',

            // Reglas para los logs
            'logs' => 'nullable|array', // Asegura que logs sea un array
            'logs.*.field' => 'nullable|string', // Cada log debe tener un campo 'field'
            'logs.*.oldValue' => 'nullable|string', // Valor anterior opcional
            'logs.*.newValue' => 'nullable|string', // Valor nuevo opcional
            'logs.*.timestamp' => 'nullable|date', // Timestamp obligatorio en formato fecha
			//'user_login' => 'nullable|numeric',  // Valida el campo 'user_login' como numérico

        ];
    }

    /**
     * Prepara los datos de entrada antes de que se apliquen las reglas de validación.
     */
    protected function prepareForValidation()
    {
        Log::info('📥 Redes sociales recibidas en el request:', [
            'socialNetworks' => $this->input('socialNetworks')
        ]);
		Log::info('logs recibidas en el request:', [
            'logs' => $this->input('logs')
        ]);

        // Convierte fechas al formato MySQL (Y-m-d H:i:s) usando Carbon
        $this->merge([
            'date_joined' => $this->formatDate($this->input('date_joined')),
            'date_termination' => $this->formatDate($this->input('date_termination')),
            'date_transferred' => $this->formatDate($this->input('date_transferred')),
            'expiration_date_license' => $this->formatDate($this->input('expiration_date_license')),
            'achievement_date'=> $this->formatDate($this->input('achievement_date')),
			 // Convertir la fecha de los logs
			 'logs' => collect($this->input('logs'))->map(function ($log) {
				$log['timestamp'] = $this->formatDate($log['timestamp']);
				return $log;
			})->toArray(),
        ]);
    }

    /**
     * Formatea una fecha en formato ISO 8601 a Y-m-d H:i:s.
     */
    private function formatDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            // Si no se puede formatear la fecha, devuelve null.
            return null;
        }
    }

    /**
     * Mensajes personalizados de error.
     */
    public function messages(): array
    {
        return [
            'date_joined.date' => 'El campo fecha de ingreso debe ser una fecha válida.',
            'date_termination.date' => 'El campo fecha de terminación debe ser una fecha válida.',
            'date_transferred.date' => 'El campo fecha de transferencia debe ser una fecha válida.',
            'expiration_date_license.date' => 'El campo fecha de expiración de licencia debe ser una fecha válida.',
            'date_joined.date_format' => 'El campo fecha de ingreso debe estar en formato Y-m-d H:i:s.',
            'date_termination.date_format' => 'El campo fecha de terminación debe estar en formato Y-m-d H:i:s.',
            'date_transferred.date_format' => 'El campo fecha de transferencia debe estar en formato Y-m-d H:i:s.',
            'achievement_date' => 'El campo achievement_date debe estar en formato Y-m-d H:i:s.',
        ];
    }
}
