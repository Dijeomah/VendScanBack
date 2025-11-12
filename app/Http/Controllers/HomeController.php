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

            // Find business link by subdomain
            $businessLink = BusinessLink::where('subdomain', $validatedSubdomain)
                ->orWhere('business_link', $validatedSubdomain)
                ->first();

            if (!$businessLink) {
                return error('Vendor not found', null, Response::HTTP_NOT_FOUND);
            }

            // Get vendor user data
            $vendor = User::where('id', $businessLink->uid)
                ->where('role', 'vendor')
                ->first();

            if (!$vendor) {
                return error('Vendor not found', null, Response::HTTP_NOT_FOUND);
            }

            // Get vendor media
            $vendorMedia = VendorMedia::where('uid', $vendor->id)->first();

            // Get categories with items for this vendor
            $categories = Category::whereHas('items', function($query) use ($businessLink) {
                $query->where('business_link', $businessLink->business_link);
            })
                ->with(['items' => function($query) use ($businessLink) {
                    $query->where('business_link', $businessLink->business_link)
                        ->where('status', true); // Only active items
                }])
                ->get();

            // Flatten items from all categories
            $allItems = [];
            foreach ($categories as $category) {
                foreach ($category->items as $item) {
                    $item->category = $category; // Add category info to item
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
                    'hero_image' => $vendorMedia->hero_image
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
