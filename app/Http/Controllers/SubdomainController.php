<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class SubdomainController extends Controller
{
    public function handle(Request $request, $subdomain)
    {
        $vendor = Vendor::where('subdomain', $subdomain)
                        ->with(['categories.items'])
                        ->firstOrFail();

        return response()->json($vendor);
    }
}
