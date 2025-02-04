<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PersistApiSandboxToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $token = $request->input('token');

        if($token && Cache::has('sandbox_token_' . $token)){
            Cache::put('sandbox_token_' . $token, now()->addMinutes(30));
            Log::info('Sandbox token persisted: ' . $token);
        }

        return $response;
    }
}
