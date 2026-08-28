<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Persist the chosen locale in session and return to wherever the
     * visitor switched from -- the EN/ID control is a plain link with a
     * "next" query param (the current URL), not a query-string-driven
     * route, so refreshing or sharing a link never carries a stale
     * ?lang= param and every existing route stays untouched.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, SetLocale::SUPPORTED, true)) {
            abort(404);
        }

        $request->session()->put('locale', $locale);

        $next = $request->string('next')->toString();
        $safeNext = $next !== '' && str_starts_with($next, '/') && ! str_starts_with($next, '//')
            ? $next
            : route('home');

        return redirect($safeNext);
    }
}
