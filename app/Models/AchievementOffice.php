<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AchievementOffice extends Model
{
    protected $table = 'achievements_offices';

    protected $fillable = [
        'name_achievements',
        'achievement_description',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;
        return Storage::disk('public')->url("achievements/{$this->image}");
    }

    public function officeRelations()
    {
        return $this->hasMany(AchievementOfficePivot::class, 'achievement_id');
    }
    public function offices()
{
    return $this->belongsToMany(
        Office::class,
        'achievement_office_pivot',
        'achievement_id',
        'office_id'
    )
    ->using(AchievementOfficePivot::class)
    ->withPivot([
        'id',
        'achievement_date',
        'enable_achievement'
    ])
    ->withTimestamps();
}



}
