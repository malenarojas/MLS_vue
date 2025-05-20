<?php

namespace App\Http\Requests\Api\v1\AgentController;

use App\Dtos\Agents\AgentParamsDto;
use App\Models\Office;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class IndexAgentRequest extends FormRequest
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
            'id' => 'nullable|numeric',
            'office_id' => 'nullable|numeric',
        ];
    }

    /**
     * Prepara los datos de entrada antes de que se apliquen las reglas de validación.
     */
    protected function prepareForValidation()
    {
        $officeId = $this->input('office_id', null);
        $office = Office::where('office_id', $officeId)->value('id');

        $this->merge([
            'id' => $office?->id ?? null,
        ]);
    }
}
