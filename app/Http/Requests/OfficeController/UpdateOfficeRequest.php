<?php

namespace App\Http\Requests\OfficeController;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateOfficeRequest extends FormRequest
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
            'region_id' => 'nullable|numeric',
            //'office_id' => 'required|integer|unique:offices,office_id,' . $this->route('office'),
            'office_id' => 'required|integer|unique:offices,office_id,' . $this->route('id'),
            'name' => 'string|max:255',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'city_id' => 'nullable|numeric',
            'zone_id' => 'nullable|numeric',
            'state_id' => 'nullable|exists:states,id',
            'province_id' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'unit' => 'nullable|string',
            'office_start_date' => 'nullable|date',
            'first_updated_to_web' => 'nullable|date',
            'access_ilist_net' => 'boolean',
            'succeed_certified' => 'boolean',
            'is_regional_office' => 'nullable|integer',
            'is_satellite_office' => 'boolean',
            'first_year_licensed' => 'nullable|date',
            'is_commercial' => 'boolean',
            'is_collection' => 'boolean',
            'date_time_stamp' => 'nullable|date',
            'active_office' => 'nullable|numeric',
            'is_external' => 'nullable|numeric',
            'office_iconnect_id' => 'nullable|numeric',
            'office_intl_id' => 'nullable|numeric',
            'macro_office' => 'nullable|string|max:255',
            'office_type' => 'nullable|string|max:255',
            'international_code' => 'nullable|numeric',
            'hide_office_from_web' => 'nullable|numeric',
            'show_whatsapp' => 'nullable|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'cell_phone' => 'nullable|string|max:20',
            'number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'floor' => 'nullable|string|max:20',
            'expiration_date' => 'nullable|date',
            'license_number' => 'nullable|string|max:100',
            'license_department' => 'nullable|string|max:100',
            'marketing_slogan' => 'nullable|string|max:255',
            'website_description' => 'nullable|string',
            'closure' => 'nullable|string',
            'image_data' => [
            'nullable',
            'string',
            'regex:/^data:image\/(jpeg|png|jpg|gif);base64,/',],
            'image' => 'nullable|string',
            'short_link' => 'nullable|string|max:255|unique:offices,short_link,' . $this->route('id'),
            'office_website' => 'nullable|url|max:255|unique:offices,office_website,' . $this->route('id'),

            'bullet_point_one' => 'nullable|string|max:255',
            'bullet_point_two' => 'nullable|string|max:255',
            'bullet_point_three' => 'nullable|string|max:255',
            'bullet_point_four' => 'nullable|string|max:255',
            'meta_tag_keywords' => 'nullable|string',
            'meta_tag_description' => 'nullable|string',

            'schedule_weekdays' => 'nullable|string',
            'schedule_saturday' => 'nullable|string',

            'socialNetworks' => 'nullable|array',
            'socialNetworks.*.id' => 'nullable|string',
            'socialNetworks.*.name' => 'nullable|string',
            'socialNetworks.*.url' => 'nullable|string',
            'socialNetworks.*.state' => 'nullable|integer|in:0,1',

            'achievementoffices' => 'nullable|array',
            'achievementoffices.*.id' => 'nullable|numeric',
            'achievementoffices.*.achievement_id' => 'nullable|numeric',
            'achievementoffices.*.achievement_date' => 'nullable|date',
            'achievementoffices.*.enable_achievement' => 'nullable|numeric',
            'achievementoffices.*.office_id' => 'nullable|numeric',
        ];
    }
    protected function prepareForValidation()
    {
        $data = $this->all();

    if (isset($data['achievementoffices']) && is_array($data['achievementoffices'])) {
        $data['achievementoffices'] = array_map(function ($item) {
            if (isset($item['achievement_date'])) {
                $item['achievement_date'] = $this->formatDate($item['achievement_date']);
            }
            return $item;
        }, $data['achievementoffices']);
    }

    $this->merge([
        'first_year_licensed' => $this->formatDate($this->input('first_year_licensed')),
        'office_start_date' => $this->formatDate($this->input('office_start_date')),
        'expiration_date' => $this->formatDate($this->input('expiration_date')),
        'first_updated_to_web' => $this->formatDate($this->input('first_updated_to_web')),
        'achievementoffices' => $data['achievementoffices'] ?? [],
    ]);
    }
private function formatDate(?string $date): ?string
{
    if (!$date) {
        return null;
    }

    try {
        return Carbon::parse($date)->format('Y-m-d'); // o 'Y-m-d H:i:s' si es datetime
    } catch (\Exception $e) {
        Log::warning("⚠️ No se pudo formatear la fecha: $date", ['exception' => $e->getMessage()]);
        return null;
    }
}


}
