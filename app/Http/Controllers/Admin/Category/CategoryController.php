<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryCreateRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Get all categories with advanced filtering, sorting, and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::with(['subcategories', 'items'])
                ->withCount(['subcategories', 'items']);

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('category_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('category_code', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Filter by user/vendor
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            // Filter by has items
            if ($request->filled('has_items')) {
                if ($request->has_items === 'true' || $request->has_items === true) {
                    $query->has('items');
                } else {
                    $query->doesntHave('items');
                }
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['category_name', 'created_at', 'updated_at', 'items_count', 'subcategories_count'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 15);
            $categories = $query->paginate($perPage);

            return success('Categories fetched successfully', $categories, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return error('Error fetching categories', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get category statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = [
                'total_categories' => Category::count(),
                'total_subcategories' => SubCategory::count(),
                'categories_with_items' => Category::has('items')->count(),
                'empty_categories' => Category::doesntHave('items')->count(),
                'total_items' => DB::table('items')->count(),
                'top_categories' => Category::withCount('items')
                    ->orderBy('items_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'category_name', 'items_count']),
            ];

            return success('Category statistics fetched successfully', $stats, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching category statistics: ' . $e->getMessage());
            return error('Error fetching statistics', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new category
     */
    public function addCategory(CategoryCreateRequest $categoryCreateRequest): JsonResponse
    {
        try {
            $categoryCheck = Category::where('category_name', $categoryCreateRequest['category_name'])
                ->where('user_id', authUser()->id)
                ->exists();

            if (!$categoryCheck) {
                $categoryData = new Category();
                $categoryData->user_id = authUser()->id;
                $categoryData->category_name = $categoryCreateRequest['category_name'];
                $categoryData->category_code = Str::slug(authUser()->id . '-' . $categoryCreateRequest['category_name']);
                $categoryData->save();

                return success('Category created successfully', $categoryData, Response::HTTP_CREATED);
            }

            return error('Category already exists', null, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return error('Failed to create category', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single category with details
     */
    public function showCategory(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::with(['subcategories.items', 'items'])
                ->withCount(['subcategories', 'items'])
                ->findOrFail($id);

            return success('Category fetched successfully', $category, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Category not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get category for editing
     */
    public function edit(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            return success('Category fetched successfully', $category, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Category not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $category = Category::findOrFail($id);
            $category->category_name = $request->category_name;
            $category->category_code = Str::slug($category->user_id . '-' . $request->category_name);
            $category->save();

            return success('Category updated successfully', $category, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return error('Failed to update category', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete category
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // Check if category has items
            if ($category->items()->count() > 0) {
                return error('Cannot delete category with items. Please delete or reassign items first.', null, Response::HTTP_BAD_REQUEST);
            }

            // Delete subcategories first
            $category->subcategories()->delete();

            $category->delete();

            return success('Category deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            return error('Failed to delete category', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get categories with items (for public menu)
     */
    public function categoriesWithItems(Request $request): JsonResponse
    {
        try {
            $query = Category::with(['subcategories', 'items' => function($query) {
                $query->where('status', true);
            }])
                ->whereHas('items');

            $perPage = $request->get('per_page', 10);
            $categories = $query->paginate($perPage);

            return success('Categories with items fetched successfully', $categories, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching categories with items: ' . $e->getMessage());
            return error('Error fetching categories', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Add item to category
     */
    public function addItemToCategory(Request $request, $categoryId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string'
            ]);

            $category = Category::findOrFail($categoryId);

            $item = $category->items()->create([
                'title' => $validated['title'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                'user_id' => auth()->id(),
                'status' => true
            ]);

            return success('Item added to category successfully', $item, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error adding item to category: ' . $e->getMessage());
            return error('Failed to add item to category', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ============ SUBCATEGORY MANAGEMENT ============

    /**
     * Get all subcategories with filtering
     */
    public function getSubcategories(Request $request): JsonResponse
    {
        try {
            $query = SubCategory::with(['category', 'items'])
                ->withCount('items');

            // Search
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where('sub_category_name', 'LIKE', "%{$searchTerm}%");
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by user/vendor
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by has items
            if ($request->filled('has_items')) {
                if ($request->has_items === 'true' || $request->has_items === true) {
                    $query->has('items');
                } else {
                    $query->doesntHave('items');
                }
            }

            // Sorting
            $sortField = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['sub_category_name', 'created_at', 'updated_at', 'items_count'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 15);
            $subcategories = $query->paginate($perPage);

            return success('Subcategories fetched successfully', $subcategories, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error fetching subcategories: ' . $e->getMessage());
            return error('Error fetching subcategories', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single subcategory
     */
    public function showSubcategory($id): JsonResponse
    {
        try {
            $subcategory = SubCategory::with(['category', 'items'])
                ->withCount('items')
                ->findOrFail($id);

            return success('Subcategory fetched successfully', $subcategory, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Subcategory not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create subcategory
     */
    public function createSubcategory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id',
                'sub_category_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $subcategory = SubCategory::create([
                'user_id' => authUser()->id,
                'category_id' => $request->category_id,
                'sub_category_name' => $request->sub_category_name,
            ]);

            return success('Subcategory created successfully', $subcategory, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating subcategory: ' . $e->getMessage());
            return error('Failed to create subcategory', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update subcategory
     */
    public function updateSubcategory(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'sub_category_name' => 'required|string|max:255',
                'category_id' => 'sometimes|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $subcategory = SubCategory::findOrFail($id);
            $subcategory->sub_category_name = $request->sub_category_name;

            if ($request->filled('category_id')) {
                $subcategory->category_id = $request->category_id;
            }

            $subcategory->save();

            return success('Subcategory updated successfully', $subcategory, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error updating subcategory: ' . $e->getMessage());
            return error('Failed to update subcategory', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete subcategory
     */
    public function deleteSubcategory($id): JsonResponse
    {
        try {
            $subcategory = SubCategory::findOrFail($id);

            // Check if subcategory has items
            if ($subcategory->items()->count() > 0) {
                return error('Cannot delete subcategory with items. Please delete or reassign items first.', null, Response::HTTP_BAD_REQUEST);
            }

            $subcategory->delete();

            return success('Subcategory deleted successfully', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error deleting subcategory: ' . $e->getMessage());
            return error('Failed to delete subcategory', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
