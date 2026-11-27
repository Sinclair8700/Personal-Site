<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\RecordPageVisit;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Apache serves the site directly (no proxy or CDN in front), so only a
        // proxy on this machine may set X-Forwarded-* headers. Trusting every
        // address let any visitor fake their IP (dodging the contact form rate
        // limit) and their host (poisoning canonical URLs). If a CDN is added
        // later, list its published IP ranges here.
        $middleware->trustProxies(at: ['127.0.0.1', '::1']);

        $middleware->appendToGroup('web', RecordPageVisit::class);
        $middleware->appendToGroup('web', SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();