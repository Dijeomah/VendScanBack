<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Vendor;

class SubdomainMiddleware
{
    public function handle($request, Closure $next)
    {
        $subdomain = $request->route('subdomain');

        if (!$vendor = Vendor::where('subdomain', $subdomain)->first()) {
            abort(404, 'Vendor not found');
        }

        // Make vendor available to all controllers
        $request->attributes->add(['currentVendor' => $vendor]);

        return $next($request);
    }
}
