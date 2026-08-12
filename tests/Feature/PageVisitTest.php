<?php

use App\Models\PageVisit;
use Illuminate\Support\Facades\Cache;

/**
 * A realistic desktop-browser request that the counter is expected to record:
 * a GET with a real User-Agent that accepts HTML, from a given IP.
 */
function browserVisit(string $uri, string $ip): \Illuminate\Testing\TestResponse
{
    return test()->withServerVariables([
        'REMOTE_ADDR' => $ip,
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
        'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    ])->get($uri);
}

it('records a genuine browser page view', function () {
    browserVisit('/education', '203.0.113.10')->assertOk();

    expect(PageVisit::count())->toBe(1);
});

it('deduplicates repeat visits from the same IP and user agent', function () {
    browserVisit('/education', '203.0.113.10')->assertOk();
    browserVisit('/education/keele-university', '203.0.113.10')->assertOk();

    // Same visitor hitting two pages must still count once.
    expect(PageVisit::count())->toBe(1);
});

it('does not record known bots or non-browser clients', function () {
    // curl-style client
    test()->withServerVariables([
        'REMOTE_ADDR' => '203.0.113.20',
        'HTTP_USER_AGENT' => 'curl/8.4.0',
        'HTTP_ACCEPT' => '*/*',
    ])->get('/education')->assertOk();

    // Declared crawler
    test()->withServerVariables([
        'REMOTE_ADDR' => '203.0.113.21',
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'HTTP_ACCEPT' => 'text/html',
    ])->get('/education')->assertOk();

    expect(PageVisit::count())->toBe(0);
});

it('does not record non-page routes such as the sitemap', function () {
    browserVisit('/sitemap.xml', '203.0.113.30')->assertOk();

    expect(PageVisit::count())->toBe(0);
});

it('does not record error (non-200) responses', function () {
    browserVisit('/a-route-that-does-not-exist', '203.0.113.40')->assertNotFound();

    expect(PageVisit::count())->toBe(0);
});

it('counts unique visitors by distinct IP, not by row', function () {
    $now = now();

    // Same IP, two different visitor hashes (e.g. two browsers) → one unique visitor.
    PageVisit::insertOrIgnore(['visitor_hash' => hash('sha256', 'a'), 'ip_address' => '198.51.100.1', 'user_agent' => 'ua-a', 'session' => 's1', 'created_at' => $now, 'updated_at' => $now]);
    PageVisit::insertOrIgnore(['visitor_hash' => hash('sha256', 'b'), 'ip_address' => '198.51.100.1', 'user_agent' => 'ua-b', 'session' => 's2', 'created_at' => $now, 'updated_at' => $now]);
    PageVisit::insertOrIgnore(['visitor_hash' => hash('sha256', 'c'), 'ip_address' => '198.51.100.2', 'user_agent' => 'ua-c', 'session' => 's3', 'created_at' => $now, 'updated_at' => $now]);

    // uniqueVisitorCount() caches for 5 minutes; clear it so we assert a fresh computation.
    Cache::flush();

    expect(PageVisit::uniqueVisitorCount())->toBe(2); // distinct IPs
    expect(PageVisit::count())->toBe(3);              // raw rows preserved
});
