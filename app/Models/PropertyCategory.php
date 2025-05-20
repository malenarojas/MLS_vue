<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyCategory extends Model
{
    protected $table = 'properties_category';

    protected $fillable = [
        'name_properties_categories',
    ];
    public function listingInformation()
    {
        return $this->hasMany(
            ListingInformation::class, // Modelo relacionado
            'property_category_id',    // Columna en `listings_information`
            'id'                       // Columna primaria en `properties_category`
        );
    }


}
