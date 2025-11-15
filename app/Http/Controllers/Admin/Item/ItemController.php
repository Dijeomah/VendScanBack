<?php

namespace App\Http\Controllers\Admin\Item;

use App\Http\Controllers\Controller;
use App\Models\BusinessLink;
use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Get all items with advanced filtering, sorting, and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Item::with(['category', 'subCategory', 'businessLink', 'user'])
                ->select('items.*');

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
            if ($request->filled('business_id')) {
                $query->where('business_link_id', $request->business_id);
            }

            // Filter by vendor/user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
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
        } catch (\Exception $e) {
            Log::error('Error fetching items: ' . $e->getMessage());
            return error('Error fetching items', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get item statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = [
                'total_items' => Item::count(),
                'active_items' => Item::where('status', true)->count(),
                'inactive_items' => Item::where('status', false)->count(),
                'total_categories' => Category::count(),
                'total_subcategories' => SubCategory::count(),
                'average_price' => round(Item::avg('price'), 2),
                'highest_price' => Item::max('price'),
                'lowest_price' => Item::min('price'),
                'items_by_category' => Category::withCount('items')
                    ->orderBy('items_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'category_name', 'items_count']),
                'recent_items' => Item::with(['category', 'user'])
                    ->latest()
                    ->limit(5)
                    ->get(['id', 'title', 'price', 'category_id', 'user_id', 'created_at']),
            ];

            return success('Item statistics fetched successfully', $stats, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching item statistics: ' . $e->getMessage());
            return error('Error fetching statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all items (legacy method)
     */
    public function food(Request $request): JsonResponse
    {
        return $this->index($request);
    }

    /**
     * Add new item
     */
    public function addFood(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'business_link_id' => 'required|exists:business_links,id',
                'title' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $item = Item::create([
                'user_id' => $request->user_id,
                'business_link_id' => $request->business_link_id,
                'title' => $request->title,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'description' => $request->description,
                'price' => $request->price,
                'status' => $request->get('status', true),
            ]);

            return success('Item created successfully', $item->load(['category', 'subCategory']), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating item: ' . $e->getMessage());
            return error('Failed to create item', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single item
     */
    public function showFood(Request $request, $id): JsonResponse
    {
        try {
            $item = Item::with(['category', 'subCategory', 'businessLink', 'user'])
                ->findOrFail($id);

            return success('Item fetched successfully', $item, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Item not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get item for editing
     */
    public function editFood(Request $request, $id): JsonResponse
    {
        return $this->showFood($request, $id);
    }

    /**
     * Update item
     */
    public function updateFood(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'category_id' => 'sometimes|required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $item = Item::findOrFail($id);

            $item->update($request->only([
                'title',
                'category_id',
                'sub_category_id',
                'description',
                'price',
                'status'
            ]));

            return success('Item updated successfully', $item->load(['category', 'subCategory']), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating item: ' . $e->getMessage());
            return error('Failed to update item', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete item
     */
    public function deleteFood($id): JsonResponse
    {
        try {
            $item = Item::findOrFail($id);
            $item->delete();

            return success('Item deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting item: ' . $e->getMessage());
            return error('Failed to delete item', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk update item status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_ids' => 'required|array',
                'item_ids.*' => 'exists:items,id',
                'status' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $updated = Item::whereIn('id', $request->item_ids)
                ->update(['status' => $request->status]);

            return success("Successfully updated {$updated} items", [
                'updated_count' => $updated,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error bulk updating items: ' . $e->getMessage());
            return error('Failed to update items', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk delete items
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_ids' => 'required|array',
                'item_ids.*' => 'exists:items,id',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $deleted = Item::whereIn('id', $request->item_ids)->delete();

            return success("Successfully deleted {$deleted} items", [
                'deleted_count' => $deleted,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting items: ' . $e->getMessage());
            return error('Failed to delete items', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
