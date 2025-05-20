<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguagesUser extends Model
{
    protected $fillable = [
		'user_id',
		'language_id',
		'is_preferred',
	];
}
