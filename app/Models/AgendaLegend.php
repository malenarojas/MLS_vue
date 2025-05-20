<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaLegend extends Model
{
    use HasFactory;


    protected $table = 'agenda_legends';

    protected $fillable = ['name'];

    public function activities()
    {
        return $this->hasMany(Activity::class, 'agenda_legends_id');
    }

}
