<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractType extends Model
{
    protected $table = 'contract_types';
    protected $fillable = ['name'];

    public function listings():HasMany
    {
        return $this->hasMany(Listing::class, 'contract_type_id', 'id');
    }
}
