<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $base = rtrim(config('app.url'), '/');

        $urls = [];
        foreach (['/' => '1.0', '/work' => '0.8', '/services' => '0.7', '/process' => '0.6',
            '/pricing' => '0.6', '/products' => '0.7', '/team' => '0.6', '/about' => '0.6',
            '/start' => '0.5', '/contact' => '0.4'] as $path => $priority) {
            $urls[] = ['loc' => $base.$path, 'priority' => $priority];
        }

        foreach (Project::query()->published()->get() as $project) {
            $urls[] = [
                'loc' => $base.'/work/'.$project->slug,
                'lastmod' => optional($project->updated_at)->toAtomString(),
                'priority' => '0.6',
            ];
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $base = rtrim(config('app.url'), '/');
        $body = "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: {$base}/sitemap.xml\n";

        return response($body)->header('Content-Type', 'text/plain');
    }
}
