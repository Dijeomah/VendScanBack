<?php

    namespace App\Http\Controllers;

    use App\Models\Category;
    use App\Models\Food;
    use Illuminate\Http\JsonResponse;

    class   HomeController extends Controller
    {
        //
        public function vendor_site($vendor_link): JsonResponse
        {
            try {
                $check = Food::where('business_link', $vendor_link)->exists();

                if ($check) {
                    $data = Category::whereIn("id", Food::where('business_link', $vendor_link)->distinct()->get(["category_id"]))
                        ->with("food")->get()->toArray();
                    return success('Vendor site data: ', $data, 200);
                }
            } catch (\Exception $exception) {
                return error('Site not found', null, 404);
            }
            return error('Site not found', null, 404);
        }
    }
