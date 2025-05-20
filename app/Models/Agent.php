<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Agent extends Model
{
    protected $fillable = [
        'agent_internal_id',
        'office_id',
        'region_id',
        'user_id',
        'studies',
        'agent_status_id',
        'additional_education',
        'image_name',
        'date_joined',
        'date_termination',
        'previous_occupation',
        'license_type',
        'license_department',
        'year_obtained_license',
        'expiration_date_license',
        'license_number',
        'address',
        'rejectionReason',
        'landline_phone',
        'marketing_slogan',
        'website_descripction',
        'countries_interested',
        'meta_tag_description',
        'bullet_point_one',
        'bullet_point_two',
        'bullet_point_three',
        'meta_tag_keywords',
        'deactivation_date',
        'commission_percentage',
        'nro_internacional_remax',
        'id_business_agent',
        'contact_id',
        'url_alterno'
    ];
    protected $appends = ['image_url'];
    const TYPE_IMAGEN = [
        'thumbnail',
        'profile',
        'small',
        'medium',
        'large',
    ];

    // Relación con el modelo AgentStatus
    public function agentStatus()
    {
        return $this->belongsTo(AgentStatus::class);
    }

    // app/Models/Agent.php
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
    protected $casts = [
        'countries_interested' => 'array', // si esta columna contiene datos en JSON
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'agent_listing', 'agent_id', 'listing_id')->withPivot('type');
    }
    // Contar contactos únicos relacionados con el agente
    public static function countContactsByAgent(int $agentId): int
    {
        return self::where('id', $agentId)
            ->distinct('contact_id')
            ->count('contact_id');
    }
    public static function countListingsByAgent(int $agentId): int
    {

        $agent = self::find($agentId);
        return $agent ? $agent->listings()->where('status_listing_id', 2)->count() : 0;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'agent_id', 'id');
    }

    // Liste de propiedades en la que el agente es invitado
    public function guest_listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'agent_listing', 'agent_id', 'listing_id');
    }

    /**
     * Relación uno a muchos con SocialNetwork.
     */
    public function socialNetworks()
    {
        return $this->hasMany(SocialNetwork::class, 'agent_id');
    }

    public function qualification(): HasMany
    {
        return $this->hasMany(Qualification::class, 'agent_id');
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación muchos a muchos con el modelo Office.
     */
    public function officestranfered()
    {
        return $this->belongsToMany(
            Office::class,
            'agent_offices', // Nombre de la tabla intermedia
            'agent_id',      // Columna en la tabla intermedia que hace referencia a Agent
            'office_id'      // Columna en la tabla intermedia que hace referencia a Office
        )->withPivot('date_transferred') // Incluye columnas adicionales de la tabla intermedia

            ->withTimestamps();            // Incluye timestamps (si están en la tabla intermedia)
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id', 'office_id');
    }

    // Relación con el modelo region
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }


    public function goals()
    {
        return $this->hasMany(Goal::class, 'agent_id');
    }
    public function documentation(): BelongsToMany
    {
        return $this->belongsToMany(Documentation::class, 'agents_documentations', 'agent_id', 'documentation_id');
    }
    public function getImageUrlAttribute()
    {
        return $this->image_name
            ? Storage::url('agents/' . $this->image_name)
            : Storage::disk('public')->url('agents/agent-defaul.gif');
    }
    public function teams()
    {
        return $this->belongsToMany(TeamManagement::class, 'agent_team', 'agent_id', 'team_id')
            ->withTimestamps()
            ->using(\App\Models\AgentTeam::class)
            ->withPivot('is_leader');
    }
}