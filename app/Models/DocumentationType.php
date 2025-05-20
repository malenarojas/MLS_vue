<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentationType extends Model
{
    const ID_PUBLIC_DOCUMENTATION = 1;
    const ID_PRIVATE_DOCUMENTATION = 32;

    // Nombre de la tabla
    protected $table = 'documentation_types';

    // Campos asignables en el modelo
    protected $fillable = [
        'name',
        'parent_id',
    ];

    // Relación recursiva, retorna los hijos de un padre
    public function documentacions_types(): HasMany
    {
        return $this->hasMany(DocumentationType::class, 'parent_id');
    }

    // Relación recursiva, retorna los hijos de un padre con los hijos de los hijos
    public function documentacions_types_hijos(): HasMany
    {
        // Especificar campo de la relación recursiva y con with para que traiga todos los hijos
        return $this->hasMany(DocumentationType::class, 'parent_id')->with('documentacions_types_hijos');
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class, 'documentation_type_id');
    }

    public function scopePublic($query)
    {
        return $query->where('parent_id', self::ID_PUBLIC_DOCUMENTATION);
    }

    public function scopePrivate($query)
    {
        return $query->where('parent_id', self::ID_PRIVATE_DOCUMENTATION);
    }
}
