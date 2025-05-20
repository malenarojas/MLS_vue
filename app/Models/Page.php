<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Page extends Model
{
    protected $fillable = ['title', 'image', 'module_id', 'parent_id'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }
    public function sections(): HasMany
{
    return $this->hasMany(Section::class)->orderBy('order');
}


    public function access_rules(): MorphMany
    {
        return $this->morphMany(AccessRule::class, 'resource');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }

}
