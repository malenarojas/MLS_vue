<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingMigrationLog extends Model
{
    protected $fillable = [
        'listing_id',
        'key',
        'MLSID',
        'status',
        'message',
        'message_error',
        'data',
        'errors',
        'warnings',
    ];

    protected $casts = [
        'data' => 'array',
        'errors' => 'array',
        'warnings' => 'array',
    ];
}
