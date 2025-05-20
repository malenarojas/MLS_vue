<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandUse extends Model
{
    protected $table = 'land_uses';

    protected $fillable = [
        'land_use_name',
    ];

    public function listingInformation()
    {
        return $this->hasMany(ListingInformation::class, 'land_use_id');
    }
}
