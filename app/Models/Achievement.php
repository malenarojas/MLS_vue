<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{

    protected $table = 'achievements';
    protected $fillable = [
        'name_achievements',
        'enable_achievement',
        'achievement_description',
    ];
    public function users () : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'achievement_user', 'achievements_id', 'user_id');
    }

}
