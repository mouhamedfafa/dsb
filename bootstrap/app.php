<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureTokenIsValid;


    // header('Access-Control-Allow-Origin", *');
    // header('Access-Control-Allow-Methods :GET,HEAD,OPTIONS,POST,PUT');
    // header('Access-Control-Allow-Headers : "Origin, X-Requested-With, Content-Type,
    // Accept, x-client-key, x-client-token, x-client-secret, Authorization');


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'EnsureTokenIsValid' => \App\Http\Middleware\EnsureTokenIsValid::class,
            'Cors' => \App\Http\Middleware\Cors::class, // Utiliser Cors ici si vous avez ce middleware
        ]);
        // append(EnsureTokenIsValid::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create()
;
