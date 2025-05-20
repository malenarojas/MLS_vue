<?php

namespace App\Http\Requests\ListingController;

use App\Dtos\Listings\ListingEditParamDto;
use App\Dtos\Listings\LocationDto;
use Illuminate\Foundation\Http\FormRequest;

class EditListingRequest extends FormRequest
{
    public ?ListingEditParamDto $listingEditParamDto = null;
    public ?LocationDto $locationDto = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'state_id' => $this->input('state_id') ?? null,
            'province_id' => $this->input('province_id') ?? null,
            'city_id' => $this->input('city_id') ?? null,
            'zone_id' => $this->input('zone_id') ?? null,
            'contact_search' => $this->input('contact_search', null),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'state_id' => ['nullable', 'exists:states,id'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'zone_id' => ['nullable', 'exists:zones,id'],
            'contact_search' => ['nullable', 'string'],
        ];
    }

    public function passedValidation()
    {
        // dd($this->validated());
        $this->locationDto = LocationDto::from($this->validated());
        $this->listingEditParamDto = ListingEditParamDto::from($this->validated());
    }
}
