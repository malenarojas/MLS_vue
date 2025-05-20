<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['option_id', 'language_id', 'value', 'order', 'enabled'];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}