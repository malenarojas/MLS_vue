<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'new_agents',
        'transactions',
        'transaction_volume',
        'exchange_rate_id',
        'new_contacts',
        'payment_amount',
        'new_listings',
        'agent_id',
        'office_id',
        'region_id',
        'month',
        'year',
        'time_in_market',
        'production_by_agent'
    ];


}
