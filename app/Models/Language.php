<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar en masa.
     */
    protected $fillable = ['name', 'code', 'is_default'];

    /**
     * Las columnas que no deberían ser visibles en las conversiones de array o JSON.
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Las columnas que deberían ser tratadas como fechas.
     */
    protected $dates = ['created_at', 'updated_at'];
}
