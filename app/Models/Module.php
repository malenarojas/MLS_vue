<?php

namespace App\Models;

use App\Traits\HasAccessRules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Module extends Model
{
    use HasAccessRules;

    protected $fillable = ['name', 'image', 'default_page_id'];
    protected $appends = ['image_url'];

    // app/Models/Module.php

public function getImageUrlAttribute(): string
{
    // Devuelve la URL pública accediendo al disco 'public'
    return Storage::disk('public')->url("{$this->image}");
}


    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function default_page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'default_page_id');
    }

    public function access_rules(): MorphMany
    {
        return $this->morphMany(AccessRule::class, 'resource');
    }
}
