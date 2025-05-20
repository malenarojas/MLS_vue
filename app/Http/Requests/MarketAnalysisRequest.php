<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketAnalysisRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajusta esto según tus necesidades de autorización
    }

    public function rules()
    {
        return [
            // Parámetros de FilterByCaptation
            'status' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'minimum_price' => 'nullable|numeric|min:0',
            'maximum_price' => 'nullable|numeric|gte:minimum_price',

            // Parámetros de FilterByDetails
            'market_segment' => 'nullable|integer',
            'transaction_type' => 'nullable|integer|exists:listing_transaction_types,id',
            'property_status' => 'nullable|integer|exists:state_properties,id',
            'contract_type' => 'nullable|integer|exists:contract_types,id',

            // Parámetros de FilterByLocation
            'country' => 'nullable|string|max:50',
            'region_id' => 'nullable|integer|exists:regions,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'province_id' => 'nullable|integer|exists:provinces,id',
            'city_id' => 'nullable|integer|exists:cities,id',
            'zone_id' => 'nullable|integer|exists:zones,id',
            'street_name' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'only_office' => 'nullable|boolean',
        ];
    }
}
