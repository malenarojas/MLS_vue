<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = ['name', 'state_id'];
    // Relación con State (1 provincia pertenece a 1 estado)

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
    public function offices()
    {
        return $this->hasMany(Office::class, 'province_id', 'id');
    }

    // Scope
    public function scopeByState($query, $state_id)
    {
        return $query->where('state_id', $state_id);
    }
}
