<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RecordPageVisit
{
    /**
     * Common bot/crawler identifiers in User-Agent strings.
     */
    protected array $botPatterns = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'apis-google',
        'bingpreview', 'yandex', 'baidu', 'duckduck', 'teoma', 'ia_archiver',
        'facebookexternalhit', 'twitterbot', 'whatsapp', 'telegram', 'slack',
        'lighthouse', 'pagespeed', 'gtmetrix', 'pingdom', 'uptimerobot',
        'semrush', 'ahrefs', 'mj12bot', 'dotbot', 'rogerbot', 'screaming frog',
        'headless', 'phantom', 'selenium', 'webdriver', 'curl', 'wget', 'python-requests',
        'apnic', 'ripe', 'arin', 'lacnic', 'afrinic', // Network registry crawlers
        'go-http-client', 'okhttp', 'java/', 'libwww', 'httpclient', 'axios', 'node-fetch',
        'scan', 'nmap', 'nikto', 'zgrab', 'masscan',
    ];

    /**
     * Record a genuine page view once the response has been prepared.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldRecord($request, $response)) {
            return $response;
        }

        // One row per unique visitor, keyed on a salted hash of IP + User-Agent.
        // The unique index on visitor_hash means insertOrIgnore is a no-op for
        // repeat visits, so a crawler hitting N URLs adds at most one row rather
        // than one per URL (the old per-session behaviour that inflated the count).
        PageVisit::insertOrIgnore([
            'visitor_hash' => hash('sha256', $request->ip().'|'.$request->userAgent().'|'.config('app.key')),
            'ip_address'   => $request->ip(),
            'user_agent'   => Str::limit((string) $request->userAgent(), 255, ''),
            'session'      => $request->session()?->getId(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return $response;
    }

    /**
     * Decide whether this request/response pair is a genuine, countable page view.
     */
    protected function shouldRecord(Request $request, Response $response): bool
    {
        if ($this->isBot($request)) {
            return false;
        }

        // Only count real page navigations: a plain GET (no XHR/JSON) that
        // actually rendered an HTML page.
        if (! $request->isMethod('GET') || $request->ajax() || $request->pjax() || $request->wantsJson()) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        if (! str_contains((string) $request->header('Accept'), 'text/html')) {
            return false;
        }

        // Skip non-page routes and static assets.
        if ($request->is('api/*', 'sitemap.xml', 'robots.txt', 'favicon.ico', 'up', 'build/*', 'storage/*')
            || $request->is('*.xml', '*.txt', '*.ico', '*.png', '*.jpg', '*.jpeg', '*.gif', '*.svg', '*.css', '*.js', '*.json', '*.map')) {
            return false;
        }

        return true;
    }

    /**
     * Check if the request appears to be from a bot/crawler.
     */
    protected function isBot(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        // Empty or implausibly short user agents are almost never real browsers.
        if (strlen($userAgent) < 15) {
            return true;
        }

        // Every mainstream browser advertises one of these engine/brand tokens.
        if (! str_contains($userAgent, 'mozilla')
            && ! str_contains($userAgent, 'opera')
            && ! str_contains($userAgent, 'safari')) {
            return true;
        }

        // Known bot/crawler/library signatures.
        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
