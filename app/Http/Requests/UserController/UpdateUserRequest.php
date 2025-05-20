<?php

namespace App\Http\Requests\Api\v1\UserController;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateUserRequest extends FormRequest
{
   /**
     * Reglas de validación para actualizar un usuario.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'name_to_show' => 'nullable|string|max:255',
            'ci' => 'nullable|string|max:50',
            'gender' => 'nullable|string|in:Femenino,Masculino,Otro',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $this->route('user'), // Ignorar el usuario actual en la validación de unicidad
            'url' => 'nullable|url|max:255',
            'remax_start_date' => 'nullable|date|date_format:Y-m-d H:i:s',
            'email_verified_at' => 'nullable|date|date_format:Y-m-d H:i:s',
            'password' => 'nullable|string|min:8', // Opcional, pero mínimo 8 caracteres si se envía
            'username' => 'required|string|max:255|unique:users,username,' . $this->route('user'), // Ignorar el usuario actual
            'user_type_id' => 'nullable|integer',
            'remax_title_id' => 'nullable|integer',
            'remax_title_to_show_id' => 'nullable|integer',
            'team_status_id' => 'nullable|integer',
            'customer_preference_id' => 'nullable|integer',
        ];
    }

    /**
     * Prepara los datos de entrada antes de que se apliquen las reglas de validación.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'remax_start_date' => $this->formatDate($this->input('remax_start_date')),
            'email_verified_at' => $this->formatDate($this->input('email_verified_at')),
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
            'remax_start_date.date' => 'El campo fecha de inicio en Remax debe ser una fecha válida.',
            'remax_start_date.date_format' => 'El campo fecha de inicio en Remax debe estar en el formato Y-m-d H:i:s.',
            'email_verified_at.date' => 'El campo de verificación de correo debe ser una fecha válida.',
            'email_verified_at.date_format' => 'El campo de verificación de correo debe estar en el formato Y-m-d H:i:s.',
        ];
    }
}
