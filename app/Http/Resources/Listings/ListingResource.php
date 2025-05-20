<?php

namespace App\Http\Resources\Listings;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'MLSID' => $this->MLSID,
            'cancellation_date' => $this->cancellation_date,
            'cancellation_reason' => $this->cancellation_reason,
            'is_published' => $this->is_published,
            'contract_end_date' => $this->contract_end_date ? Carbon::createFromFormat('Y-m-d', $this->contract_end_date)->format('m/d/Y')
                : null,
            'date_of_listing' => $this->date_of_listing ? Carbon::createFromFormat('Y-m-d', $this->date_of_listing)->format('m/d/Y')
                : null,
            'description_website' => $this->description_website,
            // Financial
            'reference' => $this->reference,
            'property_registration_number' => $this->property_registration_number,
            'financial_note' => $this->financial_note,
            // Marketing
            'translations' => $this->translations, // Campos traducibles
            'title' => $this->title,
            'description_website' => $this->description_website,
            'marketing_description' => $this->marketing_description,
            'location_information' => $this->location_information,
            // FK
            'agent_id' => $this->agent_id,
            'area_id' => $this->area_id,
            'contract_type_id' => $this->contract_type_id,
            'price_type_id' => $this->price_type_id,
            'project_id' => $this->project_id,
            'status_listing_id' => $this->status_listing_id,
            'transaction_type_id' => $this->transaction_type_id,
            // Relations
            'listing_information' => $this->listing_information,
            'area' => $this->area,
            'addition_payments' => $this->addition_payments,
            'agent' => $this->agent,
            'commission_option' => $this->commission_option,
            'contract_type' => $this->contract_type,
            'documentation' => $this->documentation,
            'price_type' => $this->price_type,
            'project' => $this->project,
            'status_listing' => $this->status_listing,
            'transaction_type' => $this->transaction_type,
            'location' => new LocationResource($this->location),
            'price' => $this->latestPrice,
        ];
    }
}