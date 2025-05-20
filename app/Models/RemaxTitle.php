<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemaxTitle extends Model
{

    protected $fillable = [
        'name',
        'user_type_id',
    ];

    /**
     * Ocultar columnas en conversiones a JSON.
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Relación con `user_types`.
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }
}
