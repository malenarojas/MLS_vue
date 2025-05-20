<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeProperty extends Model
{
    protected $fillable = ['name'];

    public function subtypeProperties()
    {
        return $this->hasMany(SubtypeProperty::class, 'type_property_id');
    }
}
