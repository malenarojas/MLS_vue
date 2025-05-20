<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentListings extends Model
{
    protected $table = 'agent_listing';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agent_id',
        'listing_id',
        'type',
    ];

    /**
     * Define la relación con el modelo Agent.
     *
     * @return BelongsTo
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    /**
     * Define la relación con el modelo Listing.
     *
     * @return BelongsTo
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}
