<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeactureListing extends Model
{
    protected $table = 'feacture_listing';

    protected $fillable = [
        'name',
        'feature_id',
        'listing_id',

    ];

    public function feature(): HasMany
    {
        return $this->hasMany(Feature::class, 'feature_id');
    }

    public function feature_children(): HasMany
    {
        return $this->hasMany(Feature::class, 'feature_id')->with('feacture_children');
    }
}
