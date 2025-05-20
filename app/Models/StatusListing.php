<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusListing extends Model
{
    protected $fillable = [
        'name',
        'is_final',
    ];

    /**
     * Relación muchos a muchos: Obtener las transiciones de un estado como origen (from_status)
     */
    public function transitions_from()
    {
        return $this->belongsToMany(StatusListing::class, 'listing_status_transitions', 'from_status_id', 'to_status_id');
    }

    /**
     * Relación muchos a muchos: Obtener las transiciones de un estado como destino (to_status)
     */
    public function transitions_to()
    {
        return $this->belongsToMany(StatusListing::class, 'listing_status_transitions', 'to_status_id', 'from_status_id')
            ->withTimestamps();
    }
}
