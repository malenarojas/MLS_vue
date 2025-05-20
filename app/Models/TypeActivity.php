<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeActivity extends Model
{
    use HasFactory;


    protected $table = 'type_actividad';

    protected $fillable = ['name'];

    public function activities()
    {
        return $this->hasMany(Activity::class, 'type_actividad_id');
    }
}
