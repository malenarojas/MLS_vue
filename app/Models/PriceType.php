<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceType extends Model
{
    protected $table = 'price_types';

    protected $fillable = ['name'];
}
