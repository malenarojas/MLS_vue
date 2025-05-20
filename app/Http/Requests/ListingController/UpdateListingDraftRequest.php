<?php

namespace App\Http\Requests\ListingController;

use App\Dtos\Listings\ListingDto;
use App\Utils\FormatString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateListingDraftRequest extends FormRequest
{
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
        $newData = [];
        $data = $this->all();
        // dd($data);
        // Log::info('Campos en la solicitud:', $data);

        $newData['is_published'] = $this->is_published ? 1 : 0;
        $newData['is_sent_to_review'] = $this->is_sent_to_review ? 1 : 0;

        foreach (self::DATE_FIELDS as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $newData[$field] = FormatString::formatDate($data[$field]);
            }
        }

        foreach (self::LISTING_INFORMATION_DATE_FIELDS as $field) {
            if (isset($data['listing_information'][$field]) && $data['listing_information'][$field] !== null) {
                $newData['listing_information'][$field] = FormatString::formatDate($data['listing_information'][$field]);
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

        Log::info('Datos convertidos:', $newData);


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
            'key' => 'required|exists:listings,key',
            // Additional fields
            'is_sent_to_review' => 'nullable|numeric',
            'is_published' => 'nullable|numeric',
            'date_of_listing' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'cancellation_date' => 'nullable|date',
            'cancellation_reason' => 'nullable|string',
            // Marketing
            'title' => 'nullable|string',
            'description_website' => 'nullable|string',
            'marketing_description' => 'nullable|string',
            'location_informationn' => 'nullable|string',
            // Translations
            'translations' => 'nullable|array',
            // Price
            'price' => ['required', 'array'],
            'price.amount' => ['required', 'numeric'],
            // 'price.currency_id' => 'nullable|exists:currencies,id',
            // Finalcial
            'reference' => 'nullable|string',
            // Cambiar por seguridad
            'property_number' => 'nullable|numeric',
            'financial_note' => 'nullable|string',
            // Commission Option
            'commission_option' => ['required', 'array'],
            'commission_option.recruitment_commission' => ['required', 'numeric'],
            'commission_option.type_recruitment_commission' => ['required', 'string', 'max:255', 'in:P,C'],
            'commission_option.sales_commission' => ['required', 'numeric'],
            'commission_option.sales_commission_type' => ['required', 'string', 'max:255', 'in:P,C'],
            // Location
            'location' => ['nullable', 'array'],
            'location.number' => ['nullable', 'string'],
            'location.unit_department' => ['nullable', 'string'],
            'location.first_address' => ['nullable', 'string'],
            'location.second_address' => ['nullable', 'string'],
            'location.zip_code' => ['nullable', 'string'],
            'location.district' => ['nullable', 'string'],
            'location.access_number' => ['nullable', 'string'],
            'location.show_addres_on_website' => ['nullable', 'boolean'],
            'location.latitude' => ['nullable', 'numeric'],
            'location.longitude' => ['nullable', 'numeric'],
            'location.city_id' => ['nullable', 'exists:cities,id'],
            'location.zone_id' => ['nullable', 'exists:zones,id'],
            'location.type_floor_id' => ['nullable', 'exists:type_floors,id'],
            // Listing listing_information
            'listing_information' => ['nullable', 'array'],
            'listing_information.id' => 'nullable|exists:listings_information,id',
            'listing_information.available_date' => 'nullable|date',
            'listing_information.year_construction' => 'nullable|date',
            'listing_information.parking_slots' => 'nullable|numeric',
            'listing_information.plant_numbers' => 'nullable|numeric',
            'listing_information.number_departments' => 'nullable|numeric',
            'listing_information.sale_sign' => 'nullable|numeric',
            'listing_information.total_area' => 'nullable|numeric',
            'listing_information.cubic_volume' => 'nullable|numeric',
            'listing_information.land_m2' => 'nullable|numeric',
            'listing_information.land_x' => 'nullable|numeric',
            'listing_information.land_y' => 'nullable|numeric',
            'listing_information.construction_area_m' => 'nullable|numeric',
            'listing_information.total_number_rooms' => 'nullable|numeric',
            'listing_information.number_bathrooms' => 'nullable|numeric',
            'listing_information.number_bedrooms' => 'nullable|numeric',
            'listing_information.number_toiletrooms' => 'nullable|numeric',
            // Foreign keys for Listing listing_information
            'listing_information.subtype_property_id' => 'nullable|exists:subtype_properties,id',
            'listing_information.market_status_id' => 'nullable|exists:market_status,id',
            'listing_information.state_property_id' => 'nullable|exists:state_properties,id',
            'listing_information.property_category_id' => 'nullable|exists:properties_category,id',
            'listing_information.land_use_id' => 'nullable|exists:land_uses,id',
            'listing_information.land_category_id' => 'nullable|exists:land_category,id',
            'listing_information.parking_type_id' => 'nullable|exists:parking_types,id',
            // features
            'features' => 'nullable|array',
            'features.*' => 'nullable|numeric|exists:features,id',
            // rooms
            'rooms' => 'nullable|array',
            'rooms.*.id' => 'nullable|exists:rooms,id',
            'rooms.*.description' => 'nullable|string',
            'rooms.*.size' => 'nullable|numeric',
            'rooms.*.dimension_x' => 'nullable|numeric',
            'rooms.*.dimension_y' => 'nullable|numeric',
            'rooms.*.room_type_id' => 'nullable|exists:room_types,id',
            // Multimedias
            'multimedias' => 'nullable|array',
            'multimedias.*.id' => 'nullable|exists:multimedias,id',
            'multimedias.*.description' => 'nullable|string',
            'multimedias.*.file' => 'nullable|image|max:2048', // Es nuevo
            'multimedias.*.is_default' => 'nullable|boolean',
            'multimedias.*.is_new' => 'nullable|numeric',
            'multimedias.*.multimedia_type_id' => 'nullable|exists:multimedia_types,id',
            'multimedias.*.room_id' => 'nullable|exists:rooms,id',

            // Foreign keys for Listing
            'area_id' => 'nullable|numeric|exists:areas,id',
            'contract_type_id' => 'nullable|exists:contract_types,id',
            'price_type_id' => 'nullable|exists:price_types,id',
            'project_id' => 'nullable|exists:projects,id',
            // 'status_listing_id' => 'nullable|exists:status_listings,id',
            'transaction_type_id' => 'nullable|exists:listing_transaction_types,id',

            'logs' => 'nullable|array',
            'logs.*.field_name' => 'required|string',
            'logs.*.old_value' => 'nullable|string',
            'logs.*.new_value' => 'nullable|string',
            'logs.*.notes' => 'nullable|string',
        ];
    }

    public function passedValidation()
    {
        $this->dto = ListingDto::from($this->validated());
    }
}
