<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentMonthlyMeasure extends Model
{
    use HasFactory;

    protected $fillable = ['agent_id', 'active_listings', 'month', 'year'];
}
