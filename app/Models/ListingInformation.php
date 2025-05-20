<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ListingInformation extends Model
{
    protected $table = 'listings_information';

    protected $fillable = [
        'available_date',
        'year_construction',
        'parking_slots',
        'plant_numbers',
        'number_departments',
        'sale_sign',
        'cubic_volume',
        'land_m2',
        'land_x',
        'land_y',
        'construction_area_m',
        'total_area',
        'total_number_rooms',
        'number_bathrooms',
        'number_bedrooms',
        'number_toiletrooms',
        'youtube_link',
        'virtual_link',
        'virtual_viewer',
        'external_link',
        'subtype_property_id',
        'market_status_id',
        'state_property_id', // Estado de la propiedad
        'property_category_id', // Categoria de la propiedad
        'land_use_id',
        'land_category_id',
        'parking_type_id',
        'listing_id',
    ];

    protected $casts = [
        // 'year_construction' => 'date',
        // 'sale_sign' => 'boolean', // Maneja 0 o 1, caso dew cambiar la logica, se debe cambiar a 'integer'
    ];

    // Relación uno a uno inversa con Listing
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function subtype_property()
    {
        return $this->belongsTo(SubtypeProperty::class);
    }

    public function marketStatus()
    {
        return $this->belongsTo(MarketStatus::class, 'market_status_id');
    }

    public function stateProperty()
    {
        return $this->belongsTo(StateProperty::class, 'state_property_id');
    }
    public function propertyCategory()
    {
        return $this->belongsTo(
            PropertyCategory::class, // Modelo relacionado
            'property_category_id',  // Columna en `listings_information`
            'id'                     // Columna primaria en `properties_category`
        );
    }

    public function landUse()
    {
        return $this->belongsTo(LandUse::class, 'land_use_id');
    }

    public function landCategory()
    {
        return $this->belongsTo(LandCategory::class, 'land_category_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'information_id', 'id');
    }

    // Mutadores para valores nulos
    protected function cubicVolume(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? 0
        );
    }
    protected function landM2(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? 0
        );
    }
    protected function landX(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? 0
        );
    }
    protected function landY(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? 0
        );
    }
    protected function constructionAreaM(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? 0
        );
    }
    protected function totalArea(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? 0
        );
    }
}
