<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            $cacheKey = 'response_' . md5($request->fullUrl());

            if (Cache::has($cacheKey)) {
                return response()->json(json_decode(Cache::get($cacheKey)), 200);
            }

            $response = $next($request);

            if ($response->isSuccessful()) {
                Cache::put($cacheKey, $response->getContent(), now()->addMinute(60));
            }

            return $response;
    }
}
