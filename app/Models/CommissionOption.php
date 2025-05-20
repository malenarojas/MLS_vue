<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionOption extends Model
{
    protected $table = 'commissions_option';

    protected $fillable = [
        'recruitment_commission',
        'type_recruitment_commission',
        'sales_commission',
        'sales_commission_type',
        'listing_id',
    ];

    // Relación inversa con Listing
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
