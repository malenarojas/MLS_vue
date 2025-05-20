<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'size',
        'dimension_x',
        'dimension_y',
        'room_type_id',
        'information_id',
    ];

    public function room_type()
    {
        return $this->belongsTo(RoomType::class);
    }
}
