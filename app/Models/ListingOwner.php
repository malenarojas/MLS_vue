<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingOwner extends Model
{


    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
