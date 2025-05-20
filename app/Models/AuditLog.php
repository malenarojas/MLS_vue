<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'agent_id',
        'listing_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'notes',
    ];

    protected $appends = ['created_at_human'];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor para obtener el created_at en formato human-readable
     */
    public function getCreatedAtHumanAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
