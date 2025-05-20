<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'number',
        'unit_department',
        'first_address',
        'second_address',
        'zip_code',
        'district',
        'access_number',
        'show_addres_on_website',
        'latitude',
        'longitude',
        'city_id',
        'zone_id',
        'listing_id',
        'type_floor_id',
    ];

    protected $appends = [
        'full_address',
    ];

    protected $casts = [
        // 'show_addres_on_website' => 'boolean',
    ];

    public function type_floor(): BelongsTo
    {
        return $this->belongsTo(TypeFloor::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function getFullAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->first_address ?? null,
            $this->zone?->name ?? null,
            $this->city?->name ?? null,
            $this->city->province?->name ?? null,
            $this->city->province?->state?->name ?? null,
        ]);

        return implode(', ', $addressParts);
    }
}
