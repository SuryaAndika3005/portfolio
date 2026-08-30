<?php

namespace App\Providers;

use App\Services\Gemini\GeminiClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GeminiClient::class, function () {
            $config = config('services.gemini');

            return new GeminiClient(
                apiKey: $config['key'],
                model: $config['default_model'],
                baseUrl: $config['base_url'],
                apiRevision: $config['api_revision'],
                timeout: $config['timeout'],
            );
        });
    }

    public function boot(): void
    {
        // Canonical/OG/sitemap URLs are built from the current request's
        // detected scheme, not a hardcoded domain. Behind a TLS-terminating
        // reverse proxy with no trusted-proxy config, Laravel would
        // otherwise see the inner HTTP hop and generate http:// URLs on a
        // site actually served over https:// — force https in production
        // only, so local development (APP_ENV=local, plain HTTP) is
        // unaffected.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Max 3 contact-form submissions per minute per IP —
        // closes the previously unthrottled /contact spam vector.
        RateLimiter::for('contact', function ($request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // AI Project Assistant is an Admin-only, single-operator feature —
        // this limit exists to absorb double-clicks/JS retry loops, not to
        // manage real multi-tenant traffic (Section 8).
        RateLimiter::for('ai-assistant', function ($request) {
            return Limit::perMinute(20)->by($request->user()?->id ?? $request->ip());
        });
    }
}
