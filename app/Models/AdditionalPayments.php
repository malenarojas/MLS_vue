<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalPayments extends Model
{
    protected $fillable = [
        'amount',
        'payment_term',
        'note',
        'type_additional_payment_id',
        'listing_id'
    ];

    public function type_additional_payment(): BelongsTo
    {
        return $this->belongsTo(TypeAdditionalPayments::class);
    }
}
