<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Qualification extends Model
{

    /**
     * Los atributos que se pueden asignar en masa.
     */
    protected $fillable = [
        'qualification',
        'comment',
        'reference_type',
        'agent_id',
    ];

    /**
     * Ocultar columnas en conversiones a JSON.
     */
    protected $hidden = ['created_at', 'updated_at'];

    public function agents(): BelongsTo
    {
        return $this->belongsTo(Agent::class,'agent_id');
    }
}
