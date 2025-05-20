<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoalRequest extends FormRequest
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
            'transactions' => ['nullable', 'numeric'],
            'transaction_volume' => ['nullable', 'numeric'],
            'new_contacts' => ['nullable', 'numeric'],
            'new_agents' => ['nullable', 'numeric'],
            'payment_amount' => ['nullable', 'numeric'],
            'new_listings' => ['nullable', 'numeric'],
            'month' => ['required', 'numeric'],
            'year' => ['required', 'numeric'],
            'office_id' => ['nullable', 'numeric'],
            'agent_id' => ['nullable', 'numeric'],
            'time_in_market' => ['nullable', 'numeric'],
            'production_by_agent' => ['nullable', 'numeric'],
        ];
    }

}
