<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
  protected $table = 'owner';

  protected $fillable = [
    'contact_id',
    'listing_id',
  ];
}
