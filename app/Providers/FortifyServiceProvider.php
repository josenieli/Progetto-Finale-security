<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

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

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });
        Event::listen(Login::class, function (Login $event) {
            Log::info('User logged in', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            Log::warning('Failed login attempt', [
                'email' => $event->credentials['email'] ?? null,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            Log::info('User logged out', [
                'user_id' => $event->user->id ?? null,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Registered::class, function (Registered $event) {
            Log::info('New user registered', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
            ]);
        });
    }
}
