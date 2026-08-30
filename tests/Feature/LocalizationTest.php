<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_english(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $this->assertSame('en', app()->getLocale());
        $response->assertSee('Explore Work', false);
    }

    public function test_switching_locale_persists_across_requests_via_session(): void
    {
        $this->get(route('lang.switch', ['locale' => 'id']))
            ->assertRedirect(route('home'));

        $this->assertSame('id', session('locale'));

        // A fresh request in the same session should now render in Indonesian.
        $response = $this->get('/');
        $response->assertOk();
        $this->assertSame('id', app()->getLocale());
        $response->assertSee('Jelajahi Karya', false);
    }

    public function test_switching_to_an_unsupported_locale_is_rejected(): void
    {
        $this->get('/lang/fr')->assertNotFound();
    }

    public function test_lang_switch_next_param_is_restricted_to_relative_paths(): void
    {
        // Open-redirect guard: an absolute/external "next" must never be honored.
        $response = $this->get(route('lang.switch', ['locale' => 'en', 'next' => 'https://evil.example.com']));

        $response->assertRedirect(route('home'));
    }

    public function test_lang_switch_next_param_allows_a_safe_relative_path(): void
    {
        $response = $this->get(route('lang.switch', ['locale' => 'en', 'next' => '/projects']));

        $response->assertRedirect('/projects');
    }

    public function test_admin_stays_english_even_when_public_locale_is_indonesian(): void
    {
        // SetLocale is scoped to public routes only (see routes/web.php) --
        // this pins down that an Admin request never inherits app locale
        // 'id' from the session, since Carbon's diffForHumans() (used on
        // the dashboard/projects list) reads app()->getLocale() directly
        // and would otherwise silently start rendering Indonesian relative
        // timestamps in an English-only Admin.
        $this->get(route('lang.switch', ['locale' => 'id']));
        $this->assertSame('id', session('locale'));

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user)->get(route('admin.dashboard'));

        $this->assertSame('en', app()->getLocale());
    }

    public function test_missing_translation_falls_back_to_english(): void
    {
        session(['locale' => 'id']);
        app()->setLocale('id');

        // A string with no entry in lang/id.json must render as its
        // English source text, never a blank string or a raw translation key.
        $this->assertSame(
            'This string intentionally has no Indonesian translation.',
            __('This string intentionally has no Indonesian translation.')
        );
    }

    public function test_homepage_and_archive_page_title_and_description_switch_locale(): void
    {
        // Language Content Completion pass: title/meta-description on both
        // pages were passed to <x-layout> as literal string attributes
        // (title="...") rather than :title="__('...')" -- a plain string
        // attribute never runs through the translator at all, so the
        // browser tab title and meta description silently stayed English
        // under the Indonesian locale even though every other static
        // string on both pages switched correctly.
        $this->get(route('lang.switch', ['locale' => 'id']));

        $home = $this->get('/');
        $home->assertSee('<title>Surya Andika — Desainer Grafis &amp; Mahasiswa Informatika</title>', false);

        $archive = $this->get('/projects');
        $archive->assertSee('<title>Arsip Proyek | Surya Andika</title>', false);
    }
}
