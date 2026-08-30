<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ga_tag_is_not_rendered_when_measurement_id_is_missing(): void
    {
        $this->app['env'] = 'production';
        config(['services.google_analytics.measurement_id' => null]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com/gtag/js', false);
    }

    public function test_ga_tag_is_not_rendered_outside_production(): void
    {
        $this->app['env'] = 'local';
        config(['services.google_analytics.measurement_id' => 'G-TG9GVCPCMP']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com/gtag/js', false);
    }

    public function test_ga_tag_is_rendered_in_production_when_measurement_id_exists(): void
    {
        $this->app['env'] = 'production';
        config(['services.google_analytics.measurement_id' => 'G-TG9GVCPCMP']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TG9GVCPCMP', false);
    }

    public function test_rendered_measurement_id_matches_configuration_and_appears_once(): void
    {
        $this->app['env'] = 'production';
        config(['services.google_analytics.measurement_id' => 'G-TG9GVCPCMP']);

        $response = $this->get('/');

        $response->assertOk();
        $content = $response->getContent();

        // Exactly one gtag.js script tag per page -- no duplicate tags.
        $this->assertSame(1, substr_count($content, 'googletagmanager.com/gtag/js'));

        // The id in the loader script URL and the gtag('config', ...) call
        // both reflect the configured measurement id, not a hardcoded value.
        $this->assertStringContainsString(
            'https://www.googletagmanager.com/gtag/js?id=' . config('services.google_analytics.measurement_id'),
            $content
        );
        $this->assertStringContainsString(
            "gtag('config', " . json_encode(config('services.google_analytics.measurement_id')) . ')',
            $content
        );
    }
}
