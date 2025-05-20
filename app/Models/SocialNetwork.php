<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialNetwork extends Model
{
    protected $fillable = [
        'name',
        'state',
        'url',
        'agent_id',
        'team_management_id',
        'office_id',
    ];

     /**
     * Relación pertenece a Agent.
     */
    public function agent():BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
    public function team(): BelongsTo
    {
        return $this->belongsTo(TeamManagement::class, 'team_management_id');
    }

    /**
     * Relación pertenece a Office.
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id', 'office_id');
    }
}
