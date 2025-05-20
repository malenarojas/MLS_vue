<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Parking extends Model
{
    protected $fillable = ['nro_spaces','parking_type_id'];


    public function parkingType(): BelongsTo
    {
        return $this->belongsTo(ParkingType::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
