<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketStatus extends Model
{
    protected $table = 'market_status';

    protected $fillable = [
        'name_market_status',
    ];

    public function listingInformation()
    {
        return $this->hasMany(ListingInformation::class, 'market_status_id');
    }
}
