<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubtypeProperty extends Model
{
    protected $table = 'subtype_properties';

    protected $fillable = [
        'name',
        'type_property_id',
        'area_id',
    ];

    public function type_property(): BelongsTo
    {
        return $this->belongsTo(TypeProperty::class);
    }

    public function listingInformation(): HasMany
    {
        return $this->hasMany(ListingInformation::class, 'subtype_property_id');
    }
}
