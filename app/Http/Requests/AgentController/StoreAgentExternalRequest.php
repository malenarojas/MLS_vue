<?php

namespace App\Http\Requests\AgentController;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreAgentExternalRequest extends FormRequest
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
            'user.agent_internal_id' => 'nullable|string',
            'office_id' => 'required|numeric',
             'user.email' => 'required|email|unique:users,email',
            'user.region_id' => 'nullable|numeric',
            'user.user_id' => 'nullable|numeric',
            'date_joined'=> 'nullable|string|max:255',
            'agent_status_id'=>'nullable|numeric',
            'license_number' => 'nullable|string|max:255',
            'marketing_slogan' => 'nullable|string|max:255',
            'website_descripction' => 'nullable|string|max:255',
            'countries_interested' => 'nullable|string|max:255',
            'meta_tag_description' => 'nullable|string|max:255',
            'bullet_point_one' => 'nullable|string|max:255',
            'bullet_point_two' => 'nullable|string|max:255',
            'bullet_point_three' => 'nullable|string|max:255',
            'meta_tag_keywords' => 'nullable|string|max:255',
            'deactivation_date' => 'nullable|string|max:255',
            'commission_percentage' => 'nullable|numeric',
            'qualifications_id' => 'nullable|numeric',
            'nro_internacional_remax'=> 'nullable|numeric',
            'id_business_agent'=> 'nullable|numeric',
             // Reglas para social_networks
            'social_networks' => 'nullable|array',
            'social_networks.*.name' => 'nullable|string|max:255',
            'social_networks.*.url' => 'nullable|string|max:255',
            'social_networks.*.state' => 'nullable|integer|in:0,1',

            // Reglas para area_specialities_user
            'area_specialities_user' => 'nullable|array',
            'area_specialities_user.*.area_speciality_id' => 'nullable|numeric',
            'area_specialities_user.*.user_id' => 'nullable|numeric',

            // Reglas para achievements_user
            'achievement_user' => 'nullable|array',
            'achievement_user.*.id' => 'nullable|numeric',
            'achievement_user.*.achievement_date' => 'nullable|date',
            'achievement_user.*.enable_achievement' => 'nullable|numeric',
            'achievement_user.*.user_id' => 'nullable|numeric',


            'archivo' => 'nullable|array',
            'archivo.*' => 'required|file|mimes:pdf,jpeg,png,jpg,gif', // 🎯 Asegura que sean archivos válidos
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:255',
            'types' => 'nullable|array',
            'types.*' => 'required|exists:documentation_types,id',
        ];
    }
     /**
     * Prepara los datos de entrada antes de que se apliquen las reglas de validación.
     */
    protected function prepareForValidation()
    {
        // Convierte fechas al formato MySQL (Y-m-d H:i:s) usando Carbon
        $this->merge([
            'date_joined' => $this->formatDate($this->input('date_joined')),
            'date_termination' => $this->formatDate($this->input('date_termination')),
            'expiration_date_license' => $this->formatDate($this->input('expiration_date_license')),
            'achievement_date' => $this->formatDate($this->input('achievement_date')),

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
            return Carbon::parse($date)->format('Y-m-d H:i:s');
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
            'expiration_date_license.date' => 'El campo fecha de expiración de licencia debe ser una fecha válida.',
            'achievement_date.date' => 'El campo de la fecha de fecha de achievement debe ser una fecha válida.',
            'date_joined.date_format' => 'El campo fecha de ingreso debe estar en formato Y-m-d H:i:s.',
            'date_termination.date_format' => 'El campo fecha de terminación debe estar en formato Y-m-d H:i:s.',
            'expiration_date_license.date_format' => 'El campo fecha de expiración de licencia debe estar en formato Y-m-d H:i:s.',
            'achievement_date.date_format' => 'El campo fecha de achievement debe estar en formato Y-m-d H:i:s.',
        ];
    }
}
