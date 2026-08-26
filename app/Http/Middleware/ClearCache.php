<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Artisan;

class ClearCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Do not wipe ride visibility cache on read-only driver polling.
        if (!$request->isMethod('GET')) {
            Artisan::call('cache:clear');
        }

        if (!$request->is('api/driver/near/by/ride')) {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
        }

        return $next($request);
    }
}
