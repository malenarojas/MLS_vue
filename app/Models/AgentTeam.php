<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AgentTeam extends Pivot
{
    protected $table = 'agent_team';

    protected $fillable = [
        'agent_id',
        'team_id',
        'is_leader',
        
    ];

    public $timestamps = true;

    protected $casts = [
        'is_leader' => 'boolean',
    ];
}
