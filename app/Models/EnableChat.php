<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnableChat extends Model
{
    use HasFactory;

    protected $table = 'enable_chat';

    protected $fillable = ['name_social_network', 'link_social_network'];
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

}
