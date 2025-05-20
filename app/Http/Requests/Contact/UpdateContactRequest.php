<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
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
            'name' => 'required|string',
            'last_name' => 'required|string',
            'mobile' => 'nullable|string|numeric',
            'email' => 'required|email',
            'profile_type_id' => 'required',
            //'preferred_language_id' => 'required',
            'company' => 'required',
            //'preferred_communication_method_id' => 'required',
            //'prospect_id' => 'required',
            //'stage_id' => 'required',

        ];
    }
	public function messages()
    {
        return [
            'preferred_communication_method_id.required' => 'required',
            'preferred_language_id.required' => 'required',
            'profile_type_id.required' => 'required',
            'prospect_id.required' => 'required',
            'stage.required' => 'required',

        ];
    }

}
