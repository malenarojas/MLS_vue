<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'internal_id',
        'agent_internal_id',
        'expected_payment_date',
        'amount_expected',
        'amount_expected_currency',
        'amount_received',
        'amount_received_currency',
        'received_date',
        'payment_type_id',
        'exchange_rate_id',
        'office_id'
    ];
    public function agent () : BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_internal_id', 'agent_internal_id');
    }
    public function transaction () : BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function office () : BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

}
