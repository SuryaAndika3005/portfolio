<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_is_valid_xml_and_contains_published_project(): void
    {
        $project = Project::factory()->create(['is_published' => true]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee(route('portfolio.show', $project->id), false);

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'sitemap.xml did not parse as valid XML');
    }

    public function test_sitemap_excludes_unpublished_project(): void
    {
        $published = Project::factory()->create(['is_published' => true]);
        $unpublished = Project::factory()->create(['is_published' => false]);

        $response = $this->get('/sitemap.xml');

        $response->assertSee(route('portfolio.show', $published->id), false);
        $response->assertDontSee(route('portfolio.show', $unpublished->id), false);
    }

    public function test_sitemap_includes_home_and_archive(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertSee(route('home'), false);
        $response->assertSee(route('portfolio.projects'), false);
    }

    public function test_robots_txt_references_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Sitemap: ' . route('sitemap'), false);
        $response->assertSee('Disallow: /admin', false);
    }

    public function test_unpublished_project_returns_404_and_has_no_canonical(): void
    {
        $project = Project::factory()->create(['is_published' => false]);

        $response = $this->get(route('portfolio.show', $project->id));

        $response->assertNotFound();
        $response->assertDontSee('rel="canonical"', false);
    }

    public function test_published_project_detail_has_unique_metadata(): void
    {
        $project = Project::factory()->create([
            'title' => 'A Distinct Project Title',
            'description' => 'A distinct project description used for meta purposes.',
            'is_published' => true,
        ]);

        $response = $this->get(route('portfolio.show', $project->id));

        $response->assertOk();
        $response->assertSee('<title>A Distinct Project Title | Surya Andika</title>', false);
        $response->assertSee('rel="canonical" href="' . route('portfolio.show', $project->id) . '"', false);
        $response->assertSee('og:title" content="A Distinct Project Title | Surya Andika"', false);
    }

    public function test_project_detail_without_any_cover_image_falls_back_to_site_image_for_og(): void
    {
        $project = Project::factory()->create([
            'image_path' => null,
            'cover_image_path' => null,
            'is_published' => true,
        ]);

        $response = $this->get(route('portfolio.show', $project->id));

        $response->assertOk();
        $response->assertSee('og:image" content="' . asset('storage/projects/dika.webp') . '"', false);
        $response->assertDontSee('content="' . asset('storage/') . '"', false);
    }

    public function test_homepage_has_canonical_and_structured_data(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('rel="canonical" href="' . route('home') . '"', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type":"Person"', false);
    }

    public function test_admin_login_is_noindex(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSee('name="robots" content="noindex, nofollow"', false);
    }
}
