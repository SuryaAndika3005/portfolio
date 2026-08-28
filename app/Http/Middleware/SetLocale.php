<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the visitor's persisted locale (session-backed, see
 * LocaleController) to the app for this request. Public-facing only in
 * practice -- Admin routes never read/write this session key and Admin
 * views never call __(), so this middleware has no visible effect there
 * even though it runs in the shared 'web' group.
 *
 * Session (not a locale-prefixed route) was chosen deliberately: it keeps
 * every existing route (/, /projects, /project/{id}) byte-identical, so
 * this batch adds bilingual support without touching route definitions,
 * link generation, or Previous/Next logic anywhere in the app. See
 * LOCALIZATION_THEME_AUDIT.md Section 7 for the full architecture
 * comparison and risk reasoning.
 */
class SetLocale
{
    public const SUPPORTED = ['en', 'id'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale'));

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
