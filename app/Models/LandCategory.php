<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandCategory extends Model
{
    protected $table = 'land_category';

    protected $fillable = [
        'land_category_name'
    ];

    // Relación con ListingInformation
    public function listingInformations()
    {
        return $this->hasMany(ListingInformation::class);
    }
}
