<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\BusinessLink;
use App\Models\Category;
use App\Models\Item;
use App\Services\CloudinaryStorage;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
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
     * Store a newly created item (supports image upload)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'business_link' => 'required|string',
                'status' => 'nullable|boolean',
                'image' => 'nullable|image|max:5120' // 5MB max
            ]);

            Log::alert('validated data', [$validated]);
            $business_link = BusinessLink::where(['userid' => authUser()->userid, 'business_link' => $validated['business_link']])->first();

            // Verify business belongs to user
//            if (!BusinessLink::where(['userid' => authUser()->userid, 'business_link' => $validated['business_link']])->exists()) {
            if (!$business_link) {
                return error('Business not found or access denied', [], Response::HTTP_FORBIDDEN);
            }

            $imageUrl = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageUrl = CloudinaryStorage::upload(
                    $image->getRealPath(),
                    $image->getClientOriginalName()
                );
            }

            $item = Item::create([
                'user_id' => authUser()->id,
                'userid' => authUser()->userid,
                'business_link' => $validated['business_link'],
                'business_link_id' => $business_link->id,
                'title' => $validated['title'],
                'category_id' => $validated['category_id'],
                'sub_category_id' => $validated['sub_category_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'status' => $validated['status'] ?? true,
                'image' => $imageUrl
            ]);

            return success('Item created successfully', $item, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            Log::error('Item creation error: ' . $e->getMessage());
            return error('Failed to create item', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
     * Update an existing item (supports image upload)
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $item = Item::where('id', $id)
                ->where('userid', authUser()->userid)
                ->firstOrFail();

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'category_id' => 'sometimes|required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'status' => 'nullable|boolean',
                'image' => 'nullable|image|max:5120' // 5MB max
            ]);

            // Handle image upload if present
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $validated['image'] = CloudinaryStorage::upload(
                    $image->getRealPath(),
                    $image->getClientOriginalName()
                );
            }

            $item->update($validated);

            return success('Item updated successfully', $item->fresh(), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return error('Item not found or access denied', null, Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            Log::error('Item update error: ' . $e->getMessage());
            return error('Failed to update item', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the Authenticated User profile (legacy method).
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
                'sub_category_id' => $validated['sub_category_id'] ?? null,
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
