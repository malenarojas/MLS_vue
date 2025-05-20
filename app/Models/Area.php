<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Area extends Model
{

    protected $fillable = [
        'name',
        'description',
        'is_base'
    ];
     // Relación con especialidades y sus hijos
     public function specialities()
     {
         return $this->belongsToMany(Speciality::class, 'area_specialities', 'area_id', 'speciality_id')
             ->withPivot('area_id', 'speciality_id','id')
             ->with('children'); // Cargar subespecialidades
     }


}
