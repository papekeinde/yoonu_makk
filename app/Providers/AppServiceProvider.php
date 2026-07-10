<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * SECURITE — Rate limiting sur les routes sensibles (OWASP A07).
     *
     * - auth-connexion : 5 tentatives / minute / IP → anti brute-force
     * - auth-inscription : 10 requêtes / minute / IP → anti-spam
     * - auth-password : 5 requêtes / minute / IP → anti email-bombing
     * - api : 60 requêtes / minute / token → protection générale
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('auth-connexion', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('auth-inscription', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('auth-password', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });
    }
}
