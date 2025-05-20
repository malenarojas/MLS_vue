<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
// use Laravel\Passport\HasApiTokens;
use Laravel\Sanctum\HasApiTokens; // Satum en lugar passport
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name_to_show',
        'ci',
        'gender',
        'phone_number',
        'email',
        'url',
        'remax_start_date',
        'password',
        'username',
        'birthdate',
        'user_type_id',
        'remax_title_id',
        'remax_title_to_show_id',
        'team_status_id',
        'customer_preference_id',
        'state_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con el modelo Agent
    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function user_type(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function remax_title(): BelongsTo
    {
        return $this->belongsTo(RemaxTitle::class);
    }

    public function remax_title_to_show(): BelongsTo
    {
        return $this->belongsTo(RemaxTitleToShow::class);
    }

    public function team_status(): BelongsTo
    {
        return $this->belongsTo(TeamStatus::class);
    }

    public function customer_preference(): BelongsTo
    {
        return $this->belongsTo(CustomerPreference::class);
    }
    public function areaSpecialities(): BelongsToMany
    {
        return $this->belongsToMany(
            AreaSpeciality::class,
            'area_speciality_user',
            'user_id',
            'area_speciality_id'
        );
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'achievement_user', 'user_id', 'achievement_id');
    }

    //Se deberia llamar cities pero mas descriptivo es area_services
    public function area_services(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'area_service', 'user_id', 'city_id');
    }
    public function areaSpecialityUsers()
    {
        return $this->hasMany(AreaSpecialityUser::class);
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'languages_users', 'user_id', 'languages_id');
    }
    public function teamStatus(): BelongsTo
    {
        return $this->belongsTo(TeamStatus::class, 'team_status_id');
    }


    // Utils methods
    // public function getAuthenticate(): ?User
    // {
    //     return session()->get('login_as')
    //         ? User::find(session()->get('login_as'))
    //         : Auth::user();
    // }
}
