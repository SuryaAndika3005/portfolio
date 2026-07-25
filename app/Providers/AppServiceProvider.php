<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Max 3 contact-form submissions per minute per IP —
        // closes the previously unthrottled /contact spam vector.
        RateLimiter::for('contact', function ($request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}
