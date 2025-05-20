<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryContact extends Model
{
    use HasFactory;
    protected $fillable = ['name_type'];

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'category_contact_id');
    }
}
