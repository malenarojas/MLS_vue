<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingTransfer extends Model
{
    protected $fillable = [
        'previous_key',
        'previous_mlsid',
        'previous_agent_id',
        'previous_agent_name',
        'previous_office_id',
        'previous_office_name',
        'new_key',
        'new_mlsid',
        'new_agent_id',
        'new_agent_name',
        'new_office_id',
        'new_office_name',
        'transferred_at',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
    ];
}
