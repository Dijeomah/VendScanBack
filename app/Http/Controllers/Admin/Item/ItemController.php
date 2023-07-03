<?php

namespace App\Http\Controllers\Admin\Item;

use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    /**
     * View aall categories.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function food()
    {
        $food = Item::latest()->paginate();
        return success('Item: ', $food, Response::HTTP_OK);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function addFood(Request $request)
    {
        $validated_data = $this->validate($request, config('validation.admin_add_food'));

        $user_business_link = $validated_data ['business_link'];
//        $categoryCheck = Category::where('category_name',$validated_data ['category_name'] )->exists();
        $food = new Item();
        $food->uid = $validated_data ['id'];
        $food->userid = $validated_data ['userid'];
        $food->business_link = $user_business_link->business_link;
        $food->title = $validated_data ['title'];
        $food->category_id = $validated_data ['category_id'];
        $food->description = $validated_data ['description'];
        $food->price = $validated_data ['price'];
        $food->status = true;
        $food->save();
        return success('Item Created Successfully. ', $food, Response::HTTP_OK);

    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function showFood(Request $request, $id)
    {
        $food = Item::find($id);
        return success('Item information: ', $food, Response::HTTP_OK);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function editFood(Request $request, $id)
    {
        $food = Item::find($id);
        return success('Item information: ', $food, Response::HTTP_OK);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateFood(Request $request, $id)
    {
        $food = Item::find($id);
        $food->title = $request->title;
        $food->category_id = $request->category_id;
        $food->description = $request->description;
        $food->price = $request->price;
        $food->status = true;
        $food->update();
        return success('Item information updated.', $food, Response::HTTP_CREATED);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function deleteFood($id)
    {
        $food = Item::find($id);
        $food->delete();
        return success('Item information deleted.', $food, Response::HTTP_OK);
    }
}
