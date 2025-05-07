<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\BusinessLink;
use App\Models\Category;
use App\Models\Item;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function index()
    {
        // dd('Hello');
        $item = Item::where('userid', authUser()->userid)->paginate(10);
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
    public function updateItem(ItemUpdateRequest $request, $id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);
            $validated = $request->validated();

            $item->update([
                'uid' => authUser()->id,
                'userid' => authUser()->role === 'admin' ? 'ADMIN001' : authUser()->userid,
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'status' => true
            ]);

            return success('Item updated', $item, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return error('Item not found', null, Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return error('Update failed', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
