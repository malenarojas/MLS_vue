<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{

    protected $fillable = ['name'];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
