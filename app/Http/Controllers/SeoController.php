<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;

/**
 * Generic, database-synchronized SEO discovery endpoints: /sitemap.xml and
 * /robots.txt. Both are Laravel routes (not static public/ files) so the
 * sitemap URL in robots.txt and every <loc> in the sitemap are built with
 * url()/route() -- environment-aware, never a hardcoded domain -- and the
 * sitemap always reflects whichever projects are currently published,
 * with no per-project ID hardcoded anywhere.
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $projects = Project::published()->orderBy('id')->get(['id', 'updated_at']);

        $urls = collect([
            ['loc' => route('home'), 'lastmod' => null],
            ['loc' => route('portfolio.projects'), 'lastmod' => null],
        ])->merge(
            $projects->map(fn (Project $project) => [
                'loc' => route('portfolio.show', $project->id),
                'lastmod' => $project->updated_at?->toAtomString(),
            ])
        );

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            '',
            'Sitemap: ' . route('sitemap'),
        ];

        return response(implode("\n", $lines) . "\n", 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
