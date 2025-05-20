<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'affair', 'description', 'grades', 'start_date', 'start_date_time',
        'end_date', 'end_date_time', 'all_day_event', 'type_actividad_id', 'agenda_legends_id'
    ];

    public function typeActividad()
    {
        return $this->belongsTo(TypeActivity::class, 'type_actividad_id');
    }

    public function agendaLegend()
    {
        return $this->belongsTo(AgendaLegend::class, 'agenda_legends_id');
    }
}
