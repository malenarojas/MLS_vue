<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feature extends Model
{
    protected $table = 'features';

    protected $fillable = [
        'name',
        'feature_id',
    ];

    /**
     * Relación recursiva: obtener las subcaracterísticas (hijos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Feature::class, 'feature_id');
    }

    /**
     * Relación recursiva para obtener todas las subcaracterísticas de manera anidada.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Relación recursiva para obtener la característica padre.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'features_id');
    }
}
