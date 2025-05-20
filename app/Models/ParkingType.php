<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParkingType extends Model
{
    protected $fillable = ['name'];

    public function parkings(): HasMany
    {
        return $this->hasMany(Parking::class);
    }
}
