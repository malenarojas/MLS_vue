<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = ['name'];
    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }
    public function offices()
    {
        return $this->hasMany(Office::class, 'state_id', 'id');
    }
}
