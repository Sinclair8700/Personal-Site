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
        // Trust the proxy's X-Forwarded-Proto/Host (e.g. Cloudflare) so
        // url()->current(), asset() and the canonical/OG tags resolve to https
        // and the real host instead of the internal http request. Narrow `at`
        // to Cloudflare's published IP ranges if the origin is publicly reachable.
        $middleware->trustProxies(at: '*');

        $middleware->appendToGroup('web', RecordPageVisit::class);
        $middleware->appendToGroup('web', SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();