<?php

namespace App\Models;

use App\Traits\HasAccessRules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Element extends Model
{
    use HasAccessRules;

    protected $fillable = ['section_id', 'type', 'content', 'order'];
    protected $casts = ['content' => 'array'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function access_rules(): MorphMany
    {
        return $this->morphMany(AccessRule::class, 'resource');
    }
}
