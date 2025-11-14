<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryCreateRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Exception;
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
     * Get all categories for authenticated vendor with filtering and sorting
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = authUser();
            $query = Category::where('user_id', $user->id)
                ->with(['subcategories', 'items'])
                ->withCount(['subcategories', 'items']);

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('category_name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('category_code', 'LIKE', "%{$searchTerm}%");
                });
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
     * Get category statistics for vendor
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $user = authUser();

            $stats = [
                'total_categories' => Category::where('user_id', $user->id)->count(),
                'total_subcategories' => SubCategory::where('user_id', $user->id)->count(),
                'categories_with_items' => Category::where('user_id', $user->id)->has('items')->count(),
                'empty_categories' => Category::where('user_id', $user->id)->doesntHave('items')->count(),
                'total_items' => DB::table('items')->where('user_id', $user->id)->count(),
                'top_categories' => Category::where('user_id', $user->id)
                    ->withCount('items')
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
     * Store a newly created category
     */
    public function store(CategoryCreateRequest $categoryCreateRequest): JsonResponse
    {
        try {
            $user = authUser();

            // Check if category already exists for this user
            if (Category::where('category_name', $categoryCreateRequest['category_name'])
                ->where('user_id', $user->id)
                ->exists()) {
                return error('Category already exists', null, Response::HTTP_BAD_REQUEST);
            }

            $category = new Category();
            $category->user_id = $user->id;
            $category->category_name = $categoryCreateRequest['category_name'];
            $category->category_code = Str::slug($user->id . '-' . $categoryCreateRequest['category_name']);
            $category->save();

            return success('Category created successfully', $category, Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            Log::error('Error creating category: ' . $exception->getMessage());
            return error('Category creation failed', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified category
     */
    public function show(Category $category): JsonResponse
    {
        try {
            // Ensure user owns this category
            if ($category->user_id !== authUser()->id) {
                return error('Unauthorized', null, Response::HTTP_FORBIDDEN);
            }

            $category->load(['subcategories.items', 'items'])
                ->loadCount(['subcategories', 'items']);

            return success('Category fetched successfully', $category, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Category not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category): JsonResponse
    {
        try {
            // Ensure user owns this category
            if ($category->user_id !== authUser()->id) {
                return error('Unauthorized', null, Response::HTTP_FORBIDDEN);
            }

            return success('Category fetched successfully', $category, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Category not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        try {
            // Ensure user owns this category
            if ($category->user_id !== authUser()->id) {
                return error('Unauthorized', null, Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return error('Validation failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

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
     * Remove the specified category
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            // Ensure user owns this category
            if ($category->user_id !== authUser()->id) {
                return error('Unauthorized', null, Response::HTTP_FORBIDDEN);
            }

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

    // ============ SUBCATEGORY MANAGEMENT ============

    /**
     * Get all subcategories for vendor with filtering
     */
    public function viewSubCategories(Request $request): JsonResponse
    {
        try {
            $user = authUser();
            $query = SubCategory::where('user_id', $user->id)
                ->with(['category', 'items'])
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
            $subCategories = $query->paginate($perPage);

            return success('Subcategories fetched successfully', $subCategories, Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Error fetching subcategories: ' . $exception->getMessage());
            return error('Error fetching subcategories', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get single subcategory
     */
    public function showSubcategory($id): JsonResponse
    {
        try {
            $subcategory = SubCategory::where('user_id', authUser()->id)
                ->with(['category', 'items'])
                ->withCount('items')
                ->findOrFail($id);

            return success('Subcategory fetched successfully', $subcategory, Response::HTTP_OK);
        } catch (\Exception $e) {
            return error('Subcategory not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new subcategory
     */
    public function createSubCategory(Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'category_id' => 'required|exists:categories,id',
                'sub_category_name' => 'required|string|max:255'
            ]);

            $user = authUser();

            // Verify category belongs to user
            $category = Category::where('id', $validated['category_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$category) {
                return error('Category not found or unauthorized', null, Response::HTTP_FORBIDDEN);
            }

            $data = SubCategory::create([
                'user_id' => $user->id,
                'category_id' => $validated['category_id'],
                'sub_category_name' => $validated['sub_category_name'],
            ]);

            return success('Subcategory created successfully', $data, Response::HTTP_CREATED);
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

            $subcategory = SubCategory::where('user_id', authUser()->id)
                ->findOrFail($id);

            $subcategory->sub_category_name = $request->sub_category_name;

            if ($request->filled('category_id')) {
                // Verify new category belongs to user
                $category = Category::where('id', $request->category_id)
                    ->where('user_id', authUser()->id)
                    ->first();

                if (!$category) {
                    return error('Category not found or unauthorized', null, Response::HTTP_FORBIDDEN);
                }

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
            $subcategory = SubCategory::where('user_id', authUser()->id)
                ->findOrFail($id);

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
