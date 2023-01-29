<?php

    namespace App\Http\Controllers;

    use App\Models\Category;
    use App\Models\Food;

    class   HomeController extends Controller
    {
        //
        public function vendor_site($vendor_link)
        {
            try {
                $check = Food::where('business_link', $vendor_link)->exists();
                if ($check) {
                    $data = Category::whereIn("id", Food::where('business_link', $vendor_link)->distinct()->get(["category_id"]))
                        ->with("food")->get()->toArray();
                    return success('Vendor site data: ', $data, 200);
                } else {
                    return error('Site not found', null, 404);
                }
            } catch (\Exception $exception) {
                return error('Site not found', null, 404);
            }
        }
    }
