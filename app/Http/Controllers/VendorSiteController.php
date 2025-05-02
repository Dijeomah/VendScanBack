<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendorSiteController extends Controller
{
    public function show(Request $request)
    {
        $business = $request->attributes->get('currentBusiness');

        return view('subdomain.vendor', [
            'business' => $business,
            'menuItems' => $business->items()->with('category')->get()
        ]);
    }
}
