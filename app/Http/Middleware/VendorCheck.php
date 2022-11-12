<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VendorCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!authUser() ) {
            return error('Session expired, Login', [], 400);
        }
        if (authUser()->role == 'vendor') {
            return $next($request);
        }
        return error('You are not allowed to view this route', [], 400);
    }
}
