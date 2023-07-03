<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class   HomeController extends Controller
{
    //
    public function vendor_site($vendor_link): JsonResponse
    {
        try {
            $check = Item::where('business_link', $vendor_link)->exists();

            if ($check) {
                $data = Category::whereIn("id", Item::where('business_link', $vendor_link)->distinct()->get(["category_id"]))->with("item")->get()->toArray();
                return success('Vendor site data: ', $data, Response::HTTP_OK);
            }
        } catch (Exception $exception) {
            return error('Site not found', null, Response::HTTP_NO_CONTENT);
        }
        return error('Data|Site not found', null, Response::HTTP_NO_CONTENT);
    }
}
