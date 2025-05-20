<?php

namespace App\Http\Requests\ListingController;

use App\Services\Listings\ListingService;
use App\Traits\AutenticationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ParamsListing extends FormRequest
{
    use AutenticationTrait;

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
            'search' => 'nullable|string',
            'office_id' => 'nullable|numeric',
            'agent_id' => 'nullable|numeric',
            'status_id' => 'nullable|numeric',
        ];
    }

    protected function prepareForValidation()
    {
        $user = $this->getAuthenticate();
        // Log::info('ParamsListing:prepareForValidation', $this->all());
        $statusId = $this->input('status_id');
        $agentId = $this->input('agent_id');
        if ($statusId === null) {
            $statusId = ListingService::LISTING_STATUS_ACTIVE;
        } else if ($statusId == 'all') {
            $statusId = null;
        }

        if ($agentId === null) {
            // $agentId = $user->agent?->id ?? null;
        } else if ($agentId === 'all') {
            $agentId = null;
        }



        $this->merge([
            'search' => $this->input('search', null),
            'office_id' => $this->input('office_id', null),
            'agent_id' => $agentId,
            'status_id' => $statusId, // default to active
        ]);
    }
}
