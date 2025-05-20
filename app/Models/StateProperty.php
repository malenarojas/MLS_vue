<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateProperty extends Model
{
    protected $fillable = [
        'name_state_properties',
    ];

    public function listingInformation()
    {
        return $this->hasMany(ListingInformation::class, 'state_property_id');
    }
}
