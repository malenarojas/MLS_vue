<?php

namespace App\Http\Requests\ListingController;

use App\Constants\ListingStatusId;
use App\Dtos\Listings\ListingDto;
use App\Services\Listings\ListingService;
use App\Traits\AutenticationTrait;
use App\Utils\FormatString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateListingRequest extends FormRequest
{
    use AutenticationTrait;

    public ListingDto $dto;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    private const DATE_FIELDS = [
        'date_of_listing',
        'contract_end_date',
        'cancellation_date'
    ];

    private const LISTING_INFORMATION_DATE_FIELDS = [
        'available_date',
        'year_construction',
    ];

    public function prepareForValidation()
    {
        $user = $this->getAuthenticate();
        $newData = [];
        $data = $this->all();
        // dd($data);

        if ($data['is_draft']) {
            $newData['status_listing_id'] = ListingService::LISTING_STATUS_DRAFT;
        } elseif ($data['is_sent_to_review']) {
            $newData['status_listing_id'] = ListingService::LISTING_STATUS_REVIEW;
        } else {
            if (!$user->hasDirectPermission('listing.show_status')) {
                $newData['status_listing_id'] = ListingService::LISTING_STATUS_REVIEW;
            }
        }

        $newData['is_published'] = $this->is_published ? 1 : 0;
        $newData['is_sent_to_review'] = $this->is_sent_to_review ? 1 : 0;
        $newData['rent_timeframe_id'] = $this['transaction_type_id'] == 2 ? $this->rent_timeframe_id : null;
        // dd($newData['rent_timeframe_id']);

        foreach (self::DATE_FIELDS as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $newData[$field] = FormatString::formatDateWithoutTime($data[$field]);
            }
        }

        foreach (self::LISTING_INFORMATION_DATE_FIELDS as $field) {
            if (isset($data['listing_information'][$field]) && $data['listing_information'][$field] !== null) {
                $newData['listing_information'][$field] = FormatString::formatDateWithoutTime($data['listing_information'][$field]);
            }
        }

        if (isset($data['listing_information']['year_construction']) && $data['listing_information']['year_construction'] !== null) {
            $newData['listing_information']['year_construction'] = FormatString::formatDateWithoutTime($data['listing_information']['year_construction']);
        }

        if (isset($data['listing_information']['sale_sign'])) {
            // Recibe bool y convierte a 1 o 0
            $newData['listing_information']['sale_sign'] = $data['listing_information']['sale_sign'] ? 1 : 0;
        }

        $newData['listing_information'] = array_merge($data['listing_information'], $newData['listing_information'] ?? []);
        $newData['key'] = $this->route('key');

        $newData['location'] = $data['location'] ?? [];
        $newData['location']['show_addres_on_website'] = $data['location']['show_addres_on_website'] ? 1 : 0;

        $this->merge($newData);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'exists:listings,key'],
            // Additional fields
            // 'is_draft' => ['nullable', 'numeric'],
            // 'is_sent_to_review' => ['nullable', 'numeric'],
            'is_published' => ['nullable', 'numeric'],
            'date_of_listing' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date'],
            'cancellation_date' => ['nullable', 'date'],
            // Marketing
            'title' => $this->conditionallyRequiredExceptDraft(['required', 'string', 'max:255']),
            'description_website' => ['nullable', 'string', 'max:1000000'],
            'marketing_description' => ['nullable', 'string', 'max:350'],
            'location_information' => ['nullable', 'string', 'max:1000000'],
            // Translations
            'translations' => ['nullable', 'array'],
            // Price
            'price' => $this->conditionallyRequiredExceptDraft(['required', 'array']),
            'price.amount' => $this->conditionallyRequiredExceptDraft(['required', 'numeric']),
            // 'price.currency_id' => 'nullable|exists:currencies,id',
            // Finalcial
            'reference' => 'nullable|string',
            // Cambiar por seguridad
            'property_number' => 'nullable|numeric',
            'financial_note' => 'nullable|string',
            // Commission Option
            'commission_option' => $this->conditionallyRequiredExceptDraft(['required', 'array']),
            'commission_option.recruitment_commission' => $this->conditionallyRequiredExceptDraft(['required', 'numeric']),
            'commission_option.type_recruitment_commission' => $this->conditionallyRequiredExceptDraft(['required', 'string', 'max:255', 'in:P,C']),
            'commission_option.sales_commission' => ['required', 'numeric'],
            'commission_option.sales_commission_type' => ['required', 'string', 'max:255', 'in:P,C'],
            // Location
            'location' => ['nullable', 'array'],
            'location.number' => ['nullable', 'string', 'max:255'],
            'location.unit_department' => ['nullable', 'string', 'max:255'],
            'location.first_address' => ['nullable', 'string', 'max:255'],
            'location.second_address' => ['nullable', 'string', 'max:255'],
            'location.zip_code' => ['nullable', 'string', 'max:255'],
            'location.district' => ['nullable', 'string', 'max:255'],
            'location.access_number' => ['nullable', 'string', 'max:255'],
            'location.show_addres_on_website' => ['nullable', 'numeric', 'in:0,1'],
            'location.latitude' => $this->conditionallyRequiredExceptDraft(['required', 'numeric']),
            'location.longitude' => $this->conditionallyRequiredExceptDraft(['required', 'numeric']),
            'location.city_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:cities,id']),
            'location.zone_id' => ['nullable', 'exists:zones,id'],
            'location.type_floor_id' => ['nullable', 'exists:type_floors,id'],
            // Listing listing_information
            'listing_information' => ['nullable', 'array'],
            'listing_information.id' => ['nullable', 'exists:listings_information,id'],
            'listing_information.available_date' => ['nullable', 'date'],
            'listing_information.year_construction' => $this->conditionallyRequiredExceptDraft(['required', 'date']),
            'listing_information.parking_slots' => ['nullable', 'numeric'],
            'listing_information.plant_numbers' => ['nullable', 'numeric'],
            'listing_information.number_departments' => ['nullable', 'numeric'],
            'listing_information.sale_sign' => ['nullable', 'numeric'],
            'listing_information.total_area' => $this->conditionallyRequiredExceptDraft(['required', 'numeric']),
            'listing_information.cubic_volume' => ['nullable', 'numeric'],
            'listing_information.land_m2' => ['nullable', 'numeric'],
            // 'listing_information.land_x' => ['nullable', 'numeric'], // No tomar en cuenta
            // 'listing_information.land_y' => ['nullable', 'numeric'],
            'listing_information.construction_area_m' => $this->conditionallyRequiredExceptDraft(['required', 'numeric']),
            'listing_information.total_number_rooms' => ['nullable', 'numeric'],
            'listing_information.number_bathrooms' => ['nullable', 'numeric'],
            'listing_information.number_bedrooms' => ['nullable', 'numeric'],
            'listing_information.number_toiletrooms' => ['nullable', 'numeric'],
            'listing_information.youtube_link' => [
                'nullable',
                'url',
                'regex:/^(https?\:\/\/)?(www\.youtube\.com|youtu\.?be)\/.+$/',
                'max:255',
            ],
            'listing_information.virtual_viewer' => ['nullable', 'string', 'in:immoviewer,matterport,iguide,other', 'max:255'],
            'listing_information.virtual_link' => $this->getVirtualLinkRules(),
            'listing_information.external_link' => ['nullable', 'url', 'max:255'],
            // Foreign keys for Listing listing_information
            'listing_information.subtype_property_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:subtype_properties,id']),
            'listing_information.market_status_id' => ['nullable', 'exists:market_status,id'],
            'listing_information.state_property_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:state_properties,id']),
            'listing_information.property_category_id' => ['nullable', 'exists:properties_category,id'],
            'listing_information.land_use_id' => ['nullable', 'exists:land_uses,id'],
            'listing_information.land_category_id' => ['nullable', 'exists:land_category,id'],
            'listing_information.parking_type_id' => ['nullable', 'exists:parking_types,id'],
            // features
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'numeric', 'exists:features,id'],
            // rooms
            'rooms' => ['nullable', 'array'],
            'rooms.*.id' => ['nullable', 'exists:rooms,id'],
            'rooms.*.description' => ['nullable', 'string'],
            'rooms.*.size' => ['nullable', 'numeric'],
            'rooms.*.dimension_x' => ['nullable', 'numeric'],
            'rooms.*.dimension_y' => ['nullable', 'numeric'],
            'rooms.*.room_type_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:room_types,id']),
            // Multimedias
            'multimedias' => ['nullable', 'array'],
            'multimedias.*.id' => ['nullable'],
            'multimedias.*.description' => ['nullable', 'string', 'max:255'],
            'multimedias.*.file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'], // Es nuevo
            'multimedias.*.is_default' => ['nullable', 'boolean'],
            'multimedias.*.is_new' => ['nullable', 'boolean'],
            'multimedias.*.multimedia_type_id' => ['nullable', 'exists:multimedia_types,id'],
            'multimedias.*.room_id' => ['nullable'],
            // Documentation
            // Public documentation
            'public_documentation' => ['nullable', 'array'],
            'public_documentation.*.id' => ['nullable', 'exists:documentations,id'],
            'public_documentation.*.description' => ['nullable', 'string', 'max:255'],
            'public_documentation.*.file' => ['nullable', 'file', 'max:2048'],
            'public_documentation.*.is_new' => ['nullable', 'numeric'],
            'public_documentation.*.documentation_type_id' => ['required', 'exists:documentation_types,id'],
            // Private documentation
            'private_documentation' => ['nullable', 'array'],
            'private_documentation.*.id' => ['nullable', 'exists:documentations,id'],
            'private_documentation.*.description' => ['nullable', 'string', 'max:255'],
            'private_documentation.*.file' => ['nullable', 'file', 'max:2048'],
            'private_documentation.*.is_new' => ['nullable', 'numeric'],
            'private_documentation.*.documentation_type_id' => ['required', 'exists:documentation_types,id'],
            // Owners
            'owners' => $this->conditionallyRequiredExceptDraft(['required', 'array']),
            'owners.*.id' => ['nullable', 'exists:contacts,id'],
            'owners.*.name' => ['nullable', 'string', 'max:255'],
            'owners.*.last_name' => ['nullable', 'string', 'max:255'],
            'owners.*.mobile' => ['nullable', 'string', 'max:255'],
            'owners.*.email' => ['nullable', 'email', 'max:255'],
            'owners.*.is_new' => ['nullable', 'numeric'],
            // Foreign keys for Listing
            'area_id' => ['nullable', 'numeric', 'exists:areas,id'],
            'contract_type_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:contract_types,id']),
            'price_type_id' => ['nullable', 'exists:price_types,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'status_listing_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:status_listings,id']),
            'cancellation_reason_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:cancellation_reasons,id'], [ListingStatusId::CANCELADO]),
            'transaction_type_id' => ['nullable', 'exists:listing_transaction_types,id'],
            'rent_timeframe_id' => $this->conditionallyRequiredExceptDraft(['required', 'exists:rent_timeframes,id'], [], [2]), // Aplica solo alquiler
            // 'logs' => ['nullable', 'array'],
            // 'logs.*.field_name' => ['required', 'string'],
            // 'logs.*.old_value' => ['nullable', 'string'],
            // 'logs.*.new_value' => ['nullable', 'string'],
            // 'logs.*.notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.required' => 'Debe ingresar la clave del listing.',
            'key.exists' => 'La clave proporcionada no corresponde a ningún listing registrado.',

            'title.required' => 'Debe ingresar un título para el listing.',

            'price.amount.required' => 'Debe especificar el monto del precio del listing.',
            'price.amount.numeric' => 'El monto del precio debe ser un valor numérico (por ejemplo: 250000).',

            'commission_option.recruitment_commission.required' => 'Debe indicar el monto de la comisión por reclutamiento.',
            'commission_option.recruitment_commission.numeric' => 'La comisión por reclutamiento debe ser un número.',
            'commission_option.type_recruitment_commission.required' => 'Debe seleccionar el tipo de comisión por reclutamiento (Porcentaje o Cantidad fija).',
            'commission_option.type_recruitment_commission.in' => 'El tipo de comisión por reclutamiento debe ser "P" (porcentaje) o "C" (cantidad).',
            'commission_option.sales_commission.required' => 'Debe indicar el monto de la comisión por venta.',
            'commission_option.sales_commission.numeric' => 'La comisión por venta debe ser un valor numérico.',
            'commission_option.sales_commission_type.required' => 'Debe seleccionar el tipo de comisión por venta (Porcentaje o Cantidad fija).',
            'commission_option.sales_commission_type.in' => 'El tipo de comisión por venta debe ser "P" (porcentaje) o "C" (cantidad).',

            'location.city_id.required' => 'Debe seleccionar una ciudad.',
            'location.city_id.exists' => 'La ciudad seleccionada no existe o no es válida.',
            'location.zone_id.exists' => 'La zona seleccionada no existe o no es válida.',
            'listing_information.available_date.date' => 'Debe ingresar una fecha de disponibilidad válida para el listing.',
            'listing_information.year_construction.required' => 'Debe ingresar el año de construcción del inmueble.',
            'listing_information.year_construction.date' => 'El año de construcción debe tener un formato de fecha válido.',
            'listing_information.total_area.required' => 'Debe indicar el área total del inmueble.',
            'listing_information.total_area.numeric' => 'El área total debe ser un valor numérico.',
            'listing_information.construction_area_m.required' => 'Debe indicar el área construida.',
            'listing_information.construction_area_m.numeric' => 'El área construida debe ser un valor numérico.',
            'listing_information.youtube_link.url' => 'Debe ingresar un enlace de YouTube válido.',
            'listing_information.youtube_link.regex' => 'El enlace debe ser una URL válida de YouTube.',
            'listing_information.virtual_link.url' => 'Debe ingresar una URL válida para los enlaces de virtual tour link.',
            'listing_information.virtual_link.regex' => 'El enlace no coincide con el formato requerido para el visor seleccionado.',
            'listing_information.virtual_link.in' => 'Debe dejar vacío el campo si no seleccionó ningún visor.',
            'listing_information.external_link.url' => 'Debe ingresar una URL válida para los enlaces externos.',
            'listing_information.subtype_property_id.required' => 'Debe seleccionar el subtipo de propiedad.',
            'listing_information.subtype_property_id.exists' => 'El subtipo de propiedad seleccionado no es válido.',
            'listing_information.state_property_id.required' => 'Debe seleccionar el estado de la propiedad.',
            'listing_information.state_property_id.exists' => 'El estado de la propiedad seleccionado no es válido.',

            'contract_type_id.required' => 'Debe seleccionar un tipo de contrato.',
            'contract_type_id.exists' => 'El tipo de contrato seleccionado no es válido.',
            'status_listing_id.required' => 'Debe seleccionar el estado del listing.',
            'status_listing_id.exists' => 'El estado del listing seleccionado no es válido.',
            'cancellation_reason_id.required' => 'Debe indicar la razón de cancelación del listing.',
            'rent_timeframe_id.required' => 'Debe seleccionar un periodo de tiempo para el alquiler.',
            'rent_timeframe_id.exists' => 'El periodo de tiempo para el alquiler seleccionado no es válido.',

            'owners.required' => 'Debe ingresar al menos un propietario para el listing.',
            'owners.*.email.email' => 'Cada propietario debe tener un correo electrónico válido (ejemplo: usuario@dominio.com).',

            'rooms.*.room_type_id.required' => 'Debe seleccionar el tipo de habitación.',
            'rooms.*.room_type_id.exists' => 'El tipo de habitación seleccionado no es válido.',
        ];
    }

    public function passedValidation()
    {
        $this->dto = ListingDto::from($this->validated());
    }

    private function getVirtualLinkRules(): array
    {
        $viewer = $this->input('listing_information.virtual_viewer');

        return match ($viewer) {
            'immoviewer' => ['nullable', 'regex:/^https:\/\/(www\.)?app\.immoviewer\.com\/.+$/'],
            'matterport' => ['nullable', 'regex:/^https:\/\/(www\.)?my\.matterport\.com\/show\/\?m=.+$/'],
            'iguide' => ['nullable', 'regex:/^https:\/\/(www\.)?youriguide\.com\/.+$/'],
            'other' => ['nullable', 'url'], // no se valida nada
            null => ['nullable', 'in:null'], // deshabilitado
            default => ['nullable', 'url'], // fallback
        };
    }

    protected function conditionallyRequiredExceptDraft(array $rules, array $onlyOnStates = [], array $onlyOnTransaction = []): array
    {
        $user = $this->getAuthenticate();
        $roleName = $user->roles[0]?->name;
        // dd($user->roles[0]);
        $statusId = (int) $this->input('status_listing_id');
        $transaction_id = $this->input('transaction_type_id');

        if ($statusId === ListingStatusId::BORRADOR || $roleName === 'Administrador') {
            return array_merge(['nullable'], array_diff($rules, ['required']));
        }

        if (!empty($onlyOnStates) && !in_array($statusId, $onlyOnStates)) {
            return array_merge(['nullable'], array_diff($rules, ['required']));
        }

        if (!empty($onlyOnTransaction) && !in_array($transaction_id, $onlyOnTransaction)) {
            return array_merge(['nullable'], array_diff($rules, ['required']));
        }

        return $rules;
    }
}
