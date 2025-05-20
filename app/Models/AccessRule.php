<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRule extends Model
{
    protected $fillable = ['type', 'value', 'access_type'];

    public function resource()
    {
        return $this->morphTo();
    }
}
