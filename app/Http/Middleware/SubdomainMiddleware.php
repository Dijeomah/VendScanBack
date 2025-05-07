<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BusinessLink;

class SubdomainMiddleware
{
    public function handle($request, Closure $next)
    {
        $subdomain = $request->route('subdomain');

        if (!$business = BusinessLink::where('business_link', $subdomain)->first()) {
            abort(404, 'Business not found');
        }

        // Make business available to all controllers
        $request->attributes->add(['currentBusiness' => $business]);

        return $next($request);
    }
}
