<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeFrame extends Model
{
    use HasFactory;

    protected $table = 'time_frames';

    protected $fillable = ['name'];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'time_frame_id');
    }
}
