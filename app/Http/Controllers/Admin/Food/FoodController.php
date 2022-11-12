<?php

namespace App\Http\Controllers\Admin\Food;

use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * View aall categories.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function food(){
        $food = Food::latest()->paginate();
        return success('Food: ', $food, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addFood(Request $request){
        $this->validate($request, config('validation.admin_add_food'));

        $user_business_link = $request->business_link;
//        $categoryCheck = Category::where('category_name',$request->category_name )->exists();
        $food = new Food();
        $food->uid   =   $request->id;
        $food->userid   =   $request->userid;
        $food->business_link   =   $user_business_link->business_link;
        $food->title = $request->title;
        $food->category_id = $request->category_id;
        $food->description = $request->description;
        $food->price = $request->price;
        $food->status = true;
        $food->save();
        return success('Food Created Successfully. ',$food, 200);

    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function showFood(Request $request, $id){
        $food = Food::find($id);
        return success('Food information: ', $food, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editFood(Request $request, $id){
        $food = Food::find($id);
        return success('Food information: ', $food, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateFood(Request $request, $id){
        $food = Food::find($id);
//        $food->uid   =   authUser()->id;
//        if (authUser()->role=='admin'){
//            $food->userid   =   'ADMIN001';
//        }
//        if (authUser()->role=='vendor'){
//            $food->userid   =   authUser()->userid;
//        }
        $food->title = $request->title;
        $food->category_id = $request->category_id;
        $food->description = $request->description;
        $food->price = $request->price;
        $food->status = true;
        $food->update();
        return success('Food information updated.', $food, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteFood($id){
        $food = Food::find($id);
        $food->delete();
        return success('Food information deleted.', $food, 200);
    }
}
