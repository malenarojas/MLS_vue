<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Relación: Un CustomerPreference puede tener muchos Users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'customer_preference_id');
    }
}
