<?php

namespace App\Models;

use App\Models\Province;
use App\Models\City;
use App\Traits\HasLocationData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasLocationData;

    protected $fillable = [
        'region_id',
        'office_id',
        'name',
        'image',
        'city',
        'province',
        'country',
        'latitude',
        'longitude',
        'first_updated_to_web',
        'access_ilist_net',
        'succeed_certified',
        'is_regional_office',
        'is_satellite_office',
        'first_year_licensed',
        'is_commercial',
        'is_collection',
        'date_time_stamp',
        'active_office',
        'office_iconnect_id',
        'office_intl_id',
        'macro_office',
        'office_type',
        'is_external',
        'schedule_weekdays',
        'schedule_saturday',
        // Nuevos campos de contacto
        'international_code',
        'office_start_date',
        'hide_office_from_web',
        'show_whatsapp',
        'phone',
        'email',
        'cell_phone',
        'number',
        'address',
        'address2',
        'postal_code',
        'floor',
        'unit',
        'state_id',
        'city_id',
        'zone_id',
        'province_id',

        // Licencia
        'expiration_date',
        'license_number',
        'license_department',

        // Marketing
        'marketing_slogan',
        'website_description',
        'closure',
        'short_link',
        'office_website',

        // Bullet points
        'bullet_point_one',
        'bullet_point_two',
        'bullet_point_three',
        'bullet_point_four',
        // SEO
        'meta_tag_keywords',
        'meta_tag_description',
    ];
    protected $appends = [
        'image_url',
        //'state_name',
        // 'province_name',
        //'city_name',
    ]; // Esto agrega el campo al JSON automáticamente

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return Storage::disk('public')->url('oficinas/' . $this->image);
    }


    public function regions()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }
    public function socialNetworks()
    {
        return $this->hasMany(SocialNetwork::class, 'office_id', 'id');
    }

    public function agents()
    {
        return $this->hasMany(Agent::class, 'office_id', 'office_id')->with('user');
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    // Devuelve el nombre del estado
    public function getStateNameAttribute(): ?string
    {
        return $this->state?->name;
    }

    // Devuelve el nombre de la provincia
    /*  return $this->province?->name;
    }
    public function getZoneNameAttribute(): ?string
    {
        return $this->zone?->name;
    }

    // Devuelve el nombre de la ciudad
    public function getCityNameAttribute(): ?string
    {
        return $this->city?->name;
    }*/

    // En Office.php
    public function teams()
    {
        return $this->hasMany(TeamManagement::class, 'office_id', 'office_id');
    }


    public function getFullAddressAttribute(): string
    {
        return $this->fullAddressFromOffice($this);
    }

    public function achievementoffices()
    {
        return $this->hasMany(AchievementOfficePivot::class, 'office_id');
    }
}
