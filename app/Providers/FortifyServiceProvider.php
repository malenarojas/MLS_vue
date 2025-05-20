<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('username', $request->username)
                ->with('agent.agentStatus')
                ->first();

            if ($user) {
                Log::info('🔍 Usuario encontrado: ' . $user->username);

                if (Hash::check($request->password, $user->password)) {
                    Log::info('✅ Contraseña válida para: ' . $user->username);

                    if ($user->agent && $user->agent->agent_status_id == 2) {
                        Log::warning('⛔ Usuario inactivo: ' . $user->username);
                        return null;
                    }

                    Log::info('🟢 Usuario autenticado correctamente: ' . $user->username);
                    return $user;
                } else {
                    Log::warning('❌ Contraseña inválida para: ' . $user->username);
                }
            } else {
                Log::warning('🚫 Usuario no encontrado: ' . $request->username);
            }

            return null;
        });


        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(50)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
