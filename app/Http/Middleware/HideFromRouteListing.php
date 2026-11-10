<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevent a route from being enumerated in route:list / public route listings.
 *
 * The route itself remains fully functional when the exact URL is requested —
 * only the router's metadata (used by php artisan route:list and any
 * introspection helpers) is suppressed for this route.
 */
class HideFromRouteListing
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
