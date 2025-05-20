<?php

namespace App\Http\Requests\ListingController;

use App\Dtos\Listings\CreateAcquisitionDto;
use App\Traits\AutenticationTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreAcquisitionRequest extends FormRequest
{
    use AutenticationTrait;

    public CreateAcquisitionDto $dto;
    public array $validationData = [];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $user = $this->getAuthenticate();
        if ($user->can('listing.create.select_office')) {
            $this->validationData['office_id'] = [
                'required',
                'exists:offices,office_id',
            ];
        } else {
            $this->validationData['office_id'] = [
                'nullable',
            ];
        }

        if ($user->can('listing.create.select_agent')) {
            $this->validationData['agent_id'] = [
                'required',
                'exists:agents,id',
            ];
        } else {
            $this->validationData['agent_id'] = [
                'nullable',
            ];
        }

        // dd($user->getAllPermissions()->pluck('name')->toArray());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'office_id' => $this->validationData['office_id'],
            'agent_id' => $this->validationData['agent_id'],
            'area_id' => 'required|exists:areas,id',
            'transaction_type_id' => 'required|exists:listing_transaction_types,id',
            'subtype_property_id' => 'required|exists:subtype_properties,id',
        ];
    }

    public function messages(): array
    {
        return [
            'office_id.required' => 'Debe seleccionar una oficina para asociar al listing.',
            'office_id.exists' => 'La oficina seleccionada no existe o no es válida.',

            'agent_id.required' => 'Debe seleccionar un agente responsable del listing.',
            'agent_id.exists' => 'El agente seleccionado no existe o no es válido.',

            'area_id.required' => 'Debe seleccionar el área geográfica para el listing.',
            'area_id.exists' => 'El área seleccionada no es válida o no está registrada.',

            'transaction_type_id.required' => 'Debe seleccionar el tipo de transacción del listing (venta, renta, etc.).',
            'transaction_type_id.exists' => 'El tipo de transacción seleccionado no es válido.',

            'subtype_property_id.required' => 'Debe seleccionar el subtipo de propiedad del listing (por ejemplo: casa, departamento, local comercial).',
            'subtype_property_id.exists' => 'El subtipo de propiedad seleccionado no es válido.',
        ];
    }

    public function passedValidation()
    {
        $this->dto = CreateAcquisitionDto::from($this->validated());
    }
}
