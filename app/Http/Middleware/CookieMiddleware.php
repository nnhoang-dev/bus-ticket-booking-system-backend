<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CookieMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jwtCookie = $request->cookie('jwt');

        if ($jwtCookie) {
            $request->headers->set('Authorization', 'Bearer ' . $jwtCookie);
        }

        echo "test";
        return $next($request);
    }
}
