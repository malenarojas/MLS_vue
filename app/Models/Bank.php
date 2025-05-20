<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Bank extends Model
{
    protected $fillable = ['name', 'order', 'enabled'];


    public function transsactions () : HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
