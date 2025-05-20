<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{

       /**
     * Los atributos que son asignables en masa.
     */
    protected $fillable = [
        'name',
        'parent_id',
    ];

    /**
     * Relación con la misma tabla para gestionar especialidades principales (padres).
     */
    public function parent()
    {
        return $this->belongsTo(Speciality::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Speciality::class, 'parent_id');
    }


    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_specialities', 'speciality_id', 'area_id');
    }

}
