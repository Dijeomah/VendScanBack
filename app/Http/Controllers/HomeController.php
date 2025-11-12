<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use App\Models\BusinessLink;
use App\Models\User;
use App\Models\VendorMedia;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class   HomeController extends Controller
{
    //
    public function vendor_site($vendor_link): JsonResponse
    {
        try {
            $validatedVendorLink = htmlspecialchars($vendor_link, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (!Item::where('business_link', $validatedVendorLink)->exists()) {
                return error('Site not found', null, Response::HTTP_NOT_FOUND);
            }

            $data = Category::whereHas('items', function($query) use ($validatedVendorLink) {
                $query->where('business_link', $validatedVendorLink);
            })
                ->with(['items' => function($query) use ($validatedVendorLink) {
                    $query->where('business_link', $validatedVendorLink);
                }])
                ->get();

            return success('Vendor site data', $data, Response::HTTP_OK);

        } catch (Exception $exception) {
            Log::error('Error in vendor_site: '.$exception->getMessage(), ['trace' => $exception->getTrace()]);
            return error('An error occurred', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get vendor menu by subdomain
     *
     * @param string $subdomain
     * @return JsonResponse
     */
    public function getVendorBySubdomain(string $subdomain): JsonResponse
    {
        try {
            $validatedSubdomain = htmlspecialchars($subdomain, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Find business link by subdomain with vendor relationship
            $businessLink = BusinessLink::where('subdomain', $validatedSubdomain)
                ->orWhere('business_link', $validatedSubdomain)
                ->with('user') // Use relationship to load user
                ->first();

            if (!$businessLink) {
                return error('Vendor not found', null, Response::HTTP_NOT_FOUND);
            }

            // Get vendor from relationship
            $vendor = $businessLink->user;

            if (!$vendor || $vendor->role !== 'vendor') {
                return error('Vendor not found', null, Response::HTTP_NOT_FOUND);
            }

            // Get vendor media using relationship
            $vendorMedia = $vendor->vendor_media;

            // Get categories with items for this vendor
            $categories = Category::whereHas('items', function($query) use ($businessLink) {
                $query->where('business_link', $businessLink->business_link);
            })
                ->with(['items' => function($query) use ($businessLink) {
                    $query->where('business_link', $businessLink->business_link)
                        ->where('status', true) // Only active items
                        ->with('category'); // Include category in items
                }])
                ->get();
//the above code return this error: [2025-11-12 01:45:20] local.ERROR: Error in getVendorBySubdomain: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'business_link' in 'where clause' (SQL: select * from `categories` where exists (select * from `items` where `categories`.`id` = `items`.`category_id` and `business_link` = drame-four-04 and `status` = 1)) {"trace":[{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Connection.php","line":720,"function":"runQueryCallback","class":"Illuminate\\Database\\Connection","type":"->","args":["select * from `categories` where exists (select * from `items` where `categories`.`id` = `items`.`category_id` and `business_link` = ? and `status` = ?)",["drame-four-04",true],{"Closure":[]}]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Connection.php","line":405,"function":"run","class":"Illuminate\\Database\\Connection","type":"->","args":["select * from `categories` where exists (select * from `items` where `categories`.`id` = `items`.`category_id` and `business_link` = ? and `status` = ?)",["drame-four-04",true],{"Closure":[]}]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php","line":2705,"function":"select","class":"Illuminate\\Database\\Connection","type":"->","args":["select * from `categories` where exists (select * from `items` where `categories`.`id` = `items`.`category_id` and `business_link` = ? and `status` = ?)",["drame-four-04",true],true]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php","line":2694,"function":"runSelect","class":"Illuminate\\Database\\Query\\Builder","type":"->","args":[]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php","line":3230,"function":"{closure:Illuminate\\Database\\Query\\Builder::get():2693}","class":"Illuminate\\Database\\Query\\Builder","type":"->","args":[]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php","line":2693,"function":"onceWithColumns","class":"Illuminate\\Database\\Query\\Builder","type":"->","args":[["*"],{"Closure":[]}]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php","line":710,"function":"get","class":"Illuminate\\Database\\Query\\Builder","type":"->","args":[["*"]]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php","line":694,"function":"getModels","class":"Illuminate\\Database\\Eloquent\\Builder","type":"->","args":[["*"]]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/app/Http/Controllers/HomeController.php","line":84,"function":"get","class":"Illuminate\\Database\\Eloquent\\Builder","type":"->","args":[]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Routing/Controller.php","line":54,"function":"getVendorBySubdomain","class":"App\\Http\\Controllers\\HomeController","type":"->","args":["drame-four-04"]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php","line":43,"function":"callAction","class":"Illuminate\\Routing\\Controller","type":"->","args":["getVendorBySubdomain",{"subdomain":"drame-four-04"}]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Routing/Route.php","line":259,"function":"dispatch","class":"Illuminate\\Routing\\ControllerDispatcher","type":"->","args":[{"Illuminate\\Routing\\Route":{"uri":"api/subdomain/{subdomain}","methods":["GET","HEAD"],"action":{"middleware":["api","api"],"uses":"App\\Http\\Controllers\\HomeController@getVendorBySubdomain","controller":"App\\Http\\Controllers\\HomeController@getVendorBySubdomain","namespace":null,"prefix":"api","where":[]},"isFallback":false,"controller":[],"defaults":[],"wheres":[],"parameters":{"subdomain":"drame-four-04"},"parameterNames":["subdomain"],"computedMiddleware":["api"],"compiled":[]}},{"App\\Http\\Controllers\\HomeController":[]},"getVendorBySubdomain"]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Routing/Route.php","line":205,"function":"runController","class":"Illuminate\\Routing\\Route","type":"->","args":[]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Routing/Router.php","line":798,"function":"run","class":"Illuminate\\Routing\\Route","type":"->","args":[]},{"file":"/Users/dramesuccess/Documents/Laravel/qr-app/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php","line":141,"function":"{closure:Illuminate\\Routing\\Router::runRouteWithinStack():797}","class":"Illuminate\\Routing\\Router","type":"->","args":[{"Illuminate\\Http\\Request":"GET /api/subdomain/drame-four-04 HTTP/2.0

            // Flatten items from all categories
            $allItems = [];
            foreach ($categories as $category) {
                foreach ($category->items as $item) {
                    $allItems[] = $item;
                }
            }

            // Build response
            $response = [
                'business_name' => $businessLink->business_name,
                'business_links' => [
                    [
                        'business_name' => $businessLink->business_name,
                        'business_link' => $businessLink->business_link,
                        'subdomain' => $businessLink->subdomain,
                        'business_type' => $businessLink->business_type,
                        'phone_number' => $businessLink->phone_number,
                        'business_address' => $businessLink->business_address,
                        'business_qr' => $businessLink->business_qr,
                        'items' => $allItems
                    ]
                ],
                'vendor_media' => $vendorMedia ? [
                    'logo' => $vendorMedia->logo,
                    'hero_image' => $vendorMedia->hero
                ] : null,
                'categories' => $categories,
                'vendor_info' => [
                    'first_name' => $vendor->first_name,
                    'last_name' => $vendor->last_name,
                    'email' => $vendor->email
                ]
            ];

            return success('Vendor menu data fetched successfully', $response, Response::HTTP_OK);

        } catch (Exception $exception) {
            Log::error('Error in getVendorBySubdomain: '.$exception->getMessage(), [
                'trace' => $exception->getTrace(),
                'subdomain' => $subdomain
            ]);
            return error('An error occurred while fetching vendor data', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
