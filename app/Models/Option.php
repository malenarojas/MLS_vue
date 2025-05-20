<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = ['category'];

    public function translations()
    {
        return $this->hasMany(OptionTranslation::class);
    }

    public function translation($languageCode = null)
    {
        $languageCode = $languageCode ?? config('app.locale');
        return $this->translations()->whereHas('language', function ($query) use ($languageCode) {
            $query->where('code', $languageCode);
        })->first();
    }
}