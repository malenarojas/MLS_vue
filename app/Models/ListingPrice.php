<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingPrice extends Model
{
    protected $table = 'listing_prices';
    protected $fillable = [
        'amount',
        'listing_id',
        'currency_id',
        'exchange_rate_id',
    ];
    protected $appends = ['price_in_dollars'];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function exchange_rate()
    {
        return $this->belongsTo(ExchangeRate::class, 'exchange_rate_id');
    }

    public function getPriceInDollarsAttribute()
    {
        if ($this->currency_id === 2) {
            return $this->amount;
        }

        $rate = $this->exchangeRate?->amount ?? 6.96;

        return $this->amount / $rate;
    }
}
