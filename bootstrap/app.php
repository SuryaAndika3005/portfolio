<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        // SetLocale is applied per-route in routes/web.php (only the public
        // pages), not here on the whole web group -- see that file for why:
        // Carbon's diffForHumans() reads app()->getLocale() directly, and a
        // global middleware here would leak the visitor's chosen public
        // locale into Admin's timestamps even though no Admin view calls
        // __().
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
