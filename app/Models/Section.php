<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Section extends Model
{
    protected $fillable = ['page_id', 'title', 'order'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(Element::class)->orderBy('order');
    }


    public function access_rules(): MorphMany
    {
        return $this->morphMany(AccessRule::class, 'resource');
    }
}
