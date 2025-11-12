<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBusinessLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Only check for vendors
        if ($user && $user->role === 'vendor') {
            if (!$user->canCreateBusiness()) {
                return response()->json([
                    'code' => 403,
                    'status' => 'error',
                    'message' => 'Business creation limit reached for your subscription tier',
                    'data' => [
                        'current_tier' => $user->subscription_tier,
                        'business_limit' => $user->business_limit,
                        'current_count' => $user->business_links()->count(),
                        'upgrade_required' => true
                    ]
                ], 403);
            }
        }

        return $next($request);
    }
}
