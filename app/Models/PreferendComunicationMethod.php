<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferendComunicationMethod extends Model
{
    use HasFactory;

    protected $table = 'preferred_communication_methods';

    protected $fillable = ['name'];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'preferred_communication_method_id');
    }
}
