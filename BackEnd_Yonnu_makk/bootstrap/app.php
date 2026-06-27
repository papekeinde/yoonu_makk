<?php

use App\Http\Middleware\EnsureEmailVerifie;
use App\Http\Middleware\EnsureGynecologueActif;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SecureHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Headers de sécurité HTTP sur toutes les requêtes API
        $middleware->appendToGroup('api', SecureHeaders::class);

        // Alias pour les routes
        $middleware->alias([
            'role'               => EnsureRole::class,
            'gynecologue.actif'  => EnsureGynecologueActif::class,
            'email.verifie'      => EnsureEmailVerifie::class,
        ]);

        // Sanctum stateful domains (pour les cookies SPA si besoin)
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
