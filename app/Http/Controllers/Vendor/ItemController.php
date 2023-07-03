<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\BusinessLink;
use App\Models\Category;
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
    public function item()
    {
        $item = Item::where('userid', authUser()->userid)->get();
        return success('Item: ', $item, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function addItem(ItemCreateRequest $itemCreateRequest): JsonResponse
    {

        if (BusinessLink::where(['userid' => authUser()->userid, 'business_link' => $itemCreateRequest['business_link']])->exists()) {
            $item = new Item();
            $item->uid = authUser()->id;
            $item->userid = authUser()->userid;
            $item->business_link = $itemCreateRequest['business_link'];
            $item->title = $itemCreateRequest['title'];
            $item->category_id = $itemCreateRequest['category_id'];
            $item->sub_category_id = $itemCreateRequest['sub_category_id'];
            $item->description = $itemCreateRequest['description'];
            $item->price = $itemCreateRequest['price'];
            $item->status = true;
            $item->save();
            return success('Item Created Successfully. ', $item, 200);
        }
        return error('Failed to create item. ', [], Response::HTTP_BAD_REQUEST);

    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function showItem($id): JsonResponse
    {
        $item = Item::find($id);
        return success('Item information: ', $item, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function editItem($id): JsonResponse
    {
        $item = Item::find($id);
        return success('Item information: ', $item, 200);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateItem(ItemUpdateRequest $itemUpdateRequest, $id): JsonResponse
    {
        $validated_data = $itemUpdateRequest->validated();

        $item = Item::find($id);
        $item->uid = authUser()->id;
        if (authUser()->role == 'admin') {
            $item->userid = 'ADMIN001';
        }
        if (authUser()->role == 'vendor') {
            $item->userid = authUser()->userid;
        }
        $item->category_id = $validated_data['category_id'];
        $item->title = $validated_data['title'];
        $item->description = $validated_data['description'];
        $item->price = $validated_data['price'];
        $item->status = true;
        $item->save();
        return success('Item information updated.', $item, Response::HTTP_OK);
    }

    /**
     * Update the Authenticated User profile.
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function deleteItem($id)
    {
        $item = Item::find($id);
        $item->delete();
        return success('Item information deleted.', $item, Response::HTTP_OK);
    }


}
