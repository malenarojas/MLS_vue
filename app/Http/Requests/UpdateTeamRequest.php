<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'shortlink' => 'nullable|string|max:255',
            'enable_chat' => 'nullable|boolean',
            'whatsapp_number' => 'nullable|string|max:255',
            'spanish_description' => 'nullable|string',
            'english_description' => 'nullable|string',
            'portuguese_description' => 'nullable|string',
            'motto' => 'nullable|string',
            'description' => 'nullable|string',
            'office_id' => 'required|exists:offices,office_id',
            'member_count' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'leader_id' => 'nullable|exists:agents,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:agents,id',
            'socialNetworks' => 'nullable|array',
            'socialNetworks.*.id' => 'nullable|integer',
            'socialNetworks.*.name' => 'nullable|string',
            'socialNetworks.*.url' => 'nullable|string',
            'socialNetworks.*.state' => 'nullable|integer|in:0,1',
            // ✅ Archivos recibidos vía FormData
            'logo_file' => 'nullable|image|max:2048',
            'image_file' => 'nullable|image|max:2048',
        ];
    }
    protected function prepareForValidation()
{
    // Transformar los miembros del equipo: sacar solo los IDs
    if ($this->has('members') && is_array($this->members)) {
        $this->merge([
            'members' => collect($this->members)->pluck('id')->toArray(),
        ]);
    }
}

}
