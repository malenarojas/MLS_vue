<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['id', 'name'];

    public function offices()
    {
        return $this->hasMany(Office::class, 'region_id', 'id');
    }

    public function agents()
    {
        return $this->hasMany(Agent::class, 'region_id', 'id');
    }
    public function getOfficesWithTeamStatuses()
    {
        // Relación de oficinas con sus estados de equipo
        return $this->offices()->get();
    }
}
