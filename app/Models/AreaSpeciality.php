<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaSpeciality extends Model
{
    protected $fillable = ['area_id', 'speciality_id'];

   /**
     * Relación con el área.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación con la especialidad.
     */
    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    /**
     * Relación uno a muchos con usuarios.
     */
    public function users(): HasMany
    {
        return $this->hasMany(AreaSpecialityUser::class, 'area_speciality_id');
    }


}
