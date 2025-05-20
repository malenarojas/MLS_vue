<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaSpecialityUser extends Model
{

    protected $table = 'area_speciality_user';

    protected $fillable = [
        'area_speciality_id',
        'user_id',
    ];

    /**
     * Relación con `AreaSpeciality`.
     */
    public function areaSpeciality(): BelongsTo
    {
        return $this->belongsTo(AreaSpeciality::class);
    }

    /**
     * Relación con el usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
