<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
class TeamManagement extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'shortlink',
        'enable_chat',
        'whatsapp_number',
        'spanish_description',
        'english_description',
        'portuguese_description',
        'motto',
        'description',
        'office_id',
        'member_count',
        'is_active',
        'image_name',
        'logo_name',

    ];

    protected $casts = [
        'enable_chat' => 'boolean',
        'is_active' => 'boolean'
    ];
    protected $appends = [
        'image_url',
        'logo_url',
        'members_count'
    ];


    /**
     * Relación con el líder del equipo
     */
        /**
     * Accesor para obtener la URL completa de la imagen del equipo
     */
    public function getMembersCountAttribute()
    {
        return $this->members()->count();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_name && Storage::disk('public')->exists($this->image_name)
            ? Storage::disk('public')->url($this->image_name)
            : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_name && Storage::disk('public')->exists($this->logo_name)
            ? Storage::disk('public')->url($this->logo_name)
            : null;
    }

    public function members()
    {
        return $this->belongsToMany(Agent::class, 'agent_team', 'team_id', 'agent_id')
            ->withTimestamps()
            ->using(AgentTeam::class) // 👉 Usamos el modelo personalizado
            ->withPivot('is_leader');
    }
    public function leader()
    {
        return $this->belongsToMany(Agent::class, 'agent_team', 'team_id', 'agent_id')
            ->wherePivot('is_leader', true)
            ->withTimestamps()
            ->using(\App\Models\AgentTeam::class);
    }

    public function getMembersWithUsersAttribute()
    {
        return $this->members()->with('user')->get();
    }

    /**
     * Miembros del equipo
     */
    /*public function members()
    {
        return $this->belongsToMany(User::class, 'team_members')
                    ->withTimestamps();
    }*/

    /**
     * Accesor para el número de teléfono formateado
     */
    public function getFormattedPhoneNumberAttribute()
    {
        return preg_replace('/[^0-9+]/', '', $this->phone_number);
    }

    /**
     * Accesor para el número de WhatsApp formateado
     */
    public function getFormattedWhatsappNumberAttribute()
    {
        return preg_replace('/[^0-9+]/', '', $this->whatsapp_number);
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'office_id');
    }
    /**
     * Relación con redes sociales del equipo
     */
    public function socialNetworks()
    {
        return $this->hasMany(SocialNetwork::class, 'team_management_id');
    }


}
