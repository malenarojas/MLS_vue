<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchievementUser extends Model
{
    protected $table = 'achievement_user';
    protected $fillable = [
        'achievement_date', // Fecha del logro
        'achievement_id',   // ID del logro (clave foránea)
        'enable_achievement',
        'user_id',          // ID del usuario (clave foránea)
    ];
}
