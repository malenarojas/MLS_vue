<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $fillable = [
        'transaction_id',
        'internal_id',
        'commission_type_id',
        'agent_internal_id',
        'office_id',
        'date_created',
        'date_edited',
        'total_commission_amount',
        'total_commission_amount_currency',
        'total_commission_amount_usd',
        'total_commission_percentage',
        'exchange_rate_id'
    ];

    public function agent () {
        return $this->belongsTo(Agent::class, 'agent_internal_id', 'agent_internal_id');
    }

	public function transaction () : BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
