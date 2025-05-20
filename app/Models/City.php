<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'city_id',
        'latitude',
        'longitude',
        'province_id',
    ];

    // public function province(): BelongsTo
    // {
    //     return $this->belongsTo(Province::class);
    // }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class);
    }
    public function offices()
    {
        return $this->hasMany(Office::class, 'city_id', 'id');
    }

    // Scope
    public function scopeByProvince($query, $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }
}
