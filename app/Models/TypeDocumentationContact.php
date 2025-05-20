<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDocumentationContact extends Model
{
    use HasFactory;


    protected $table = 'type_documentacion_contacts';

    protected $fillable = ['name_types', 'enabled'];

    public function documentationContacts()
    {
        return $this->hasMany(DocumentationContact::class, 'type_documentacion_id');
    }
}
