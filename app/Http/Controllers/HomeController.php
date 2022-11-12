<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;

class   HomeController extends Controller
{
    //
    public function vendor_site($vendor_link){
        $data = Category::whereIn("id",Food::where('business_link', $vendor_link)->distinct()->get(["category_id"]))->with("food")->get()->toArray();
        return success('Vendor site data: ', $data, 200);
    }
}
