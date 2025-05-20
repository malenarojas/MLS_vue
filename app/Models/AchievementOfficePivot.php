<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchievementOfficePivot extends Model
{
    protected $table = 'achievements_office_pivot';

    protected $fillable = [
        'achievement_date',
        'enable_achievement',
        'achievement_id',
        'office_id',
    ];

    public function achievement()
    {
        return $this->belongsTo(AchievementOffice::class, 'achievement_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
