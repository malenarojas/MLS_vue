<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Documentation extends Model
{
    protected $table = 'documentations';

    // Campos asignables en el modelo
    protected $fillable = [
        'link',
        'description',
        'documentation_type_id',
        'original_name',
    ];

    protected $appends = [
        'name',
        'extension',
        'full_link',
        'upload_date',
    ];

    // Accesor para obtener la URL del archivo
    public function getFullLinkAttribute(): string
    {
        if (!$this->link) {
            return '';
        }

        return Storage::disk('public')->url($this->link);
    }

    public function getNameAttribute(): string
    {
        if (!$this->link) {
            return '';
        }

        $fileName = pathinfo($this->link, PATHINFO_FILENAME);

        return $fileName;
    }

    public function getExtensionAttribute(): string
    {
        if (!$this->link) {
            return '';
        }

        $extension = pathinfo($this->link, PATHINFO_EXTENSION);

        return $extension;
    }

    public function getUploadDateAttribute(): string
    {
        return Carbon::parse($this->updated_at)->locale('es')->translatedFormat('j \d\e F \d\e Y');
    }

    public function documentation_type()
    {
        return $this->belongsTo(DocumentationType::class, 'documentation_type_id');
    }

    public function documentation_listings()
    {
        return $this->belongsToMany(Listing::class, 'documentation_listing', 'documentation_id', 'listing_id');
    }
    public function agents_documentations()
    {
        return $this->belongsToMany(Agent::class, 'agents_documentations', 'documentation_id', 'agent_id');
    }
}
