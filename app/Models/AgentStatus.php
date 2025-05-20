<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentStatus extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'agents_statuses';

    // Atributos que pueden ser asignados masivamente
    protected $fillable = [
        'name',
    ];

    // Relación con el modelo Agent
    public function agents()
    {
        return $this->hasMany(Agent::class);
    }
}
