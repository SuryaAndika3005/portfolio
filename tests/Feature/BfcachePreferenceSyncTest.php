<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BfcachePreferenceSyncTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The server-rendered locale is exposed on <html data-locale="...">
     * so resources/js/locale-sync.js can detect a stale BFCache snapshot
     * client-side. This is the one new server-side surface this feature
     * adds -- everything else (the actual sync/reload decision) is JS
     * runtime behavior this suite can't exercise without a browser, so
     * this pins down the one thing that can regress silently on the
     * server side: the attribute not reflecting the real session locale.
     */
    public function test_rendered_html_exposes_current_locale_via_data_locale_attribute(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-locale="en"', false);

        $this->get(route('lang.switch', ['locale' => 'id']));

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('data-locale="id"', false);
    }

    /**
     * Admin never renders <x-layout> (its own admin/_layout.blade.php has
     * a separate <html> tag and never includes locale-sync.js), so this
     * BFCache sync mechanism must have no footprint there.
     */
    public function test_admin_layout_does_not_expose_data_locale_or_load_locale_sync(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('data-locale=', false);
        $response->assertDontSee('locale-sync', false);
    }

    /**
     * Theme regression guard: the inline anti-FOUC script (must stay
     * first in <head>, before first paint) and the theme.js/
     * preferences-fab.js Vite entries must survive this change untouched
     * -- this feature only ADDS a pageshow listener inside theme.js, it
     * must never replace the existing no-flash mechanism.
     */
    public function test_homepage_still_ships_the_inline_anti_fouc_theme_script(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee("localStorage.getItem('theme')", false);
        $response->assertSee('prefers-color-scheme: dark', false);
    }
}
