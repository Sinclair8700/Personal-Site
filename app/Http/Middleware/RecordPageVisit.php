<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordPageVisit
{
    /**
     * Record every incoming page visit once the response has been prepared.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        PageVisit::firstOrCreate([
            'ip_address' => $request->ip(),
            'session' => $request->session()?->getId(),
        ]);

        return $response;
    }
}

