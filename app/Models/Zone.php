<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'zone_id',
        'latitude',
        'longitude',
        'city_id',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function city(): HasMany
    {
        return $this->hasMany(City::class);
    }
    public function office(): HasMany
    {
        return $this->hasMany(Office::class);
    }

    // Scope
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }
}
