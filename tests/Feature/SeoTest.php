<?php

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function jsonLdGraph(string $html): array
{
    preg_match('#<script type="application/ld\+json">(\{"@context":"https://schema.org","@graph".*?)</script>#s', $html, $m);

    return json_decode($m[1] ?? 'null', true)['@graph'] ?? [];
}

it('builds the canonical URL from APP_URL and the path only', function () {
    $base = rtrim(config('app.url'), '/');

    get('/projects?utm_source=spam')
        ->assertSee('<link rel="canonical" href="'.$base.'/projects" />', false)
        ->assertSee('<meta property="og:url" content="'.$base.'/projects">', false);

    get('/')->assertSee('<link rel="canonical" href="'.$base.'/" />', false);
});

it('leaves the canonical tag off noindex pages', function () {
    get(route('login-form'))
        ->assertSee('noindex', false)
        ->assertDontSee('rel="canonical"', false);
});

it('puts the name in the home page title and h1', function () {
    get('/')
        ->assertSee('<title>Alex Davies | Full-Stack Software Developer in Stoke-on-Trent</title>', false)
        ->assertSeeInOrder(['<h1', 'Alex Davies', '</h1>'], false)
        ->assertSee('<meta property="og:type" content="profile">', false);
});

it('describes Alex as one linked Person entity', function () {
    $graph = jsonLdGraph(get('/')->getContent());
    $person = collect($graph)->firstWhere('@type', 'Person');

    expect($person['name'])->toBe('Alex Davies')
        ->and($person['@id'])->toEndWith('/#person')
        ->and($person['sameAs'])->toContain('https://github.com/Sinclair8700')
        ->and(collect($graph)->firstWhere('@type', 'ProfilePage')['mainEntity']['@id'])->toBe($person['@id'])
        ->and(json_encode($person['alumniOf']))->not->toContain('ucsb.edu');
});

it('gives the CV page a name-first title', function () {
    get(route('cv.show'))
        ->assertSee('<title>Alex Davies CV | Full-Stack Software Developer, Stoke-on-Trent</title>', false)
        ->assertSeeInOrder(['<h1', 'Alex Davies CV', '</h1>'], false);
});

it('keeps project pages without screenshots out of search results', function () {
    $stub = Project::doesntHave('images')->first() ?? Project::create(['name' => 'Stub', 'slug' => 'stub', 'description' => 'A stub project.']);

    get(route('projects.show', $stub))->assertSee('<meta name="robots" content="noindex, nofollow">', false);
});

it('lists pages with lastmod dates in the sitemap', function () {
    get('/sitemap.xml')
        ->assertOk()
        ->assertSee('<loc>'.url('/cv').'</loc>', false)
        ->assertSee('<lastmod>', false);
});

it('has no em or en dashes on the public pages', function (string $uri) {
    $html = get($uri)->getContent();

    expect($html)->not->toMatch('/[\x{2013}\x{2014}]|&[mn]dash;|&#821[12];/u');
})->with(['/', '/projects', '/projects/assembly-simulator', '/education', '/education/keele-university', '/cv', '/contact', '/cv/download.md']);
