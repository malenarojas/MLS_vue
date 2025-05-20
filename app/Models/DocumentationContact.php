<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationContact extends Model
{
    use HasFactory;


    protected $table = 'documentation_contacts';

    protected $fillable = ['link', 'description', 'contact_id', 'type_documentacion_id'];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function typeDocumentacion()
    {
        return $this->belongsTo(TypeDocumentationContact::class, 'type_documentacion_id');
    }
}
