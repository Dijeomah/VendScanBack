<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemCreateRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\BusinessLink;
use App\Models\Category;
use App\Models\Item;
use App\Services\CloudinaryStorage;
use App\Traits\ChecksSubscriptionLimits;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    use ChecksSubscriptionLimits;
    /**
     * Get all items for vendor with advanced filtering, sorting, and pagination
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = authUser();
            $query = Item::where('user_id', $user->id)
                ->with(['category', 'subCategory', 'businessLink']);

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by subcategory
            if ($request->filled('sub_category_id')) {
                $query->where('sub_category_id', $request->sub_category_id);
            }

            // Filter by business
            if ($request->filled('business_link_id')) {
                $query->where('business_link_id', $request->business_link_id);
            }

            // Filter by business link string
            if ($request->filled('business_link')) {
                $query->where('business_link', $request->business_link);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status === 'true' || $request->status === '1');
            }

            // Filter by price range
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['title', 'price', 'created_at', 'updated_at', 'status'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 15);
            $items = $query->paginate($perPage);

            return success('Items fetched successfully', $items, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching vendor items: ' . $e->getMessage());
            return error('Error fetching items', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get item statistics for vendor
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $user = authUser();

            $stats = [
                'total_items' => Item::where('user_id', $user->id)->count(),
                'active_items' => Item::where('user_id', $user->id)->where('status', true)->count(),
                'inactive_items' => Item::where('user_id', $user->id)->where('status', false)->count(),
                'average_price' => round(Item::where('user_id', $user->id)->avg('price'), 2),
                'highest_price' => Item::where('user_id', $user->id)->max('price'),
                'lowest_price' => Item::where('user_id', $user->id)->min('price'),
                'items_by_category' => Category::where('user_id', $user->id)
                    ->withCount(['items' => function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }])
                    ->orderBy('items_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'category_name', 'items_count']),
                'recent_items' => Item::where('user_id', $user->id)
                    ->with(['category'])
                    ->latest()
                    ->limit(5)
                    ->get(['id', 'title', 'price', 'category_id', 'status', 'created_at']),
            ];

            return success('Item statistics fetched successfully', $stats, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Error fetching item statistics: ' . $e->getMessage());
            return error('Error fetching statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            // Check subscription limits for items
            if ($error = $this->checkLimit('items')) {
                return $error;
            }

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
        // Check subscription limits for items
        if ($error = $this->checkLimit('items')) {
            return $error;
        }

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
