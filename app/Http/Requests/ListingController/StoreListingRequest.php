<?php

namespace App\Http\Requests\ListingController;

use App\Dtos\Listings\ListingDto;
use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
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
            // 'key' => 'required|string|unique:listings,key',
            // 'MLSID' => 'required|string|unique:listings,MLSID',
            'transaction_type_id' => 'required|exists:listing_transaction_types,id',
            'market_segment' => 'required|string|max:255|in:residential,commercial',
            'date_of_listing' => 'nullable|date',
            'contract_end_date' => 'nullable|date',
            'cancellation_date' => 'nullable|date',
            'cancellation_reason' => 'nullable|string',
            'reference' => 'nullable|string',
            'property_registration_number' => 'nullable|string',
            'title_catchment' => 'nullable|string',
            'description_website' => 'nullable|string',
            'marketing_description' => 'nullable|string',
            'location_neigthborhood_information' => 'nullable|string',
            'financial_note' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'contract_type_id' => 'nullable|exists:contract_types,id',
            'area_id' => 'nullable|exists:areas,id',
            'status_listing_id' => 'nullable|exists:status_listings,id',
            'price_type_id' => 'nullable|exists:price_types,id',
            // Commision
            // Listing information
        ];
    }

    /**
     * Convert validated request data to a ListingDto.
     */
    public function toDto(): ListingDto
    {
        return ListingDto::from($this->validated());
    }
}
