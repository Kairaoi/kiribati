<?php

namespace App\Providers;

use App\Models\National\Eregistry\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use SocialiteProviders\Keycloak\KeycloakExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
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
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $this->app->make('events')->listen(
            SocialiteWasCalled::class,
            KeycloakExtendSocialite::class . '@handle'
        );
    

        Event::listen(Login::class, function (Login $event): void {
            Log::channel('login')->info('Login successful', [
                'user_id' => $event->user->getAuthIdentifier(),
                'guard' => $event->guard,
                'ip' => request()->ip(),
            ]);
        });

        Event::listen(Failed::class, function (Failed $event): void {
            Log::channel('login')->warning('Login failed', [
                'user_id' => $event->user?->getAuthIdentifier(),
                'guard' => $event->guard,
                'ip' => request()->ip(),
            ]);
        });
    }
}
