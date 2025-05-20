<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_key',
        'name',
        'last_name',
        'email',
        'mobile',
        'qualification',
        'greeting',
        'company',
        'department_name',
        'identification_number',
        'birthdate',
        'sex',
        'nationality',
        'preferred_language',
        'motivation',
        'marital_status',
        'children',
        'first_time_buyer',
        'buyer_commission',
        'type_buyer_commission',
        'mail',
        'home_phone',
        'cellular',
        'other_phone',
        'agent_id',
        'grade_id',
        'time_frame_id',
        'preferred_communication_method_id',
        'category_contact_id',
        'profile_type_id'
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function documentationContacts()
    {
        return $this->hasMany(DocumentationContact::class);
    }
    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function categoryContact()
    {
        return $this->belongsTo(CategoryContact::class, 'category_contact_id');
    }

    public function timeFrame()
    {
        return $this->belongsTo(TimeFrame::class, 'time_frame_id');
    }

    public function preferredCommunicationMethod()
    {
        return $this->belongsTo(PreferendComunicationMethod::class, 'preferred_communication_method_id');
    }

    public function enableChats()
    {
        return $this->hasMany(EnableChat::class, 'contact_id', 'id');
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, 'contact_id', 'id');
    }

    public function owners()
    {
        return $this->hasMany(Owner::class, 'contact_id', 'id');
    }
}
