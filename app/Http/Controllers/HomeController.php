<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
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

}
