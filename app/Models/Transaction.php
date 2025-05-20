<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'agent_id',
        'office_id',
        'transaction_status_id',
        'trr_report_id',
        'listing_id',
        'listing_uuid',
        'current_listing_price',
        'current_listing_price_usd',
        'current_listing_price_euro',
        'transaction_type_id',
        'internal_id',
        'both',
        'mls_id',
        'exchange_rate_id',
        'sold_date',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'transaction_id', 'id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
