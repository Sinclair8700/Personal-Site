<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
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
    ];

    /**
     * Record every incoming page visit once the response has been prepared.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip recording if this looks like a bot
        if ($this->isBot($request)) {
            return $response;
        }

        PageVisit::insertOrIgnore([
            'ip_address' => $request->ip(),
            'session' => $request->session()?->getId(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $response;
    }

    /**
     * Check if the request appears to be from a bot/crawler.
     */
    protected function isBot(Request $request): bool
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        // Empty user agent is suspicious
        if (empty($userAgent)) {
            return true;
        }

        // Check against known bot patterns
        foreach ($this->botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return true;
            }
        }

        return false;
    }
}

